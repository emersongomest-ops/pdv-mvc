# Graph Report - projects\pdv\backend  (2026-07-16)

## Corpus Check
- 374 files · ~47,865 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1945 nodes · 3785 edges · 204 communities (162 shown, 42 thin omitted)
- Extraction: 87% EXTRACTED · 13% INFERRED · 0% AMBIGUOUS · INFERRED: 504 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `7bf6a697`
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
- CustomersRepositoryInterface.php
- InventoryRepositoryInterface.php
- PaymentsRepositoryInterface.php
- PromotionsRepositoryInterface.php
- RefundsReturnsRepositoryInterface.php
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
- UpdateCustomerAction
- SaleStatus.php
- CloseCashShiftTest
- ListCategoriesAction
- ResumeSaleAction
- DeleteCategoryAction
- DeleteProductAction
- ShowCategoryAction
- CurrentCashShiftController.php
- ShowProductAction
- CatalogResource
- UpdateSaleLineAction
- CreateCustomerAction
- ListCustomersAction
- CustomerResource
- CreateSaleAction
- ShowSaleAction
- FindCustomerByCpfAction
- ShowCustomerAction
- OperationalRouteAccessTest
- CustomerErrorCodeTest
- ResumeSaleController.php
- ListStoresTest
- ListPromotionsAction
- PaymentMethod
- SaleResource
- Promotion.php
- ApplyPromotionToSaleAction
- SalePaymentValidator
- require-dev
- setup
- ListStoresTest
- Sale.php
- config
- ApiErrorResponse.php
- PromotionDiscountCalculator
- PromotionErrorCodeTest
- RemovePromotionFromSaleController.php
- autoload-dev
- psr-4
- require
- HasFactory
- post-create-project-cmd
- TelescopeServiceProvider
- AssignCorrelationId.php
- HorizonServiceProvider
- AttachCustomerToSaleAction
- HoldSaleAction
- AssertManagerStoreAccess
- RefundErrorCodeTest
- DeleteCategoryAction
- RemoveSaleLineAction
- FormRequest
- AuthenticationDomainException
- ResumeSaleController.php
- 2026_07_16_165219_create_telescope_entries_table.php
- ListStoreInventoryRequest
- SaleCompletedNotification
- SaleCompletedNotificationTest
- BusOrchestratorTest
- UsersRepository
- InventoryErrorCodeTest
- PromotionErrorCodeTest
- SaleErrorCodeTest
- AppServiceProvider.php
- RecordCustomerPurchaseJob
- RecordSaleCompletedAnalyticsJob
- AdminStoreAccessIdorTest
- DispatchSaleSideEffectsTest
- ProbeQueuedJob
- CompleteSaleTest
- ShowCurrentUserController.php
- EnsureStoreContext.php
- StoreDomainException
- SalesCartTest
- ShowAdminShiftReportController.php
- AdjustStoreInventoryAction
- RemoveSaleLineController.php
- ResumeSaleController.php
- StoreRepository
- SessionGateTest
- CustomerCrudTest
- ListAdminShiftsAction
- ListStoreInventoryAction
- AdminRouteAccessTest
- LoginTest
- ListAdminAuditLogsAction
- HoldSaleAction
- ShowAdminSaleAction
- OperationalPosController.php
- AdminInventoryTest
- ActsWithOperationalSession.php
- OperationalRouteAccessTest
- SelectStoreContextTest
- AdminAuditLogTest

## God Nodes (most connected - your core abstractions)
1. `User` - 243 edges
2. `Store` - 123 edges
3. `Controller` - 113 edges
4. `TestCase` - 109 edges
5. `Sale` - 97 edges
6. `Product` - 85 edges
7. `Money` - 46 edges
8. `Customer` - 44 edges
9. `Promotion` - 44 edges
10. `AssertManagerStoreAccess` - 37 edges

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

## Communities (204 total, 42 thin omitted)

### Community 0 - "User"
Cohesion: 0.09
Nodes (14): AddSaleLineAction, CatalogRepositoryInterface, SalesRepositoryInterface, SalesRepositoryInterface, ShowSaleAction, AddSaleLineController, JsonResponse, JsonResponse (+6 more)

### Community 1 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+5 more)

### Community 2 - "TestCase"
Cohesion: 0.43
Nodes (5): EnsureOpenCashShift, CashShiftRepositoryInterface, Closure, Request, Response

