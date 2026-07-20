# Graph Report - projects\pdv\backend  (2026-07-19)

## Corpus Check
- 398 files · ~53,220 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2092 nodes · 4109 edges · 203 communities (161 shown, 42 thin omitted)
- Extraction: 86% EXTRACTED · 14% INFERRED · 0% AMBIGUOUS · INFERRED: 576 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `87fe5def`
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
- AnalyticsRepositoryInterface.php
- CashShiftRepositoryInterface.php
- CatalogRepositoryInterface.php
- CustomersRepositoryInterface.php
- InventoryRepositoryInterface.php
- PaymentsRepositoryInterface.php
- PromotionsRepositoryInterface.php
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
- CashShiftRepositoryInterface.php
- RemoveSaleLineController.php
- ResumeSaleController.php
- CustomersRepositoryInterface.php
- FormRequest
- UsersRepository
- StoreInventoryFactory
- ShiftClosingReport
- AuthenticationDomainException
- StoreRepository
- SelectStoreContextTest
- ListAdminAuditLogsAction
- ExampleTest
- CustomerCrudTest
- AdminInventoryTest
- AdminRouteAccessTest
- RemovePromotionFromSaleController.php
- AdminInventoryTest
- ActsWithOperationalSession.php
- actingAsManagerForStore

## God Nodes (most connected - your core abstractions)
1. `User` - 279 edges
2. `Store` - 140 edges
3. `Controller` - 119 edges
4. `TestCase` - 115 edges
5. `Sale` - 97 edges
6. `Product` - 94 edges
7. `Customer` - 53 edges
8. `Money` - 48 edges
9. `Promotion` - 47 edges
10. `AssertManagerStoreAccess` - 43 edges

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

## Communities (203 total, 42 thin omitted)

### Community 0 - "User"
Cohesion: 0.10
Nodes (4): CustomerStoreStat, BelongsTo, AdminAnalyticsTest, AdminStoreAccessIdorTest

### Community 1 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+5 more)

### Community 2 - "TestCase"
Cohesion: 0.43
Nodes (5): EnsureOpenCashShift, CashShiftRepositoryInterface, Closure, Request, Response

### Community 3 - "Controller"
Cohesion: 0.10
Nodes (12): PaymentRequest, PaymentResult, RefundRequest, RefundResult, charge(), refund(), StubPaymentGateway, LogSaleAnalyticsRecorder (+4 more)

### Community 4 - "StoreContext"
Cohesion: 0.05
Nodes (20): CreateUserAction, UsersRepositoryInterface, ListUsersAction, UsersRepositoryInterface, UsersRepositoryInterface, ShowUserAction, UsersRepositoryInterface, UpdateUserAction (+12 more)

### Community 5 - "scripts"
Cohesion: 0.17
Nodes (12): scripts, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall, @php artisan key:generate --ansi (+4 more)

### Community 6 - "AuthenticationDomainException"
Cohesion: 0.16
Nodes (4): BelongsToMany, HasMany, static, PromotionFactory

### Community 7 - "UserFactory"
Cohesion: 0.06
Nodes (16): CashShiftFactory, static, CategoryFactory, static, CustomerFactory, static, ProductFactory, static (+8 more)

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
Cohesion: 0.20
Nodes (6): DatabaseSeeder, DemoStoreSeeder, ManagerUserSeeder, OperatorUserSeeder, Seeder, WithoutModelEvents

### Community 15 - "AnalyticsRepositoryInterface"
Cohesion: 0.16
Nodes (8): AddSaleLineAction, CatalogRepositoryInterface, SalesRepositoryInterface, CreateSaleAction, SalesRepositoryInterface, CreateSaleController, JsonResponse, CreateSaleRequest

### Community 16 - "CashShiftRepository.php"
Cohesion: 0.07
Nodes (21): OpenCashShiftAction, CashShiftRepositoryInterface, ShiftClosingReport, buildClosingReport(), close(), createOpen(), findById(), findOpenForUser() (+13 more)

### Community 17 - "CatalogRepository.php"
Cohesion: 0.19
Nodes (6): ListAdminShiftsAction, CashShiftRepositoryInterface, Collection, ListAdminShiftsController, JsonResponse, ListAdminShiftsRequest

