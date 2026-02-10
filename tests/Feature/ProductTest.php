<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed categories as they are required for product creation
        $this->seed(\Database\Seeders\CategorySeeder::class);
    }

    public function test_can_list_products_paginated(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $category = Category::first();
        Product::factory()->count(15)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
            'data',
            'meta' => ['current_page', 'last_page', 'total']
        ])
            ->assertJsonCount(10, 'data'); // Default pagination is 10
    }

    public function test_can_filter_products_by_category(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $category1 = Category::first();
        $category2 = Category::skip(1)->first();

        Product::factory()->create(['category_id' => $category1->id]);
        Product::factory()->create(['category_id' => $category2->id]);

        $response = $this->getJson('/api/products?category_id=' . $category1->id);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.category.id', $category1->id);
    }

    public function test_can_create_product(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $category = Category::first();

        $data = [
            'name' => 'New Product',
            'category_id' => $category->id,
            'description' => 'Description here',
            'price' => 99.99,
            'stock' => 10,
            'enabled' => true,
        ];

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'New Product');

        $this->assertDatabaseHas('products', ['name' => 'New Product']);
    }

    public function test_can_update_product(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $category = Category::first();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $data = ['name' => 'Updated Name'];

        $response = $this->putJson("/api/products/{$product->id}", $data);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Name');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Name']);
    }

    public function test_can_soft_delete_product(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $category = Category::first();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_can_bulk_delete_products(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $category = Category::first();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);
        $ids = $products->pluck('id')->toArray();

        $response = $this->postJson('/api/products/bulk-delete', ['ids' => $ids]);

        $response->assertStatus(200);
        foreach ($ids as $id) {
            $this->assertSoftDeleted('products', ['id' => $id]);
        }
    }

    public function test_can_export_products(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->get('/api/products/export');

        $response->assertStatus(200);
    // Additional assertions for file download can be added if needed
    }
    public function test_validation_prevents_invalid_product(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/products', [
            'name' => '', // Invalid: required
            'price' => 'abc', // Invalid: must be numeric
            'category_id' => 999, // Invalid: must exist
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price', 'category_id']);
    }
}