### Community 3 - "Controller"
Cohesion: 0.16
Nodes (8): PaymentRequest, PaymentResult, RefundRequest, RefundResult, charge(), refund(), StubPaymentGateway, PaymentGatewayInterface

### Community 4 - "StoreContext"
Cohesion: 0.21
Nodes (6): AuditLogRepositoryInterface, CatalogRepositoryInterface, UpdateProductAction, JsonResponse, UpdateProductController, UpdateProductRequest

### Community 5 - "scripts"
Cohesion: 0.17
Nodes (12): scripts, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall, @php artisan key:generate --ansi (+4 more)

### Community 6 - "AuthenticationDomainException"
Cohesion: 0.09
Nodes (19): CashShiftDomainException, ErrorCode, CatalogDomainException, ErrorCode, CustomerDomainException, ErrorCode, InventoryDomainException, ErrorCode (+11 more)

### Community 7 - "UserFactory"
Cohesion: 0.06
Nodes (18): CashShiftFactory, static, CategoryFactory, static, CustomerFactory, static, ProductFactory, static (+10 more)

### Community 8 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 9 - "DomainScaffolder"
Cohesion: 0.21
Nodes (3): CreateDomainCommand, DomainScaffolder, Command

### Community 10 - "AppServiceProvider.php"
Cohesion: 0.23
Nodes (9): NotifyManagersOfSaleCompleted, SaleCompleted, AbstractQueuedJob, Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels (+1 more)

### Community 11 - "ListAccessibleStoresAction"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "DatabaseSeeder.php"
Cohesion: 0.83
Nodes (3): DatabaseSeeder, Seeder, WithoutModelEvents

### Community 16 - "CashShiftRepository.php"
Cohesion: 0.07
Nodes (20): OpenCashShiftAction, CashShiftRepositoryInterface, ShiftClosingReport, buildClosingReport(), close(), createOpen(), findById(), findOpenForUser() (+12 more)

### Community 17 - "CatalogRepository.php"
Cohesion: 0.28
Nodes (3): ListAdminShiftsController, JsonResponse, ListAdminShiftsRequest

### Community 18 - "CustomersRepository.php"
Cohesion: 0.25
Nodes (3): CustomersRepository, Collection, CustomersRepositoryInterface

### Community 20 - "InventoryRepository.php"
Cohesion: 0.09
Nodes (11): adjustQuantity(), findForStoreProduct(), listForStore(), Collection, InventoryRepository, Collection, BelongsTo, StoreInventory (+3 more)

### Community 22 - "PromotionsRepository.php"
Cohesion: 0.13
Nodes (6): findApplied(), Collection, PromotionsRepository, BelongsTo, SalePromotion, PromotionsRepositoryInterface

### Community 23 - "RefundsReturnsRepository.php"
Cohesion: 0.21
Nodes (5): GetAdminDashboardMetricsAction, AdminDashboardMetrics, AdminDashboardController, JsonResponse, Request

### Community 24 - "SalesRepository.php"
Cohesion: 0.14
Nodes (11): createCategory(), createProduct(), deleteCategory(), deleteProduct(), findCategoryById(), findProductById(), listCategories(), listProducts() (+3 more)

### Community 34 - "CatalogRepositoryInterface.php"
Cohesion: 0.33
Nodes (4): CloseCashShiftAction, CashShiftRepositoryInterface, CloseCashShiftController, JsonResponse

### Community 35 - "CustomersRepositoryInterface.php"
Cohesion: 0.20
Nodes (8): create(), findByCpf(), findById(), list(), Collection, update(), Customer, HasMany

### Community 37 - "InventoryRepositoryInterface.php"
Cohesion: 0.21
Nodes (6): ListAdminSalesAction, Collection, SalesRepositoryInterface, ListAdminSalesController, JsonResponse, ListAdminSalesRequest

### Community 39 - "PromotionsRepositoryInterface.php"
Cohesion: 0.18
Nodes (9): attachToSale(), create(), findByCode(), findById(), list(), listAppliedForSale(), Collection, update() (+1 more)

### Community 40 - "RefundsReturnsRepositoryInterface.php"
Cohesion: 0.11
Nodes (10): ActsWithAdminStoreAccess, PaymentsRepository, PaymentLine, BelongsTo, PaymentMethod, PaymentsRepositoryInterface, AdminShiftReportTest, ListAdminSalesTest (+2 more)