### Community 18 - "CustomersRepository.php"
Cohesion: 0.18
Nodes (5): CustomersRepository, Collection, Customer, HasMany, CustomersRepositoryInterface

### Community 20 - "InventoryRepository.php"
Cohesion: 0.05
Nodes (22): AdjustStoreInventoryAction, AuditLogRepositoryInterface, CatalogRepositoryInterface, InventoryRepositoryInterface, adjustQuantity(), findForStoreProduct(), listForStore(), mapForStoreProducts() (+14 more)

### Community 21 - "PaymentsRepository.php"
Cohesion: 0.16
Nodes (5): SalesRepositoryInterface, RemoveSaleLineAction, SalesRepositoryInterface, ResumeSaleAction, SaleCartGuard

### Community 22 - "PromotionsRepository.php"
Cohesion: 0.12
Nodes (6): PromotionAuditSnapshot, Collection, PromotionsRepository, Promotion, DateTimeInterface, PromotionsRepositoryInterface

### Community 23 - "RefundsReturnsRepository.php"
Cohesion: 0.21
Nodes (5): GetAdminDashboardMetricsAction, AdminDashboardMetrics, AdminDashboardController, JsonResponse, Request

### Community 24 - "SalesRepository.php"
Cohesion: 0.12
Nodes (11): createCategory(), createProduct(), deleteCategory(), deleteProduct(), findCategoryById(), findProductById(), listCategories(), listProducts() (+3 more)

### Community 32 - "AnalyticsRepositoryInterface.php"
Cohesion: 0.16
Nodes (3): Category, HasMany, CategoryCrudTest

### Community 34 - "CatalogRepositoryInterface.php"
Cohesion: 0.33
Nodes (4): CloseCashShiftAction, CashShiftRepositoryInterface, CloseCashShiftController, JsonResponse

### Community 35 - "CustomersRepositoryInterface.php"
Cohesion: 0.19
Nodes (6): ListCampaignCustomersAction, AnalyticsRepositoryInterface, Collection, ListCampaignCustomersController, JsonResponse, ListCampaignCustomersRequest

### Community 37 - "InventoryRepositoryInterface.php"
Cohesion: 0.21
Nodes (6): ListAdminSalesAction, Collection, SalesRepositoryInterface, ListAdminSalesController, JsonResponse, ListAdminSalesRequest

### Community 38 - "PaymentsRepositoryInterface.php"
Cohesion: 0.12
Nodes (9): AnalyticsRepositoryInterface, AdminAnalyticsSnapshot, CustomerSpendRow, adminSnapshot(), listCampaignCustomers(), Collection, AnalyticsRepository, Collection (+1 more)

### Community 39 - "PromotionsRepositoryInterface.php"
Cohesion: 0.16
Nodes (8): SalesRepositoryInterface, ShowSaleAction, AddSaleLineController, JsonResponse, JsonResponse, Request, ShowSaleController, AddSaleLineRequest

### Community 57 - "CreateSaleAction"
Cohesion: 0.20
Nodes (5): ListProductsAction, CatalogRepositoryInterface, ListProductsController, JsonResponse, ListProductsRequest

### Community 61 - "Sale"
Cohesion: 0.21
Nodes (6): AuditLogRepositoryInterface, CatalogRepositoryInterface, UpdateProductAction, JsonResponse, UpdateProductController, UpdateProductRequest

### Community 62 - "Category"
Cohesion: 0.22
Nodes (5): GetAdminAnalyticsAction, AnalyticsRepositoryInterface, GetAdminAnalyticsController, JsonResponse, GetAdminAnalyticsRequest

### Community 63 - "CreateProductAction"
Cohesion: 0.33
Nodes (5): LogoutUserAction, Request, LogoutController, JsonResponse, Request

### Community 66 - "SaleErrorCodeTest"
Cohesion: 0.21
Nodes (5): CloseCashShiftRequest, ApplyPromotionToSaleController, JsonResponse, ApplyPromotionToSaleRequest, FormRequest

