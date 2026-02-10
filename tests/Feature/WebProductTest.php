<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_access_product_index()
    {
        $response = $this->get('/products');
        $response->assertStatus(200);
    }

    public function test_can_bulk_delete_products_via_web()
    {
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);
        $ids = $products->pluck('id')->toArray();

        $response = $this->post('/products/bulk-delete', [
            'ids' => $ids
        ]);

        $response->assertRedirect('/products');
        $this->assertSoftDeleted('products', ['id' => $ids[0]]);
        $this->assertSoftDeleted('products', ['id' => $ids[1]]);
        $this->assertSoftDeleted('products', ['id' => $ids[2]]);
    }
}