### Community 57 - "CreateSaleAction"
Cohesion: 0.29
Nodes (6): ListProductsAction, CatalogRepositoryInterface, Collection, ListProductsController, JsonResponse, Request

### Community 61 - "Sale"
Cohesion: 0.09
Nodes (16): SaleCartGuard, addLine(), attachCustomer(), complete(), createInProgress(), findById(), hold(), listForAdmin() (+8 more)

### Community 66 - "SaleErrorCodeTest"
Cohesion: 0.20
Nodes (6): ApplyPromotionToSaleAction, PromotionsRepositoryInterface, SalesRepositoryInterface, ApplyPromotionToSaleController, JsonResponse, ApplyPromotionToSaleRequest

### Community 73 - "UpdateProductAction"
Cohesion: 0.08
Nodes (13): AdminAuditFilters, AuditLogEntry, append(), listForAdmin(), ListAdminAuditLogsController, JsonResponse, ListAdminAuditLogsRequest, AuditLogRepository (+5 more)

### Community 74 - "CatalogRepositoryInterface.php"
Cohesion: 0.22
Nodes (5): CatalogRepositoryInterface, UpdateCategoryAction, JsonResponse, UpdateCategoryController, UpdateCategoryRequest

### Community 75 - "SaleResource"
Cohesion: 0.29
Nodes (7): ListOperationalProductsAction, CatalogRepositoryInterface, Collection, InventoryRepositoryInterface, ListOperationalProductsController, JsonResponse, Request

### Community 76 - "SalesRepository"
Cohesion: 0.14
Nodes (4): AdminSaleFilters, Collection, SalesRepository, SalesRepositoryInterface

### Community 77 - "Model"
Cohesion: 0.22
Nodes (5): CreateCategoryAction, CatalogRepositoryInterface, CreateCategoryController, JsonResponse, StoreCategoryRequest

### Community 78 - "ListCategoriesAction"
Cohesion: 0.17
Nodes (5): PromotionAuditSnapshot, Collection, PromotionDiscountCalculator, Promotion, DateTimeInterface

### Community 79 - "ShowCategoryAction"
Cohesion: 0.21
Nodes (5): CreateProductAction, CatalogRepositoryInterface, CreateProductController, JsonResponse, StoreProductRequest

### Community 80 - "ShowProductAction"
Cohesion: 0.31
Nodes (5): ListAccessibleStoresAction, StoreRepositoryInterface, ListStoresController, JsonResponse, Request

### Community 81 - "AppServiceProvider.php"
Cohesion: 0.21
Nodes (5): generateForSale(), StubFiscalReceiptGenerator, FiscalReceipt, BelongsTo, FiscalReceiptGeneratorInterface

### Community 82 - "StoreRepository"
Cohesion: 0.10
Nodes (8): assignedStoreIds(), findById(), listAccessibleForUser(), userCanAccessStore(), User, Authenticatable, AdminUserManagementTest, PromotionCrudTest

### Community 83 - "AdminRouteAccessTest"
Cohesion: 0.33
Nodes (4): PromotionsRepositoryInterface, ShowPromotionAction, JsonResponse, ShowPromotionController

### Community 84 - "LoginTest"
Cohesion: 0.08
Nodes (16): CreateUserAction, UsersRepositoryInterface, ListUsersAction, Collection, UsersRepositoryInterface, UsersRepositoryInterface, ShowUserAction, CreateUserController (+8 more)

### Community 85 - "OperationalRouteAccessTest"
Cohesion: 0.39
Nodes (5): CreatePromotionAction, AuditLogRepositoryInterface, PromotionsRepositoryInterface, CreatePromotionController, JsonResponse

### Community 86 - "UpdateCustomerAction"
Cohesion: 0.24
Nodes (5): CustomersRepositoryInterface, UpdateCustomerAction, JsonResponse, UpdateCustomerController, UpdateCustomerRequest

### Community 87 - "SaleStatus.php"
Cohesion: 0.31
Nodes (5): Request, StoreRepositoryInterface, SelectStoreContextAction, Request, StoreContext