### Community 73 - "UpdateProductAction"
Cohesion: 0.07
Nodes (13): AdminAuditFilters, AuditLogEntry, append(), listForAdmin(), ListAdminAuditLogsController, JsonResponse, ListAdminAuditLogsRequest, AuditLogRepository (+5 more)

### Community 74 - "CatalogRepositoryInterface.php"
Cohesion: 0.22
Nodes (5): CatalogRepositoryInterface, UpdateCategoryAction, JsonResponse, UpdateCategoryController, UpdateCategoryRequest

### Community 75 - "SaleResource"
Cohesion: 0.20
Nodes (6): ListOperationalProductsAction, CatalogRepositoryInterface, InventoryRepositoryInterface, ListOperationalProductsController, JsonResponse, ListOperationalProductsRequest

### Community 76 - "SalesRepository"
Cohesion: 0.10
Nodes (8): hold(), Collection, SalesRepository, BelongsTo, HasMany, Sale, HasOne, SalesRepositoryInterface

### Community 77 - "Model"
Cohesion: 0.22
Nodes (5): CreateCategoryAction, CatalogRepositoryInterface, CreateCategoryController, JsonResponse, StoreCategoryRequest

### Community 78 - "ListCategoriesAction"
Cohesion: 0.25
Nodes (4): BelongsTo, HasMany, Refund, RefundResource

### Community 79 - "ShowCategoryAction"
Cohesion: 0.21
Nodes (5): CreateProductAction, CatalogRepositoryInterface, CreateProductController, JsonResponse, StoreProductRequest

### Community 80 - "ShowProductAction"
Cohesion: 0.31
Nodes (5): ListAccessibleStoresAction, StoreRepositoryInterface, ListStoresController, JsonResponse, Request

### Community 81 - "AppServiceProvider.php"
Cohesion: 0.15
Nodes (6): generateForSale(), StubFiscalReceiptGenerator, FiscalReceipt, BelongsTo, SaleResource, FiscalReceiptGeneratorInterface

### Community 82 - "StoreRepository"
Cohesion: 0.08
Nodes (5): Product, AdminAuditLogTest, ProductCrudTest, AdminInventoryTest, ApplyPromotionToSaleTest

### Community 83 - "AdminRouteAccessTest"
Cohesion: 0.33
Nodes (4): PromotionsRepositoryInterface, ShowPromotionAction, JsonResponse, ShowPromotionController

### Community 84 - "LoginTest"
Cohesion: 0.12
Nodes (12): attachToSale(), create(), findApplied(), findByCode(), findById(), list(), listAppliedForSale(), Collection (+4 more)

### Community 85 - "OperationalRouteAccessTest"
Cohesion: 0.27
Nodes (6): CreatePromotionAction, AuditLogRepositoryInterface, PromotionsRepositoryInterface, CreatePromotionController, JsonResponse, StorePromotionRequest

### Community 86 - "UpdateCustomerAction"
Cohesion: 0.24
Nodes (5): CustomersRepositoryInterface, UpdateCustomerAction, JsonResponse, UpdateCustomerController, UpdateCustomerRequest

### Community 87 - "SaleStatus.php"
Cohesion: 0.14
Nodes (13): Request, StoreRepositoryInterface, SelectStoreContextAction, OperationalPosController, JsonResponse, Request, EnsureStoreContext, Closure (+5 more)

### Community 88 - "CloseCashShiftTest"
Cohesion: 0.12
Nodes (17): AdminSaleFilters, addLine(), attachCustomer(), complete(), createInProgress(), findById(), findLineById(), findLineByProduct() (+9 more)

### Community 92 - "ListCategoriesAction"
Cohesion: 0.53
Nodes (3): ListCategoriesAction, CatalogRepositoryInterface, Collection

### Community 93 - "ResumeSaleAction"
Cohesion: 0.22
Nodes (9): dev, serve, test, Composer\\Config::disableProcessTimeout, npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74,#86efac\" \"php artisan serve\" \"php artisan queue:listen redis --tries=3 --timeout=0\" \"php artisan reverb:start\" \"php artisan pail --timeout=0\" \"npm run dev\" --names=server,queue,reverb,logs,vite --kill-others, @php artisan config:clear --ansi @no_additional_args, @php artisan serve --host=127.0.0.1 --port=8000, @php artisan test (+1 more)

