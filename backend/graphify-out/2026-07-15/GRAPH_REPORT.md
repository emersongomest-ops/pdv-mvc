# Graph Report - projects\pdv\backend  (2026-07-15)

## Corpus Check
- 215 files · ~24,630 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1084 nodes · 1915 edges · 102 communities (84 shown, 18 thin omitted)
- Extraction: 89% EXTRACTED · 11% INFERRED · 0% AMBIGUOUS · INFERRED: 210 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `f82cedd2`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- User
- composer.json
- TestCase
- Controller
- StoreContext
- scripts
- AuthenticationDomainException
- UserFactory
- devDependencies
- DomainScaffolder
- AppServiceProvider.php
- ListAccessibleStoresAction
- AuthErrorCodeTest
- StoreErrorCodeTest
- DatabaseSeeder.php
- AnalyticsRepositoryInterface
- CashShiftRepository.php
- CatalogRepository.php
- CustomersRepository.php
- IdentityAccessRepository.php
- InventoryRepository.php
- PaymentsRepository.php
- PromotionsRepository.php
- RefundsReturnsRepository.php
- SalesRepository.php
- CashShiftRepositoryInterface.php
- CatalogRepositoryInterface.php
- InventoryRepositoryInterface.php
- PaymentsRepositoryInterface.php
- SalesRepositoryInterface.php
- CreateSaleAction
- ErrorCode.php
- Sale
- Category
- CreateProductAction
- CatalogErrorCodeTest
- InventoryErrorCodeTest
- SaleErrorCodeTest
- UpdateProductAction
- CatalogRepositoryInterface.php
- SaleResource
- SalesRepository
- Model
- ListCategoriesAction
- ShowCategoryAction
- ShowProductAction
- AppServiceProvider.php
- StoreRepository
- AdminRouteAccessTest
- LoginTest
- OperationalRouteAccessTest
- ProductCrudTest
- CloseCashShiftTest
- ListCategoriesAction
- ResumeSaleAction
- DeleteCategoryAction
- DeleteProductAction
- ShowCategoryAction
- CurrentCashShiftController.php
- ShowProductAction
- CatalogResource

## God Nodes (most connected - your core abstractions)
1. `User` - 107 edges
2. `Store` - 73 edges
3. `Controller` - 59 edges
4. `Sale` - 58 edges
5. `TestCase` - 57 edges
6. `Product` - 54 edges
7. `Category` - 29 edges
8. `CashShift` - 26 edges
9. `SaleLine` - 26 edges
10. `StoreInventory` - 22 edges

## Surprising Connections (you probably didn't know these)
- `findCategoryById()` --references--> `Category`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Category.php
- `createCategory()` --references--> `Category`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Category.php
- `updateCategory()` --references--> `Category`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Category.php
- `deleteCategory()` --references--> `Category`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Category.php
- `findProductById()` --references--> `Product`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Product.php

## Import Cycles
- None detected.

## Communities (102 total, 18 thin omitted)

### Community 0 - "User"
Cohesion: 0.10
Nodes (5): Product, ProductCrudTest, AdminInventoryTest, InventorySaleTest, CompleteSaleTest

### Community 1 - "composer.json"
Cohesion: 0.05
Nodes (41): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+33 more)

### Community 2 - "TestCase"
Cohesion: 0.43
Nodes (5): EnsureOpenCashShift, CashShiftRepositoryInterface, Closure, Request, Response

### Community 3 - "Controller"
Cohesion: 0.07
Nodes (20): LoginUserAction, CompleteSaleAction, FiscalReceiptGeneratorInterface, InventoryRepositoryInterface, PaymentGatewayInterface, PaymentsRepositoryInterface, SalesRepositoryInterface, HoldSaleAction (+12 more)

### Community 4 - "StoreContext"
Cohesion: 0.10
Nodes (16): Request, StoreRepositoryInterface, SelectStoreContextAction, OperationalPosController, JsonResponse, Request, EnsureStoreContext, Closure (+8 more)

### Community 5 - "scripts"
Cohesion: 0.08
Nodes (27): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+19 more)

### Community 6 - "AuthenticationDomainException"
Cohesion: 0.07
Nodes (25): CashShiftDomainException, ErrorCode, CatalogDomainException, ErrorCode, AuthenticationDomainException, ErrorCode, InventoryDomainException, ErrorCode (+17 more)

### Community 7 - "UserFactory"
Cohesion: 0.06
Nodes (15): CashShiftFactory, static, CategoryFactory, static, static, ProductFactory, static, SaleFactory (+7 more)

### Community 8 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 9 - "DomainScaffolder"
Cohesion: 0.21
Nodes (3): CreateDomainCommand, DomainScaffolder, Command