### Community 88 - "CloseCashShiftTest"
Cohesion: 0.17
Nodes (8): SalesRepositoryInterface, UpdateSaleLineAction, findLineById(), findLineByProduct(), removeLine(), updateLineQuantity(), BelongsTo, SaleLine

### Community 92 - "ListCategoriesAction"
Cohesion: 0.31
Nodes (5): ListCategoriesAction, CatalogRepositoryInterface, Collection, ListCategoriesController, JsonResponse

### Community 93 - "ResumeSaleAction"
Cohesion: 0.22
Nodes (9): dev, serve, test, Composer\\Config::disableProcessTimeout, npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74,#86efac\" \"php artisan serve\" \"php artisan queue:listen redis --tries=3 --timeout=0\" \"php artisan reverb:start\" \"php artisan pail --timeout=0\" \"npm run dev\" --names=server,queue,reverb,logs,vite --kill-others, @php artisan config:clear --ansi @no_additional_args, @php artisan serve --host=127.0.0.1 --port=8000, @php artisan test (+1 more)

### Community 94 - "DeleteCategoryAction"
Cohesion: 0.36
Nodes (6): PromotionsRepositoryInterface, SalesRepositoryInterface, RemovePromotionFromSaleAction, JsonResponse, Request, RemovePromotionFromSaleController

### Community 95 - "DeleteProductAction"
Cohesion: 0.28
Nodes (3): CloseCashShiftRequest, AttachCustomerToSaleRequest, FormRequest

### Community 96 - "ShowCategoryAction"
Cohesion: 0.33
Nodes (4): CatalogRepositoryInterface, ShowCategoryAction, JsonResponse, ShowCategoryController

### Community 97 - "CurrentCashShiftController.php"
Cohesion: 0.53
Nodes (4): CurrentCashShiftController, CashShiftRepositoryInterface, JsonResponse, Request

### Community 98 - "ShowProductAction"
Cohesion: 0.33
Nodes (4): CatalogRepositoryInterface, ShowProductAction, JsonResponse, ShowProductController

### Community 99 - "CatalogResource"
Cohesion: 0.29
Nodes (7): ListRefundsForSaleAction, Collection, RefundsReturnsRepositoryInterface, SalesRepositoryInterface, ListRefundsForSaleController, JsonResponse, Request

### Community 102 - "UpdateSaleLineAction"
Cohesion: 0.27
Nodes (6): AuditLogRepositoryInterface, PromotionsRepositoryInterface, UpdatePromotionAction, JsonResponse, UpdatePromotionController, UpdatePromotionRequest

### Community 103 - "CreateCustomerAction"
Cohesion: 0.22
Nodes (3): CreateCustomerAction, CustomersRepositoryInterface, CustomerPayloadNormalizer

### Community 104 - "ListCustomersAction"
Cohesion: 0.29
Nodes (6): ListCustomersAction, Collection, CustomersRepositoryInterface, ListCustomersController, JsonResponse, Request

### Community 105 - "CustomerResource"
Cohesion: 0.22
Nodes (4): CreateCustomerController, JsonResponse, StoreCustomerRequest, CustomerResource

### Community 106 - "CreateSaleAction"
Cohesion: 0.31
Nodes (5): CreateSaleAction, SalesRepositoryInterface, CreateSaleController, JsonResponse, Request

### Community 107 - "ShowSaleAction"
Cohesion: 0.25
Nodes (5): DispatchSaleSideEffects, BusOrchestrator, Batch, PendingBatch, PendingChain

### Community 108 - "FindCustomerByCpfAction"
Cohesion: 0.31
Nodes (5): FindCustomerByCpfAction, CustomersRepositoryInterface, FindOperationalCustomerController, JsonResponse, Request

### Community 109 - "ShowCustomerAction"
Cohesion: 0.33
Nodes (4): CustomersRepositoryInterface, ShowCustomerAction, JsonResponse, ShowCustomerController

### Community 110 - "OperationalRouteAccessTest"
Cohesion: 0.08
Nodes (10): BaseTestCase, AdminDashboardMetricsTest, ListAdminNotificationsTest, AdminCatalogAccessTest, CategoryCrudTest, ExampleTest, CorrelationIdMiddlewareTest, TestCase (+2 more)

### Community 111 - "CustomerErrorCodeTest"
Cohesion: 0.22
Nodes (5): UsersRepositoryInterface, UpdateUserAction, JsonResponse, UpdateUserController, UpdateUserRequest