### Community 94 - "DeleteCategoryAction"
Cohesion: 0.31
Nodes (6): PromotionsRepositoryInterface, SalesRepositoryInterface, RemovePromotionFromSaleAction, JsonResponse, Request, RemovePromotionFromSaleController

### Community 95 - "DeleteProductAction"
Cohesion: 0.28
Nodes (3): Collection, RefundsReturnsRepository, RefundsReturnsRepositoryInterface

### Community 96 - "ShowCategoryAction"
Cohesion: 0.33
Nodes (4): CatalogRepositoryInterface, ShowCategoryAction, JsonResponse, ShowCategoryController

### Community 97 - "CurrentCashShiftController.php"
Cohesion: 0.14
Nodes (13): CurrentCashShiftController, CashShiftRepositoryInterface, JsonResponse, Request, ListCategoriesController, JsonResponse, Controller, JsonResponse (+5 more)

### Community 98 - "ShowProductAction"
Cohesion: 0.33
Nodes (4): CatalogRepositoryInterface, ShowProductAction, JsonResponse, ShowProductController

### Community 99 - "CatalogResource"
Cohesion: 0.29
Nodes (7): ListRefundsForSaleAction, Collection, RefundsReturnsRepositoryInterface, SalesRepositoryInterface, ListRefundsForSaleController, JsonResponse, Request

### Community 102 - "UpdateSaleLineAction"
Cohesion: 0.24
Nodes (6): AuditLogRepositoryInterface, PromotionsRepositoryInterface, UpdatePromotionAction, JsonResponse, UpdatePromotionController, UpdatePromotionRequest

### Community 104 - "ListCustomersAction"
Cohesion: 0.20
Nodes (5): ListCustomersAction, CustomersRepositoryInterface, ListCustomersController, JsonResponse, ListCustomersRequest

### Community 105 - "CustomerResource"
Cohesion: 0.22
Nodes (5): CreateCustomerAction, CustomersRepositoryInterface, CreateCustomerController, JsonResponse, StoreCustomerRequest

### Community 106 - "CreateSaleAction"
Cohesion: 0.46
Nodes (3): ApplyPromotionToSaleAction, PromotionsRepositoryInterface, SalesRepositoryInterface

### Community 107 - "ShowSaleAction"
Cohesion: 0.25
Nodes (5): DispatchSaleSideEffects, BusOrchestrator, Batch, PendingBatch, PendingChain

### Community 108 - "FindCustomerByCpfAction"
Cohesion: 0.31
Nodes (5): FindCustomerByCpfAction, CustomersRepositoryInterface, FindOperationalCustomerController, JsonResponse, Request

### Community 109 - "ShowCustomerAction"
Cohesion: 0.23
Nodes (5): CustomersRepositoryInterface, ShowCustomerAction, JsonResponse, ShowCustomerController, CustomerResource

### Community 110 - "OperationalRouteAccessTest"
Cohesion: 0.11
Nodes (11): BaseTestCase, RefreshDatabase, AdminDashboardMetricsTest, ListAdminNotificationsTest, AdminCatalogAccessTest, ExampleTest, CorrelationIdMiddlewareTest, ListStoresTest (+3 more)

### Community 111 - "CustomerErrorCodeTest"
Cohesion: 0.29
Nodes (6): ApiErrorResponse, ErrorCode, JsonResponse, Request, AuthenticationException, AuthorizationException

### Community 112 - "ResumeSaleController.php"
Cohesion: 0.32
Nodes (3): CompleteSaleController, JsonResponse, CompleteSaleRequest

### Community 113 - "ListStoresTest"
Cohesion: 0.22
Nodes (5): SalesRepositoryInterface, UpdateSaleLineAction, JsonResponse, UpdateSaleLineController, UpdateSaleLineRequest

### Community 117 - "ListPromotionsAction"
Cohesion: 0.20
Nodes (5): ListPromotionsAction, PromotionsRepositoryInterface, ListPromotionsController, JsonResponse, ListPromotionsRequest

### Community 118 - "PaymentMethod"
Cohesion: 0.36
Nodes (4): ShowCurrentUserAction, JsonResponse, Request, ShowCurrentUserController

