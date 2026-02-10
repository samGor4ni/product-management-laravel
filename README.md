# 🚀 Laravel Product Management System

A premium Laravel-based Product Management system with a robust RESTful API, Admin Dashboard, and automated documentation.

---

## 🛠️ Technology Stack
- **Core**: Laravel (Latest Stable)
- **Database**: MySQL 8.0 (with Soft Deletes)
- **Environment**: Laravel Sail (Docker)
- **API Documentation**: L5-Swagger (OpenAPI 3.1 / PHP 8 Attributes)
- **Excel Support**: Maatwebsite/Excel
- **Authentication**: Laravel Sanctum

---

## 📂 Project Structure

```text
app/
  Exports/
    ProductsExport.php      # Logic for Excel file generation
  Http/
    Controllers/
      Api/
        ProductController.php # Core API logic with Swagger Attributes
      ProductController.php    # Web dashboard controller
    Requests/
      StoreProductRequest.php  # Validation for product creation
      UpdateProductRequest.php # Validation for product updates
    Resources/
      ProductResource.php     # API transformation layer (JSON structure)
  Models/
    Category.php              # Category entity & relationships
    Product.php               # Product entity with SoftDeletes
config/
  l5-swagger.php              # Documentation UI & scanner settings
database/
  factories/                  # Model blueprints for generating test data
    CategoryFactory.php
    ProductFactory.php
  migrations/                 # Database table schemas
    2026_02_09_083252_create_categories_table.php
    2026_02_09_083253_create_products_table.php
  seeders/                    # Sample data for initial setup
    CategorySeeder.php
    ProductSeeder.php
    DatabaseSeeder.php        # Entry point for all seeders
resources/
  views/
    layouts/
      app.blade.php           # Main dashboard layout (Navigation & UI)
    products/                 # CRUD templates for Web Dashboard
      index.blade.php         # Product list & filtering
      create.blade.php        # Add new product form
      edit.blade.php          # Update product form
routes/
  api.php                     # API endpoint definitions
  web.php                     # Web dashboard route definitions
storage/api-docs/
  api-docs.json               # Generated OpenAPI specification
tests/Feature/
  ProductTest.php             # API functionality tests
  WebProductTest.php          # Web dashboard functionality tests
```

---

## 📝 Approach & Implementation
This project was developed with a focus on code quality, modern standards, and developer experience.

### 🧩 Core Strategy
- **Containerized Development**: Used **Laravel Sail** to ensure the environment is reproducible and isolated.
- **Domain-Driven Validation**: All business logic and validation are encapsulated in **Form Requests**, keeping controllers clean and adhering to the Single Responsibility Principle.
- **API-First Design**: The API was designed before the web layer, using **Eloquent Resources** to ensure that data transformation is consistent across all delivery channels.

### 🛠️ Key Implementation Details
- **OpenAPI 3.1 & PHP 8 Attributes**: Instead of legacy annotations, I used native PHP Attributes for Swagger documentation. This future-proofs the documentation and improves static analysis.
- **Relational Filtering**: Implemented a flexible filtering system in `ProductController` that handles both numeric `category_id` and string-based category name searches using `whereHas` relationships.
- **Automated Verification**: Every core feature (CRUD, Bulk Actions, Excel Export) is backed by an automated Feature Test to prevent regressions.

---

## 🏗️ Project Setup Instructions

### 1. Prerequisites
- Docker & Docker Desktop installed.
- PHP and Composer installed locally (only for initial setup, otherwise use Sail).

### 2. Initial Installation
Clone the repository and enter the project directory:

```bash
cp .env.example .env
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

### 3. Environment Variables (.env)
The project is pre-configured to work with Laravel Sail and Swagger using the values in `.env.example`. **Please ensure you copy `.env.example` to `.env` as shown in the installation steps.**

Key values included in `.env.example`:
```env
APP_NAME="Product Management"
APP_URL=http://localhost:8080
APP_PORT=8080

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

FORWARD_DB_PORT=3306

L5_SWAGGER_CONST_HOST=http://localhost:8080
```

### 4. Database Access
You can access the database using any MySQL client with the credentials above:
- **Host**: `127.0.0.1`
- **Port**: `3306`
- **User**: `sail`
- **Pass**: `password`

---

## 📖 API Documentation

The API is fully documented using **Swagger/OpenAPI**.
- **Documentation UI**: [http://localhost:8080/api/documentation](http://localhost:8080/api/documentation)
- **JSON Spec**: [http://localhost:8080/docs/api-docs.json](http://localhost:8080/docs/api-docs.json)

### 🔐 How to Authorize (Swagger UI)
To test protected endpoints directly in the browser:

1.  **Generate a Token**: Run this command in your terminal to get a Bearer token:
    ```bash
    ./vendor/bin/sail artisan tinker --execute="echo App\Models\User::first()->createToken('tester')->plainTextToken;"
    ```
2.  **Authorize in UI**: 
    - Click the **Authorize** button at the top right of the Swagger page.
    - Paste the generated token into the **Value** field.
    - Click **Authorize** then **Close**.
3.  **Test Endpoints**: You can now use the "Try it out" button on any protected route.

### Key Endpoints

| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :---: |
| `GET` | `/api/products` | Paginated list with filtering (category, status) | ✅ |
| `POST` | `/api/products` | Create a new product | ✅ |
| `GET` | `/api/products/{id}` | Detailed product view | ✅ |
| `PUT` | `/api/products/{id}` | Update existing product | ✅ |
| `DELETE` | `/api/products/{id}` | Soft delete a product | ✅ |
| `POST` | `/api/products/bulk-delete` | Delete multiple products by ID | ✅ |
| `GET` | `/api/products/export` | Download products as `.xlsx` file | ✅ |

> **Note**: For authenticated requests, use the `Bearer {token}` header. Tokens can be generated for users created via the seeder.

---

## 🧪 Testing
The project includes a comprehensive test suite covering API CRUD, filtering, and bulk operations.

```bash
./vendor/bin/sail artisan test
```

---

## 💡 Assumptions & Design Choices

1.  **PHP 8 Attributes for Swagger**: 
    The project uses PHP 8 Attributes for OpenAPI documentation. This was chosen to satisfy `swagger-php` v6.x requirements, which provides better performance and native PHP integration compared to PHPDoc annotations.
    
2.  **Service-Oriented Thinking**: 
    While the project follows MVC, the API responses use **Eloquent Resources** (`ProductResource`) to ensure a decoupled and consistent data structure that includes relationship details (Category) as requested.

3.  **Sanctum Authentication**: 
    We chose Laravel Sanctum for API authentication as it provides a lightweight, robust token-based system suitable for SPAs and mobile apps.

4.  **Soft Deletes Implementation**: 
    Standard `SoftDeletes` are used for products to prevent accidental data loss while keeping the database clean for the end-user.

5.  **Bonus Filtering**:
    In addition to filtering by `category_id`, the system supports searching by category name string (e.g., `?category=clothing`) as a developer convenience.

---

## 🖥️ Web Interface
A dedicated Admin Dashboard is available at the root URL: `http://localhost:8080/products`.
- Integrated category management.
- Real-time status toggles.
- Bulk action support.
- One-click Excel export.