### Community 112 - "ResumeSaleController.php"
Cohesion: 0.32
Nodes (3): CompleteSaleController, JsonResponse, CompleteSaleRequest

### Community 113 - "ListStoresTest"
Cohesion: 0.60
Nodes (3): JsonResponse, Request, ShowAdminShiftReportController

### Community 117 - "ListPromotionsAction"
Cohesion: 0.31
Nodes (5): ListPromotionsAction, Collection, PromotionsRepositoryInterface, ListPromotionsController, JsonResponse

### Community 119 - "SaleResource"
Cohesion: 0.05
Nodes (27): CreateRefundAction, AuditLogRepositoryInterface, InventoryRepositoryInterface, PaymentGatewayInterface, RefundsReturnsRepositoryInterface, create(), findCompletedSale(), listForSale() (+19 more)

### Community 120 - "Promotion.php"
Cohesion: 0.32
Nodes (3): AdjustStoreInventoryController, JsonResponse, AdjustStoreInventoryRequest

### Community 121 - "ApplyPromotionToSaleAction"
Cohesion: 0.29
Nodes (6): ListHeldSalesAction, Collection, SalesRepositoryInterface, ListHeldSalesController, JsonResponse, Request

### Community 122 - "SalePaymentValidator"
Cohesion: 0.32
Nodes (3): JsonResponse, SelectStoreContextController, SelectStoreContextRequest

### Community 123 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, laravel/telescope, mockery/mockery, nunomaduro/collision (+1 more)

### Community 124 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 126 - "ListStoresTest"
Cohesion: 0.19
Nodes (3): StorePromotionRequest, BelongsToMany, HasMany

### Community 127 - "Sale.php"
Cohesion: 0.32
Nodes (3): HoldSaleController, JsonResponse, HoldSaleRequest

### Community 128 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 129 - "ApiErrorResponse.php"
Cohesion: 0.29
Nodes (6): ApiErrorResponse, ErrorCode, JsonResponse, Request, AuthenticationException, AuthorizationException

### Community 130 - "PromotionDiscountCalculator"
Cohesion: 0.11
Nodes (4): Money, PromotionResource, SaleResource, MoneyTest

### Community 131 - "PromotionErrorCodeTest"
Cohesion: 0.25
Nodes (5): LogoutUserAction, Request, LogoutController, JsonResponse, Request

### Community 132 - "RemovePromotionFromSaleController.php"
Cohesion: 0.61
Nodes (6): CompleteSaleAction, FiscalReceiptGeneratorInterface, InventoryRepositoryInterface, PaymentGatewayInterface, PaymentsRepositoryInterface, SalesRepositoryInterface

### Community 133 - "autoload-dev"
Cohesion: 0.15
Nodes (13): ActsWithOperationalSession, InteractsWithStatefulApi, RefreshDatabase, CloseCashShiftTest, OpenCashShiftTest, OperationalShiftGateTest, OperationalCatalogTest, InventorySaleTest (+5 more)

### Community 134 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 135 - "require"
Cohesion: 0.25
Nodes (8): require, laravel/framework, laravel/horizon, laravel/pulse, laravel/reverb, laravel/sanctum, laravel/tinker, php

### Community 136 - "HasFactory"
Cohesion: 0.18
Nodes (5): BelongsTo, BelongsToMany, BelongsToMany, HasFactory, Notifiable

### Community 137 - "post-create-project-cmd"
Cohesion: 0.33
Nodes (5): create(), findById(), list(), Collection, update()

### Community 139 - "AssignCorrelationId.php"
Cohesion: 0.53
Nodes (4): AssignCorrelationId, Closure, Request, Response

### Community 142 - "AttachCustomerToSaleAction"
Cohesion: 0.33
Nodes (5): AttachCustomerToSaleAction, CustomersRepositoryInterface, SalesRepositoryInterface, AttachCustomerToSaleController, JsonResponse

### Community 144 - "HoldSaleAction"
Cohesion: 0.06
Nodes (3): Product, ProductCrudTest, ApplyPromotionToSaleTest

### Community 145 - "AssertManagerStoreAccess"
Cohesion: 0.33
Nodes (4): GetAdminShiftReportAction, CashShiftRepositoryInterface, AssertManagerStoreAccess, StoreRepositoryInterface