### Community 119 - "SaleResource"
Cohesion: 0.33
Nodes (6): CreateRefundAction, AuditLogRepositoryInterface, InventoryRepositoryInterface, PaymentGatewayInterface, RefundsReturnsRepositoryInterface, RefundType

### Community 120 - "Promotion.php"
Cohesion: 0.10
Nodes (10): ActsWithAdminStoreAccess, PaymentsRepository, PaymentLine, BelongsTo, PaymentMethod, PaymentsRepositoryInterface, AdminShiftReportTest, ListAdminSalesTest (+2 more)

### Community 121 - "ApplyPromotionToSaleAction"
Cohesion: 0.29
Nodes (6): ListHeldSalesAction, Collection, SalesRepositoryInterface, ListHeldSalesController, JsonResponse, Request

### Community 123 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, laravel/telescope, mockery/mockery, nunomaduro/collision (+1 more)

### Community 124 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 126 - "ListStoresTest"
Cohesion: 0.32
Nodes (3): CreateRefundController, JsonResponse, StoreRefundRequest

### Community 127 - "Sale.php"
Cohesion: 0.22
Nodes (5): HoldSaleAction, SalesRepositoryInterface, HoldSaleController, JsonResponse, HoldSaleRequest

### Community 128 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 129 - "ApiErrorResponse.php"
Cohesion: 0.27
Nodes (4): LoginUserAction, LoginController, JsonResponse, LoginRequest

### Community 130 - "PromotionDiscountCalculator"
Cohesion: 0.10
Nodes (5): Collection, PromotionDiscountCalculator, Money, PromotionResource, MoneyTest

### Community 131 - "PromotionErrorCodeTest"
Cohesion: 0.06
Nodes (4): Store, OperationalCustomerTest, CompleteSaleTest, SalesCartTest

### Community 132 - "RemovePromotionFromSaleController.php"
Cohesion: 0.61
Nodes (6): CompleteSaleAction, FiscalReceiptGeneratorInterface, InventoryRepositoryInterface, PaymentGatewayInterface, PaymentsRepositoryInterface, SalesRepositoryInterface

### Community 133 - "autoload-dev"
Cohesion: 0.13
Nodes (10): ActsWithOperationalSession, InteractsWithStatefulApi, OperationalRouteAccessTest, CloseCashShiftTest, OpenCashShiftTest, OperationalShiftGateTest, InventorySaleTest, CompleteSaleSideEffectsTest (+2 more)

### Community 134 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 135 - "require"
Cohesion: 0.25
Nodes (8): require, laravel/framework, laravel/horizon, laravel/pulse, laravel/reverb, laravel/sanctum, laravel/tinker, php

### Community 137 - "post-create-project-cmd"
Cohesion: 0.29
Nodes (5): create(), findById(), list(), Collection, update()

### Community 139 - "AssignCorrelationId.php"
Cohesion: 0.53
Nodes (4): AssignCorrelationId, Closure, Request, Response

### Community 142 - "AttachCustomerToSaleAction"
Cohesion: 0.22
Nodes (6): AttachCustomerToSaleAction, CustomersRepositoryInterface, SalesRepositoryInterface, AttachCustomerToSaleController, JsonResponse, AttachCustomerToSaleRequest

### Community 144 - "HoldSaleAction"
Cohesion: 0.15
Nodes (3): CatalogRepository, Collection, CatalogRepositoryInterface

### Community 145 - "AssertManagerStoreAccess"
Cohesion: 0.22
Nodes (7): ListAdminAuditLogsAction, AuditLogRepositoryInterface, AssertManagerStoreAccess, StoreRepositoryInterface, ListAdminNotificationsController, JsonResponse, Request

### Community 147 - "DeleteCategoryAction"
Cohesion: 0.33
Nodes (4): create(), findCompletedSale(), listForSale(), Collection

### Community 148 - "RemoveSaleLineAction"
Cohesion: 0.32
Nodes (3): JsonResponse, SelectStoreContextController, SelectStoreContextRequest

### Community 150 - "AuthenticationDomainException"
Cohesion: 0.31
Nodes (5): GetAdminShiftReportAction, CashShiftRepositoryInterface, JsonResponse, Request, ShowAdminShiftReportController

