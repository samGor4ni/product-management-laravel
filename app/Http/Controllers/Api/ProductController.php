<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;
use OpenApi\Attributes as OA;

#[OA\Info(title: "Product Management API", version: "1.0.0")]
#[OA\Server(url: "http://localhost:8080")]
#[OA\SecurityScheme(securityScheme: "bearerAuth", type: "http", scheme: "bearer", bearerFormat: "JWT")]
class ProductController extends Controller
{
    #[OA\Get(
        path: "/api/products",
        operationId: "getProductsList",
        summary: "Get list of products",
        tags: ["Products"],
        parameters: [
            new OA\Parameter(name: "category_id", in: "query", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "category", in: "query", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "enabled", in: "query", schema: new OA\Schema(type: "boolean"))
        ],
        responses: [
            new OA\Response(response: 200, description: "OK"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ],
        security: [
            ['bearerAuth' => []]
        ]
    )]
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        } elseif ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->category . '%');
            });
        }
        if ($request->filled('enabled')) {
            $query->where('enabled', $request->boolean('enabled'));
        }
        return ProductResource::collection($query->paginate(10)->withQueryString());
    }

    #[OA\Post(
        path: "/api/products",
        operationId: "storeProduct",
        summary: "Store new product",
        tags: ["Products"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "category_id", "price", "stock"],
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "category_id", type: "integer"),
                    new OA\Property(property: "description", type: "string"),
                    new OA\Property(property: "price", type: "number"),
                    new OA\Property(property: "stock", type: "integer"),
                    new OA\Property(property: "enabled", type: "boolean")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Created"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation Error")
        ],
        security: [
            ['bearerAuth' => []]
        ]
    )]
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());
        return new ProductResource($product);
    }

    #[OA\Get(
        path: "/api/products/{product}",
        operationId: "getProductById",
        summary: "Get product info",
        tags: ["Products"],
        parameters: [
            new OA\Parameter(name: "product", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "OK"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Not Found")
        ],
        security: [
            ['bearerAuth' => []]
        ]
    )]
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    #[OA\Put(
        path: "/api/products/{product}",
        operationId: "updateProduct",
        summary: "Update product",
        tags: ["Products"],
        parameters: [
            new OA\Parameter(name: "product", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "category_id", type: "integer"),
                    new OA\Property(property: "description", type: "string"),
                    new OA\Property(property: "price", type: "number"),
                    new OA\Property(property: "stock", type: "integer"),
                    new OA\Property(property: "enabled", type: "boolean")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Updated"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Not Found"),
            new OA\Response(response: 422, description: "Validation Error")
        ],
        security: [
            ['bearerAuth' => []]
        ]
    )]
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        return new ProductResource($product);
    }

    #[OA\Delete(
        path: "/api/products/{product}",
        operationId: "deleteProduct",
        summary: "Delete product",
        tags: ["Products"],
        parameters: [
            new OA\Parameter(name: "product", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Deleted"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Not Found")
        ],
        security: [
            ['bearerAuth' => []]
        ]
    )]
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product soft-deleted']);
    }

    #[OA\Post(
        path: "/api/products/bulk-delete",
        operationId: "bulkDeleteProducts",
        summary: "Bulk delete products",
        tags: ["Products"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["ids"],
                properties: [
                    new OA\Property(property: "ids", type: "array", items: new OA\Items(type: "integer"))
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Deleted"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation Error")
        ],
        security: [
            ['bearerAuth' => []]
        ]
    )]
    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array|exists:products,id']);
        Product::whereIn('id', $request->ids)->delete();
        return response()->json(['message' => 'Products bulk deleted successfully']);
    }

    #[OA\Get(
        path: "/api/products/export",
        operationId: "exportProducts",
        summary: "Export products",
        tags: ["Products"],
        responses: [
            new OA\Response(response: 200, description: "OK"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ],
        security: [
            ['bearerAuth' => []]
        ]
    )]
    public function export()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }
}