### Community 148 - "RemoveSaleLineAction"
Cohesion: 0.06
Nodes (6): Store, StorePolicy, OperationalCustomerTest, actingAsManagerForStore(), attachManagerToStore(), static

### Community 149 - "FormRequest"
Cohesion: 0.27
Nodes (4): LoginUserAction, LoginController, JsonResponse, LoginRequest

### Community 150 - "AuthenticationDomainException"
Cohesion: 0.31
Nodes (6): AuthenticationDomainException, ErrorCode, EnsureUserHasRole, Closure, Request, Response

### Community 151 - "ResumeSaleController.php"
Cohesion: 0.50
Nodes (4): extra, laravel, dont-discover, laravel/telescope

### Community 152 - "2026_07_16_165219_create_telescope_entries_table.php"
Cohesion: 0.83
Nodes (3): down(), getConnection(), up()

### Community 157 - "ListStoreInventoryRequest"
Cohesion: 0.28
Nodes (3): ListStoreInventoryController, JsonResponse, ListStoreInventoryRequest

### Community 160 - "SaleCompletedNotificationTest"
Cohesion: 0.12
Nodes (5): CatalogRepository, Collection, Category, HasMany, CatalogRepositoryInterface

### Community 162 - "UsersRepository"
Cohesion: 0.28
Nodes (3): Collection, UsersRepository, UsersRepositoryInterface

### Community 169 - "AppServiceProvider.php"
Cohesion: 0.28
Nodes (4): LogSaleAnalyticsRecorder, AppServiceProvider, SaleAnalyticsRecorderInterface, ServiceProvider

### Community 176 - "ShowCurrentUserController.php"
Cohesion: 0.36
Nodes (4): ShowCurrentUserAction, JsonResponse, Request, ShowCurrentUserController

### Community 177 - "EnsureStoreContext.php"
Cohesion: 0.43
Nodes (5): EnsureStoreContext, Closure, Request, Response, StoreRepositoryInterface

### Community 178 - "StoreDomainException"
Cohesion: 0.33
Nodes (4): DeleteProductAction, CatalogRepositoryInterface, DeleteProductController, JsonResponse

### Community 180 - "ShowAdminShiftReportController.php"
Cohesion: 0.19
Nodes (9): DeleteCategoryController, JsonResponse, Controller, ListAdminNotificationsController, JsonResponse, Request, JsonResponse, Request (+1 more)

### Community 181 - "AdjustStoreInventoryAction"
Cohesion: 0.57
Nodes (4): AdjustStoreInventoryAction, AuditLogRepositoryInterface, CatalogRepositoryInterface, InventoryRepositoryInterface

### Community 182 - "RemoveSaleLineController.php"
Cohesion: 0.60
Nodes (3): JsonResponse, Request, RemoveSaleLineController

### Community 183 - "ResumeSaleController.php"
Cohesion: 0.60
Nodes (3): JsonResponse, Request, ResumeSaleController

### Community 191 - "ListAdminShiftsAction"
Cohesion: 0.53
Nodes (3): ListAdminShiftsAction, CashShiftRepositoryInterface, Collection

### Community 192 - "ListStoreInventoryAction"
Cohesion: 0.53
Nodes (3): ListStoreInventoryAction, Collection, InventoryRepositoryInterface

### Community 198 - "OperationalPosController.php"
Cohesion: 0.60
Nodes (3): OperationalPosController, JsonResponse, Request

### Community 200 - "ActsWithOperationalSession.php"
Cohesion: 0.70
Nodes (4): actingAsOperatorAtStore(), actingAsOperatorWithOpenShift(), static, withOpenShift()