### Community 10 - "AppServiceProvider.php"
Cohesion: 0.10
Nodes (11): SalePaymentValidator, PaymentRequest, PaymentResult, RefundRequest, RefundResult, charge(), refund(), StubPaymentGateway (+3 more)

### Community 11 - "ListAccessibleStoresAction"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "DatabaseSeeder.php"
Cohesion: 0.60
Nodes (3): DatabaseSeeder, Seeder, WithoutModelEvents

### Community 16 - "CashShiftRepository.php"
Cohesion: 0.07
Nodes (19): CloseCashShiftAction, CashShiftRepositoryInterface, OpenCashShiftAction, CashShiftRepositoryInterface, close(), createOpen(), findById(), findOpenForUser() (+11 more)

### Community 17 - "CatalogRepository.php"
Cohesion: 0.11
Nodes (14): ActsWithOperationalSession, BaseTestCase, InteractsWithStatefulApi, RefreshDatabase, OpenCashShiftTest, OperationalShiftGateTest, AdminCatalogAccessTest, CategoryCrudTest (+6 more)

### Community 20 - "InventoryRepository.php"
Cohesion: 0.07
Nodes (19): AdjustStoreInventoryAction, CatalogRepositoryInterface, InventoryRepositoryInterface, adjustQuantity(), findForStoreProduct(), listForStore(), Collection, AdjustStoreInventoryController (+11 more)

### Community 21 - "PaymentsRepository.php"
Cohesion: 0.31
Nodes (5): SalesRepositoryInterface, RemoveSaleLineAction, JsonResponse, Request, RemoveSaleLineController

### Community 24 - "SalesRepository.php"
Cohesion: 0.14
Nodes (11): createCategory(), createProduct(), deleteCategory(), deleteProduct(), findCategoryById(), findProductById(), listCategories(), listProducts() (+3 more)

### Community 34 - "CatalogRepositoryInterface.php"
Cohesion: 0.09
Nodes (15): AddSaleLineAction, CatalogRepositoryInterface, SalesRepositoryInterface, SalesRepositoryInterface, ShowSaleAction, SalesRepositoryInterface, UpdateSaleLineAction, AddSaleLineController (+7 more)

### Community 37 - "InventoryRepositoryInterface.php"
Cohesion: 0.19
Nodes (6): ListStoreInventoryAction, Collection, InventoryRepositoryInterface, ListStoreInventoryController, JsonResponse, ListStoreInventoryRequest

### Community 38 - "PaymentsRepositoryInterface.php"
Cohesion: 0.12
Nodes (5): CatalogRepository, Collection, Category, HasMany, CatalogRepositoryInterface

### Community 57 - "CreateSaleAction"
Cohesion: 0.29
Nodes (6): ListProductsAction, CatalogRepositoryInterface, Collection, ListProductsController, JsonResponse, Request

### Community 61 - "Sale"
Cohesion: 0.11
Nodes (14): SaleCartGuard, addLine(), complete(), createInProgress(), findById(), hold(), listHeldForShift(), Collection (+6 more)

### Community 62 - "Category"
Cohesion: 0.07
Nodes (15): findById(), listAccessibleForUser(), userCanAccessStore(), Store, User, StorePolicy, Authenticatable, CloseCashShiftTest (+7 more)

### Community 63 - "CreateProductAction"
Cohesion: 0.21
Nodes (5): CreateProductAction, CatalogRepositoryInterface, CreateProductController, JsonResponse, StoreProductRequest

### Community 73 - "UpdateProductAction"
Cohesion: 0.21
Nodes (5): CatalogRepositoryInterface, UpdateProductAction, JsonResponse, UpdateProductController, UpdateProductRequest

### Community 74 - "CatalogRepositoryInterface.php"
Cohesion: 0.22
Nodes (5): CatalogRepositoryInterface, UpdateCategoryAction, JsonResponse, UpdateCategoryController, UpdateCategoryRequest

### Community 75 - "SaleResource"
Cohesion: 0.25
Nodes (5): generateForSale(), StubFiscalReceiptGenerator, FiscalReceipt, BelongsTo, FiscalReceiptGeneratorInterface

### Community 76 - "SalesRepository"
Cohesion: 0.12
Nodes (9): findLineById(), findLineByProduct(), removeLine(), updateLineQuantity(), Collection, SalesRepository, BelongsTo, SaleLine (+1 more)

### Community 77 - "Model"
Cohesion: 0.18
Nodes (5): BelongsTo, BelongsToMany, BelongsToMany, HasFactory, Notifiable

### Community 78 - "ListCategoriesAction"
Cohesion: 0.29
Nodes (6): ListHeldSalesAction, Collection, SalesRepositoryInterface, ListHeldSalesController, JsonResponse, Request