### Community 151 - "ResumeSaleController.php"
Cohesion: 0.50
Nodes (4): extra, laravel, dont-discover, laravel/telescope

### Community 152 - "2026_07_16_165219_create_telescope_entries_table.php"
Cohesion: 0.83
Nodes (3): down(), getConnection(), up()

### Community 157 - "ListStoreInventoryRequest"
Cohesion: 0.19
Nodes (6): ListStoreInventoryAction, Collection, InventoryRepositoryInterface, ListStoreInventoryController, JsonResponse, ListStoreInventoryRequest

### Community 160 - "SaleCompletedNotificationTest"
Cohesion: 0.19
Nodes (5): BelongsTo, BelongsTo, RefundLine, BelongsToMany, Model

### Community 162 - "UsersRepository"
Cohesion: 0.31
Nodes (6): AuditLogRepositoryInterface, CashShiftRepositoryInterface, ReopenCashShiftAction, JsonResponse, Request, ReopenCashShiftController

### Community 176 - "ShowCurrentUserController.php"
Cohesion: 0.33
Nodes (4): DeleteCategoryAction, CatalogRepositoryInterface, DeleteCategoryController, JsonResponse

### Community 177 - "EnsureStoreContext.php"
Cohesion: 0.31
Nodes (5): SalesRepositoryInterface, ShowAdminSaleAction, JsonResponse, Request, ShowAdminSaleController

### Community 178 - "StoreDomainException"
Cohesion: 0.33
Nodes (4): DeleteProductAction, CatalogRepositoryInterface, DeleteProductController, JsonResponse

### Community 184 - "CustomersRepositoryInterface.php"
Cohesion: 0.22
Nodes (6): create(), findByCpf(), findById(), list(), Collection, update()

### Community 186 - "UsersRepository"
Cohesion: 0.23
Nodes (3): Collection, UsersRepository, UsersRepositoryInterface

### Community 187 - "StoreInventoryFactory"
Cohesion: 0.06
Nodes (10): assignedStoreIds(), findById(), listAccessibleForUser(), userCanAccessStore(), User, Authenticatable, AdminUserManagementTest, ReopenCashShiftTest (+2 more)

### Community 192 - "AuthenticationDomainException"
Cohesion: 0.53
Nodes (4): EnsureUserHasRole, Closure, Request, Response

### Community 194 - "SelectStoreContextTest"
Cohesion: 0.27
Nodes (7): CashShiftDomainException, ErrorCode, CatalogDomainException, ErrorCode, ErrorCode, StoreDomainException, DomainException

### Community 204 - "ActsWithOperationalSession.php"
Cohesion: 0.70
Nodes (4): actingAsOperatorAtStore(), actingAsOperatorWithOpenShift(), static, withOpenShift()

### Community 206 - "actingAsManagerForStore"
Cohesion: 0.67
Nodes (3): actingAsManagerForStore(), attachManagerToStore(), static

## Knowledge Gaps
- **68 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+63 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **42 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `StoreInventoryFactory` to `User`, `ApiErrorResponse.php`, `PromotionErrorCodeTest`, `StoreContext`, `autoload-dev`, `UserFactory`, `post-create-project-cmd`, `AppServiceProvider.php`, `DatabaseSeeder.php`, `AnalyticsRepositoryInterface`, `CashShiftRepository.php`, `AssertManagerStoreAccess`, `CatalogRepository.php`, `CustomersRepository.php`, `InventoryRepository.php`, `FormRequest`, `AuthenticationDomainException`, `RefundsReturnsRepository.php`, `PromotionsRepository.php`, `ListStoreInventoryRequest`, `SaleCompletedNotification`, `AnalyticsRepositoryInterface.php`, `CatalogRepositoryInterface.php`, `UsersRepository`, `InventoryRepositoryInterface.php`, `PaymentsRepositoryInterface.php`, `RefundsReturnsRepositoryInterface.php`, `AppServiceProvider.php`, `SaleErrorCodeTest`, `AdminStoreAccessIdorTest`, `EnsureStoreContext.php`, `SalesCartTest`, `ShowAdminShiftReportController.php`, `FormRequest`, `UsersRepository`, `Sale`, `Category`, `StoreRepository`, `CustomerCrudTest`, `AdminRouteAccessTest`, `UpdateProductAction`, `SalesRepository`, `ActsWithOperationalSession.php`, `actingAsManagerForStore`, `ShowProductAction`, `StoreRepository`, `OperationalRouteAccessTest`, `SaleStatus.php`, `CatalogResource`, `UpdateSaleLineAction`, `PaymentMethod`, `SaleResource`, `Promotion.php`?**
  _High betweenness centrality (0.225) - this node is a cross-community bridge._