## Knowledge Gaps
- **68 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+63 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **42 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `StoreRepository` to `StoreContext`, `autoload-dev`, `HasFactory`, `post-create-project-cmd`, `AppServiceProvider.php`, `CashShiftRepository.php`, `AssertManagerStoreAccess`, `HoldSaleAction`, `RemoveSaleLineAction`, `FormRequest`, `InventoryRepository.php`, `RefundsReturnsRepository.php`, `SaleCompletedNotificationTest`, `CatalogRepositoryInterface.php`, `UsersRepository`, `InventoryRepositoryInterface.php`, `RefundsReturnsRepositoryInterface.php`, `AdminStoreAccessIdorTest`, `CompleteSaleTest`, `ShowCurrentUserController.php`, `SalesCartTest`, `AdjustStoreInventoryAction`, `StoreRepository`, `SessionGateTest`, `StoreFactory`, `Sale`, `CustomerCrudTest`, `ListAdminShiftsAction`, `ListStoreInventoryAction`, `AdminRouteAccessTest`, `LoginTest`, `ListAdminAuditLogsAction`, `CreateProductAction`, `ShowAdminSaleAction`, `AdminInventoryTest`, `ActsWithOperationalSession.php`, `OperationalRouteAccessTest`, `SelectStoreContextTest`, `AdminAuditLogTest`, `ListCategoriesAction`, `ShowProductAction`, `LoginTest`, `OperationalRouteAccessTest`, `SaleStatus.php`, `CloseCashShiftTest`, `CatalogResource`, `UpdateSaleLineAction`, `CreateSaleAction`, `OperationalRouteAccessTest`, `CustomerErrorCodeTest`, `SaleResource`?**
  _High betweenness centrality (0.204) - this node is a cross-community bridge._
- **Why does `Controller` connect `ShowAdminShiftReportController.php` to `User`, `PromotionErrorCodeTest`, `StoreContext`, `AttachCustomerToSaleAction`, `CashShiftRepository.php`, `CatalogRepository.php`, `FormRequest`, `RefundsReturnsRepository.php`, `ListStoreInventoryRequest`, `CatalogRepositoryInterface.php`, `InventoryRepositoryInterface.php`, `ShowCurrentUserController.php`, `StoreDomainException`, `RemoveSaleLineController.php`, `ResumeSaleController.php`, `CreateSaleAction`, `SaleErrorCodeTest`, `OperationalPosController.php`, `UpdateProductAction`, `CatalogRepositoryInterface.php`, `SaleResource`, `Model`, `ShowCategoryAction`, `ShowProductAction`, `AdminRouteAccessTest`, `LoginTest`, `OperationalRouteAccessTest`, `UpdateCustomerAction`, `ListCategoriesAction`, `DeleteCategoryAction`, `ShowCategoryAction`, `CurrentCashShiftController.php`, `ShowProductAction`, `CatalogResource`, `UpdateSaleLineAction`, `ListCustomersAction`, `CustomerResource`, `CreateSaleAction`, `FindCustomerByCpfAction`, `ShowCustomerAction`, `CustomerErrorCodeTest`, `ResumeSaleController.php`, `ListStoresTest`, `ListPromotionsAction`, `SaleResource`, `Promotion.php`, `ApplyPromotionToSaleAction`, `SalePaymentValidator`, `Sale.php`?**
  _High betweenness centrality (0.159) - this node is a cross-community bridge._
- **Why does `Sale` connect `Sale` to `User`, `PromotionDiscountCalculator`, `RemovePromotionFromSaleController.php`, `HasFactory`, `AttachCustomerToSaleAction`, `CashShiftRepository.php`, `HoldSaleAction`, `RemoveSaleLineAction`, `PaymentsRepository.php`, `InventoryRepository.php`, `RefundsReturnsRepositoryInterface.php`, `AdminStoreAccessIdorTest`, `CompleteSaleTest`, `SalesCartTest`, `Category`, `CreateProductAction`, `SaleErrorCodeTest`, `HoldSaleAction`, `ShowAdminSaleAction`, `SalesRepository`, `AppServiceProvider.php`, `CloseCashShiftTest`, `DeleteCategoryAction`, `CreateSaleAction`, `ShowSaleAction`, `PaymentMethod`, `SaleResource`?**
  _High betweenness centrality (0.090) - this node is a cross-community bridge._
- **Are the 159 inferred relationships involving `User` (e.g. with `.execute()` and `.handle()`) actually correct?**
  _`User` has 159 INFERRED edges - model-reasoned connections that need verification._
- **Are the 103 inferred relationships involving `Store` (e.g. with `.definition()` and `.definition()`) actually correct?**
  _`Store` has 103 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Sale` (e.g. with `.buildClosingReport()` and `.manager_dashboard_returns_parallel_kpi_metrics()`) actually correct?**
  _`Sale` has 21 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _68 weakly-connected nodes found - possible documentation gaps or missing edges._