### Community 79 - "ShowCategoryAction"
Cohesion: 0.31
Nodes (5): CreateSaleAction, SalesRepositoryInterface, CreateSaleController, JsonResponse, Request

### Community 80 - "ShowProductAction"
Cohesion: 0.31
Nodes (5): ListAccessibleStoresAction, StoreRepositoryInterface, ListStoresController, JsonResponse, Request

### Community 81 - "AppServiceProvider.php"
Cohesion: 0.17
Nodes (5): PaymentsRepository, PaymentLine, BelongsTo, SaleResource, PaymentsRepositoryInterface

### Community 82 - "StoreRepository"
Cohesion: 0.22
Nodes (4): StoreRepository, AppServiceProvider, ServiceProvider, StoreRepositoryInterface

### Community 86 - "ProductCrudTest"
Cohesion: 0.22
Nodes (5): CreateCategoryAction, CatalogRepositoryInterface, CreateCategoryController, JsonResponse, StoreCategoryRequest

### Community 88 - "CloseCashShiftTest"
Cohesion: 0.27
Nodes (6): JsonResponse, ShowProductController, Controller, AdminDashboardController, JsonResponse, Request

### Community 92 - "ListCategoriesAction"
Cohesion: 0.31
Nodes (5): ListCategoriesAction, CatalogRepositoryInterface, Collection, ListCategoriesController, JsonResponse

### Community 93 - "ResumeSaleAction"
Cohesion: 0.31
Nodes (5): SalesRepositoryInterface, ResumeSaleAction, JsonResponse, Request, ResumeSaleController

### Community 94 - "DeleteCategoryAction"
Cohesion: 0.33
Nodes (4): DeleteCategoryAction, CatalogRepositoryInterface, DeleteCategoryController, JsonResponse

### Community 95 - "DeleteProductAction"
Cohesion: 0.33
Nodes (4): DeleteProductAction, CatalogRepositoryInterface, DeleteProductController, JsonResponse

### Community 96 - "ShowCategoryAction"
Cohesion: 0.33
Nodes (4): CatalogRepositoryInterface, ShowCategoryAction, JsonResponse, ShowCategoryController

### Community 97 - "CurrentCashShiftController.php"
Cohesion: 0.53
Nodes (4): CurrentCashShiftController, CashShiftRepositoryInterface, JsonResponse, Request

## Knowledge Gaps
- **64 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+59 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **18 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `Category` to `User`, `Controller`, `StoreContext`, `PaymentsRepositoryInterface.php`, `Model`, `DatabaseSeeder.php`, `ShowCategoryAction`, `CashShiftRepository.php`, `ShowProductAction`, `StoreRepository`, `AdminRouteAccessTest`, `LoginTest`, `OperationalRouteAccessTest`, `CatalogRepository.php`?**
  _High betweenness centrality (0.155) - this node is a cross-community bridge._
- **Why does `Controller` connect `CloseCashShiftTest` to `Controller`, `StoreContext`, `CashShiftRepository.php`, `InventoryRepository.php`, `PaymentsRepository.php`, `CatalogRepositoryInterface.php`, `InventoryRepositoryInterface.php`, `CreateSaleAction`, `CreateProductAction`, `UpdateProductAction`, `CatalogRepositoryInterface.php`, `ListCategoriesAction`, `ShowCategoryAction`, `ShowProductAction`, `ProductCrudTest`, `ListCategoriesAction`, `ResumeSaleAction`, `DeleteCategoryAction`, `DeleteProductAction`, `ShowCategoryAction`, `CurrentCashShiftController.php`?**
  _High betweenness centrality (0.144) - this node is a cross-community bridge._
- **Why does `Sale` connect `Sale` to `User`, `CatalogRepositoryInterface.php`, `Controller`, `SaleResource`, `SalesRepository`, `Model`, `ShowCategoryAction`, `AppServiceProvider.php`, `InventoryRepository.php`, `PaymentsRepository.php`, `SaleStatus.php`, `ResumeSaleAction`, `Category`?**
  _High betweenness centrality (0.141) - this node is a cross-community bridge._
- **Are the 77 inferred relationships involving `User` (e.g. with `.execute()` and `.definition()`) actually correct?**
  _`User` has 77 INFERRED edges - model-reasoned connections that need verification._
- **Are the 56 inferred relationships involving `Store` (e.g. with `.definition()` and `.definition()`) actually correct?**
  _`Store` has 56 INFERRED edges - model-reasoned connections that need verification._
- **Are the 6 inferred relationships involving `Sale` (e.g. with `.test_delete_product_with_sale_lines_returns_cat_product_in_use()` and `.test_completing_already_completed_sale_returns_sale_already_completed()`) actually correct?**
  _`Sale` has 6 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _64 weakly-connected nodes found - possible documentation gaps or missing edges._