- **Why does `Controller` connect `CurrentCashShiftController.php` to `ApiErrorResponse.php`, `StoreContext`, `AttachCustomerToSaleAction`, `AnalyticsRepositoryInterface`, `CashShiftRepository.php`, `CatalogRepository.php`, `AssertManagerStoreAccess`, `InventoryRepository.php`, `RemoveSaleLineAction`, `AuthenticationDomainException`, `RefundsReturnsRepository.php`, `ListStoreInventoryRequest`, `CatalogRepositoryInterface.php`, `CustomersRepositoryInterface.php`, `UsersRepository`, `InventoryRepositoryInterface.php`, `PromotionsRepositoryInterface.php`, `ShowCurrentUserController.php`, `EnsureStoreContext.php`, `StoreDomainException`, `CreateSaleAction`, `Sale`, `Category`, `CreateProductAction`, `SaleErrorCodeTest`, `UpdateProductAction`, `CatalogRepositoryInterface.php`, `SaleResource`, `Model`, `ShowCategoryAction`, `ShowProductAction`, `AdminRouteAccessTest`, `OperationalRouteAccessTest`, `UpdateCustomerAction`, `SaleStatus.php`, `DeleteCategoryAction`, `ShowCategoryAction`, `ShowProductAction`, `CatalogResource`, `UpdateSaleLineAction`, `ListCustomersAction`, `CustomerResource`, `FindCustomerByCpfAction`, `ShowCustomerAction`, `ResumeSaleController.php`, `ListStoresTest`, `ListPromotionsAction`, `PaymentMethod`, `ApplyPromotionToSaleAction`, `ListStoresTest`, `Sale.php`?**
  _High betweenness centrality (0.137) - this node is a cross-community bridge._
- **Why does `TestCase` connect `OperationalRouteAccessTest` to `User`, `PromotionDiscountCalculator`, `PromotionErrorCodeTest`, `autoload-dev`, `AuthErrorCodeTest`, `StoreErrorCodeTest`, `RefundErrorCodeTest`, `InventoryRepository.php`, `FormRequest`, `AnalyticsRepositoryInterface.php`, `BusOrchestratorTest`, `CashShiftRepositoryInterface.php`, `InventoryErrorCodeTest`, `PromotionErrorCodeTest`, `RefundsReturnsRepositoryInterface.php`, `AppServiceProvider.php`, `SaleErrorCodeTest`, `SalesRepositoryInterface.php`, `AdminStoreAccessIdorTest`, `DispatchSaleSideEffectsTest`, `CompleteSaleTest`, `SalesCartTest`, `ShowAdminShiftReportController.php`, `StoreInventoryFactory`, `CatalogErrorCodeTest`, `InventoryErrorCodeTest`, `CustomerCrudTest`, `AdminRouteAccessTest`, `StoreRepository`, `ShowSaleAction`, `Promotion.php`, `SalePaymentValidator`?**
  _High betweenness centrality (0.089) - this node is a cross-community bridge._
- **Are the 189 inferred relationships involving `User` (e.g. with `.execute()` and `.handle()`) actually correct?**
  _`User` has 189 INFERRED edges - model-reasoned connections that need verification._
- **Are the 120 inferred relationships involving `Store` (e.g. with `.topCustomersBySpend()` and `.definition()`) actually correct?**
  _`Store` has 120 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Sale` (e.g. with `.buildClosingReport()` and `.manager_dashboard_returns_parallel_kpi_metrics()`) actually correct?**
  _`Sale` has 21 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _68 weakly-connected nodes found - possible documentation gaps or missing edges._