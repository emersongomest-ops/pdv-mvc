# Graph Report - projects\pdv\backend  (2026-07-20)

## Corpus Check
- 445 files · ~60,339 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2356 nodes · 4614 edges · 205 communities (166 shown, 39 thin omitted)
- Extraction: 86% EXTRACTED · 14% INFERRED · 0% AMBIGUOUS · INFERRED: 637 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `ba4f0fdf`
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
- AdminStoreAccessIdorTest
- CustomersRepositoryInterface.php
- AdjustStoreInventoryAction
- RefundsReturnsRepositoryInterface.php
- StoreInventoryFactory
- StoreRepository
- AdminAuditLogTest
- SelectStoreContextTest
- SelectStoreContextTest
- ListAdminAuditLogsAction
- ListAdminShiftsAction
- OperationalStoreContextTest
- HoldSaleAction
- RemoveSaleLineAction
- CatalogResource
- EnsureUserHasRole.php

## God Nodes (most connected - your core abstractions)
1. `User` - 284 edges
2. `Store` - 148 edges
3. `TestCase` - 129 edges
4. `Controller` - 125 edges
5. `Product` - 99 edges
6. `Sale` - 99 edges
7. `Customer` - 64 edges
8. `Money` - 50 edges
9. `Promotion` - 50 edges
10. `AssertManagerStoreAccess` - 43 edges

## Surprising Connections (you probably didn't know these)
- `findProductById()` --references--> `Product`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Product.php
- `createProduct()` --references--> `Product`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Product.php
- `updateProduct()` --references--> `Product`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Product.php
- `deleteProduct()` --references--> `Product`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Product.php
- `findById()` --references--> `Customer`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Customers/Repositories/CustomersRepositoryInterface.php → projects/pdv/backend/app/Models/Customer.php

## Import Cycles
- None detected.

## Communities (205 total, 39 thin omitted)

### Community 0 - "User"
Cohesion: 0.22
Nodes (5): SalesRepositoryInterface, UpdateSaleLineAction, JsonResponse, UpdateSaleLineController, UpdateSaleLineRequest

### Community 1 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+5 more)

### Community 2 - "TestCase"
Cohesion: 0.43
Nodes (5): EnsureOpenCashShift, CashShiftRepositoryInterface, Closure, Request, Response

### Community 3 - "Controller"
Cohesion: 0.08
Nodes (18): PaymentRequest, PaymentMethod, PaymentResult, RefundRequest, RefundResult, charge(), PaymentChargeStatus, queryChargeStatus() (+10 more)

### Community 4 - "StoreContext"
Cohesion: 0.13
Nodes (6): CardInstrumentFormatGuard, assertValidForCharge(), CardInstrument, NotImplementedCardInstrumentValidator, CardInstrumentValidatorInterface, CardInstrumentFormatGuardTest

### Community 5 - "scripts"
Cohesion: 0.17
Nodes (12): scripts, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall, @php artisan key:generate --ansi (+4 more)

### Community 6 - "AuthenticationDomainException"
Cohesion: 0.15
Nodes (3): PiiCrypto, DemoPromotionSeeder, Encrypter

### Community 7 - "UserFactory"
Cohesion: 0.05
Nodes (18): CashShiftFactory, static, CategoryFactory, static, CustomerFactory, static, ProductFactory, static (+10 more)

### Community 8 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 9 - "DomainScaffolder"
Cohesion: 0.06
Nodes (25): ConsumePaymentWebhookAction, PaymentsRepositoryInterface, PaymentWebhookPayloadNormalizerInterface, PaymentWebhookSignatureVerifierInterface, PendingPaymentOutboxInterface, ErrorCode, PaymentGatewayInterface, PaymentsRepositoryInterface (+17 more)

### Community 10 - "AppServiceProvider.php"
Cohesion: 0.23
Nodes (9): NotifyManagersOfSaleCompleted, SaleCompleted, AbstractQueuedJob, Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels (+1 more)

### Community 11 - "ListAccessibleStoresAction"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 13 - "StoreErrorCodeTest"
Cohesion: 0.24
Nodes (4): PaymentErrorCodeTest, ErrorCode, ErrorCode, StoreErrorCodeTest

### Community 14 - "DatabaseSeeder.php"
Cohesion: 0.05
Nodes (20): adjustQuantity(), findForStoreProduct(), listForStore(), mapForStoreProducts(), Collection, InventoryRepository, Collection, BelongsTo (+12 more)

### Community 15 - "AnalyticsRepositoryInterface"
Cohesion: 0.14
Nodes (7): HasMany, BelongsTo, BelongsToMany, HasMany, BelongsToMany, HasFactory, Model

### Community 16 - "CashShiftRepository.php"
Cohesion: 0.07
Nodes (21): OpenCashShiftAction, CashShiftRepositoryInterface, ShiftClosingReport, buildClosingReport(), close(), createOpen(), findById(), findOpenForUser() (+13 more)

### Community 17 - "CatalogRepository.php"
Cohesion: 0.28
Nodes (3): ListAdminShiftsController, JsonResponse, ListAdminShiftsRequest

### Community 18 - "CustomersRepository.php"
Cohesion: 0.21
Nodes (3): CustomersRepository, Collection, CustomersRepositoryInterface

### Community 20 - "InventoryRepository.php"
Cohesion: 0.12
Nodes (6): PendingPaymentOutboxEntry, self, push(), InMemoryPendingPaymentOutbox, RedisPendingPaymentOutbox, PendingPaymentOutboxInterface

### Community 21 - "PaymentsRepository.php"
Cohesion: 0.22
Nodes (3): SalesRepositoryInterface, ResumeSaleAction, SaleCartGuard

### Community 22 - "PromotionsRepository.php"
Cohesion: 0.10
Nodes (7): PromotionAuditSnapshot, Collection, PromotionsRepository, DateTimeInterface, Promotion, PromotionsRepositoryInterface, ApplyPromotionToSaleTest

### Community 23 - "RefundsReturnsRepository.php"
Cohesion: 0.19
Nodes (5): GetAdminDashboardMetricsAction, AdminDashboardMetrics, AdminDashboardController, JsonResponse, Request

### Community 24 - "SalesRepository.php"
Cohesion: 0.11
Nodes (12): createCategory(), createProduct(), deleteCategory(), deleteProduct(), findCategoryById(), findProductById(), listCategories(), listProducts() (+4 more)

### Community 32 - "AnalyticsRepositoryInterface.php"
Cohesion: 0.12
Nodes (6): self, WebhookRetryItem, push(), InMemoryWebhookRetryQueue, RedisWebhookRetryQueue, WebhookRetryQueueInterface

### Community 34 - "CatalogRepositoryInterface.php"
Cohesion: 0.27
Nodes (6): Model, PiiEncryptedDate, Model, PiiEncryptedString, CarbonInterface, CastsAttributes

### Community 35 - "CustomersRepositoryInterface.php"
Cohesion: 0.19
Nodes (6): ListCampaignCustomersAction, AnalyticsRepositoryInterface, Collection, ListCampaignCustomersController, JsonResponse, ListCampaignCustomersRequest

### Community 37 - "InventoryRepositoryInterface.php"
Cohesion: 0.28
Nodes (3): ListAdminSalesController, JsonResponse, ListAdminSalesRequest

### Community 38 - "PaymentsRepositoryInterface.php"
Cohesion: 0.12
Nodes (9): AnalyticsRepositoryInterface, AdminAnalyticsSnapshot, CustomerSpendRow, adminSnapshot(), listCampaignCustomers(), Collection, AnalyticsRepository, Collection (+1 more)

### Community 39 - "PromotionsRepositoryInterface.php"
Cohesion: 0.14
Nodes (10): AddSaleLineAction, CatalogRepositoryInterface, SalesRepositoryInterface, CreateSaleAction, SalesRepositoryInterface, SalesRepositoryInterface, ShowSaleAction, AddSaleLineController (+2 more)

### Community 57 - "CreateSaleAction"
Cohesion: 0.20
Nodes (5): ListProductsAction, CatalogRepositoryInterface, ListProductsController, JsonResponse, ListProductsRequest

### Community 61 - "Sale"
Cohesion: 0.29
Nodes (6): ApiErrorResponse, ErrorCode, JsonResponse, Request, AuthenticationException, AuthorizationException

### Community 63 - "CreateProductAction"
Cohesion: 0.53
Nodes (3): ListAdminSalesAction, Collection, SalesRepositoryInterface

### Community 66 - "SaleErrorCodeTest"
Cohesion: 0.46
Nodes (3): ApplyPromotionToSaleAction, PromotionsRepositoryInterface, SalesRepositoryInterface

### Community 73 - "UpdateProductAction"
Cohesion: 0.05
Nodes (19): AuditLogRepositoryInterface, CatalogRepositoryInterface, UpdateProductAction, AdminAuditFilters, AuditLogEntry, append(), listForAdmin(), ListAdminAuditLogsController (+11 more)

### Community 74 - "CatalogRepositoryInterface.php"
Cohesion: 0.22
Nodes (5): CatalogRepositoryInterface, UpdateCategoryAction, JsonResponse, UpdateCategoryController, UpdateCategoryRequest

### Community 75 - "SaleResource"
Cohesion: 0.20
Nodes (6): ListOperationalProductsAction, CatalogRepositoryInterface, InventoryRepositoryInterface, ListOperationalProductsController, JsonResponse, ListOperationalProductsRequest

### Community 76 - "SalesRepository"
Cohesion: 0.10
Nodes (10): AdminSaleFilters, findLineById(), findLineByProduct(), removeLine(), updateLineQuantity(), Collection, SalesRepository, BelongsTo (+2 more)

### Community 77 - "Model"
Cohesion: 0.22
Nodes (5): CreateCategoryAction, CatalogRepositoryInterface, CreateCategoryController, JsonResponse, StoreCategoryRequest

### Community 79 - "ShowCategoryAction"
Cohesion: 0.21
Nodes (5): CreateProductAction, CatalogRepositoryInterface, CreateProductController, JsonResponse, StoreProductRequest

### Community 80 - "ShowProductAction"
Cohesion: 0.31
Nodes (5): ListAccessibleStoresAction, StoreRepositoryInterface, ListStoresController, JsonResponse, Request

### Community 81 - "AppServiceProvider.php"
Cohesion: 0.21
Nodes (5): generateForSale(), StubFiscalReceiptGenerator, FiscalReceipt, BelongsTo, FiscalReceiptGeneratorInterface

### Community 83 - "AdminRouteAccessTest"
Cohesion: 0.33
Nodes (4): PromotionsRepositoryInterface, ShowPromotionAction, JsonResponse, ShowPromotionController

### Community 84 - "LoginTest"
Cohesion: 0.11
Nodes (12): attachToSale(), create(), findApplied(), findByCode(), findById(), list(), listAppliedForSale(), Collection (+4 more)

### Community 85 - "OperationalRouteAccessTest"
Cohesion: 0.27
Nodes (6): CreatePromotionAction, AuditLogRepositoryInterface, PromotionsRepositoryInterface, CreatePromotionController, JsonResponse, StorePromotionRequest

### Community 86 - "UpdateCustomerAction"
Cohesion: 0.22
Nodes (5): CustomersRepositoryInterface, UpdateCustomerAction, JsonResponse, UpdateCustomerController, UpdateCustomerRequest

### Community 87 - "SaleStatus.php"
Cohesion: 0.10
Nodes (16): Request, StoreRepositoryInterface, SelectStoreContextAction, OperationalPosController, JsonResponse, Request, EnsureStoreContext, Closure (+8 more)

### Community 88 - "CloseCashShiftTest"
Cohesion: 0.12
Nodes (15): addLine(), attachCustomer(), complete(), createInProgress(), findById(), hold(), listForAdmin(), listHeldForShift() (+7 more)

### Community 92 - "ListCategoriesAction"
Cohesion: 0.31
Nodes (5): ListCategoriesAction, CatalogRepositoryInterface, Collection, ListCategoriesController, JsonResponse

### Community 93 - "ResumeSaleAction"
Cohesion: 0.22
Nodes (9): dev, serve, test, Composer\\Config::disableProcessTimeout, npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74,#86efac\" \"php artisan serve\" \"php artisan queue:listen redis --tries=3 --timeout=0\" \"php artisan reverb:start\" \"php artisan pail --timeout=0\" \"npm run dev\" --names=server,queue,reverb,logs,vite --kill-others, @php artisan config:clear --ansi @no_additional_args, @php artisan serve --host=127.0.0.1 --port=8000, @php artisan test (+1 more)

### Community 94 - "DeleteCategoryAction"
Cohesion: 0.31
Nodes (6): PromotionsRepositoryInterface, SalesRepositoryInterface, RemovePromotionFromSaleAction, JsonResponse, Request, RemovePromotionFromSaleController

### Community 95 - "DeleteProductAction"
Cohesion: 0.05
Nodes (20): CreateUserAction, UsersRepositoryInterface, ListUsersAction, UsersRepositoryInterface, UsersRepositoryInterface, ShowUserAction, UsersRepositoryInterface, UpdateUserAction (+12 more)

### Community 96 - "ShowCategoryAction"
Cohesion: 0.21
Nodes (5): CatalogRepositoryInterface, ShowCategoryAction, JsonResponse, ShowCategoryController, CatalogResource

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
Cohesion: 0.32
Nodes (3): CreateSaleController, JsonResponse, CreateSaleRequest

### Community 104 - "ListCustomersAction"
Cohesion: 0.20
Nodes (5): ListCustomersAction, CustomersRepositoryInterface, ListCustomersController, JsonResponse, ListCustomersRequest

### Community 105 - "CustomerResource"
Cohesion: 0.22
Nodes (5): CreateCustomerAction, CustomersRepositoryInterface, CreateCustomerController, JsonResponse, StoreCustomerRequest

### Community 106 - "CreateSaleAction"
Cohesion: 0.29
Nodes (3): LogSaleAnalyticsRecorder, SaleAnalyticsRecorderInterface, CompleteSaleSideEffectsTest

### Community 107 - "ShowSaleAction"
Cohesion: 0.25
Nodes (5): DispatchSaleSideEffects, BusOrchestrator, Batch, PendingBatch, PendingChain

### Community 108 - "FindCustomerByCpfAction"
Cohesion: 0.18
Nodes (6): FindCustomerByCpfAction, CustomersRepositoryInterface, CustomerPayloadNormalizer, FindOperationalCustomerController, JsonResponse, Request

### Community 109 - "ShowCustomerAction"
Cohesion: 0.33
Nodes (4): CustomersRepositoryInterface, ShowCustomerAction, JsonResponse, ShowCustomerController

### Community 110 - "OperationalRouteAccessTest"
Cohesion: 0.05
Nodes (18): ActsWithAdminStoreAccess, BaseTestCase, RefreshDatabase, AdminAnalyticsTest, AdminDashboardMetricsTest, ListAdminNotificationsTest, ShowAdminSaleTest, AdminRouteAccessTest (+10 more)

### Community 111 - "CustomerErrorCodeTest"
Cohesion: 0.27
Nodes (4): LoginUserAction, LoginController, JsonResponse, LoginRequest

### Community 112 - "ResumeSaleController.php"
Cohesion: 0.27
Nodes (4): CompleteSaleController, JsonResponse, CompleteSaleRequest, Validator

### Community 117 - "ListPromotionsAction"
Cohesion: 0.20
Nodes (5): ListPromotionsAction, PromotionsRepositoryInterface, ListPromotionsController, JsonResponse, ListPromotionsRequest

### Community 118 - "PaymentMethod"
Cohesion: 0.27
Nodes (6): AuditLogRepositoryInterface, PromotionsRepositoryInterface, UpdatePromotionAction, JsonResponse, UpdatePromotionController, UpdatePromotionRequest

### Community 119 - "SaleResource"
Cohesion: 0.06
Nodes (22): CreateRefundAction, AuditLogRepositoryInterface, InventoryRepositoryInterface, PaymentGatewayInterface, RefundsReturnsRepositoryInterface, create(), findCompletedSale(), listForSale() (+14 more)

### Community 120 - "Promotion.php"
Cohesion: 0.06
Nodes (24): NormalizedPaymentWebhook, findLineByTransactionReference(), markLineStatus(), markWebhookProcessed(), DateTimeInterface, PaymentLineStatus, recordWebhookEvent(), normalize() (+16 more)

### Community 121 - "ApplyPromotionToSaleAction"
Cohesion: 0.29
Nodes (6): ListHeldSalesAction, Collection, SalesRepositoryInterface, ListHeldSalesController, JsonResponse, Request

### Community 122 - "SalePaymentValidator"
Cohesion: 0.25
Nodes (3): CardInstrumentValidatorInterface, SalePaymentValidator, SalePaymentValidatorTest

### Community 123 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, laravel/telescope, mockery/mockery, nunomaduro/collision (+1 more)

### Community 124 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 126 - "ListStoresTest"
Cohesion: 0.23
Nodes (3): Collection, UsersRepository, UsersRepositoryInterface

### Community 127 - "Sale.php"
Cohesion: 0.32
Nodes (3): HoldSaleController, JsonResponse, HoldSaleRequest

### Community 128 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 130 - "PromotionDiscountCalculator"
Cohesion: 0.08
Nodes (7): Collection, PromotionDiscountCalculator, Money, SaleCompletedNotification, PromotionResource, SaleResource, MoneyTest

### Community 131 - "PromotionErrorCodeTest"
Cohesion: 0.05
Nodes (15): Store, StorePolicy, OperationalCustomerTest, AdminInventoryTest, InventorySaleTest, CompleteSaleTest, HoldSaleTest, SalesCartTest (+7 more)

### Community 132 - "RemovePromotionFromSaleController.php"
Cohesion: 0.42
Nodes (7): CompleteSaleAction, FiscalReceiptGeneratorInterface, InventoryRepositoryInterface, PaymentGatewayInterface, PaymentsRepositoryInterface, PendingPaymentOutboxInterface, SalesRepositoryInterface

### Community 133 - "autoload-dev"
Cohesion: 0.06
Nodes (10): ActsWithOperationalSession, InteractsWithStatefulApi, OperationalRouteAccessTest, CloseCashShiftTest, OpenCashShiftTest, OperationalShiftGateTest, CustomerPiiEncryptionTest, CardPaymentValidationTest (+2 more)

### Community 134 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 135 - "require"
Cohesion: 0.25
Nodes (8): require, laravel/framework, laravel/horizon, laravel/pulse, laravel/reverb, laravel/sanctum, laravel/tinker, php

### Community 136 - "HasFactory"
Cohesion: 0.14
Nodes (4): Customer, HasMany, CustomerResource, CustomerCrudTest

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
Cohesion: 0.10
Nodes (5): CatalogRepository, Collection, Product, CatalogRepositoryInterface, ProductCrudTest

### Community 145 - "AssertManagerStoreAccess"
Cohesion: 0.19
Nodes (8): GetAdminAnalyticsAction, AnalyticsRepositoryInterface, ListAdminAuditLogsAction, AuditLogRepositoryInterface, GetAdminShiftReportAction, CashShiftRepositoryInterface, AssertManagerStoreAccess, StoreRepositoryInterface

### Community 148 - "RemoveSaleLineAction"
Cohesion: 0.09
Nodes (21): GetAdminAnalyticsController, JsonResponse, JsonResponse, Request, ReopenCashShiftController, JsonResponse, Request, ShowAdminShiftReportController (+13 more)

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
Cohesion: 0.33
Nodes (4): CloseCashShiftAction, CashShiftRepositoryInterface, CloseCashShiftController, JsonResponse

### Community 162 - "UsersRepository"
Cohesion: 0.60
Nodes (3): AuditLogRepositoryInterface, CashShiftRepositoryInterface, ReopenCashShiftAction

### Community 169 - "AppServiceProvider.php"
Cohesion: 0.22
Nodes (7): AdjustStoreInventoryAction, AuditLogRepositoryInterface, CatalogRepositoryInterface, InventoryRepositoryInterface, AdjustStoreInventoryController, JsonResponse, AdjustStoreInventoryRequest

### Community 172 - "AdminStoreAccessIdorTest"
Cohesion: 0.33
Nodes (5): LogoutUserAction, Request, LogoutController, JsonResponse, Request

### Community 176 - "ShowCurrentUserController.php"
Cohesion: 0.33
Nodes (4): DeleteCategoryAction, CatalogRepositoryInterface, DeleteCategoryController, JsonResponse

### Community 177 - "EnsureStoreContext.php"
Cohesion: 0.60
Nodes (3): JsonResponse, Request, ShowAdminSaleController

### Community 178 - "StoreDomainException"
Cohesion: 0.33
Nodes (4): DeleteProductAction, CatalogRepositoryInterface, DeleteProductController, JsonResponse

### Community 184 - "CustomersRepositoryInterface.php"
Cohesion: 0.22
Nodes (6): create(), findByCpf(), findById(), list(), Collection, update()

### Community 187 - "StoreInventoryFactory"
Cohesion: 0.05
Nodes (15): assignedStoreIds(), findById(), listAccessibleForUser(), userCanAccessStore(), BelongsToMany, User, Authenticatable, Notifiable (+7 more)

### Community 193 - "SelectStoreContextTest"
Cohesion: 0.36
Nodes (4): ShowCurrentUserAction, JsonResponse, Request, ShowCurrentUserController

### Community 194 - "SelectStoreContextTest"
Cohesion: 0.27
Nodes (7): CashShiftDomainException, ErrorCode, CatalogDomainException, ErrorCode, ErrorCode, StoreDomainException, DomainException

### Community 196 - "ListAdminShiftsAction"
Cohesion: 0.53
Nodes (3): ListAdminShiftsAction, CashShiftRepositoryInterface, Collection

### Community 200 - "OperationalStoreContextTest"
Cohesion: 0.12
Nodes (6): GetAdminAnalyticsRequest, CloseCashShiftRequest, ApplyPromotionToSaleController, JsonResponse, ApplyPromotionToSaleRequest, FormRequest

### Community 211 - "EnsureUserHasRole.php"
Cohesion: 0.53
Nodes (4): EnsureUserHasRole, Closure, Request, Response

## Knowledge Gaps
- **68 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+63 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **39 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `StoreInventoryFactory` to `ApiErrorResponse.php`, `PromotionErrorCodeTest`, `autoload-dev`, `UserFactory`, `HasFactory`, `post-create-project-cmd`, `AppServiceProvider.php`, `DatabaseSeeder.php`, `AnalyticsRepositoryInterface`, `CashShiftRepository.php`, `AssertManagerStoreAccess`, `HoldSaleAction`, `FormRequest`, `PromotionsRepository.php`, `RefundsReturnsRepository.php`, `SalesRepository.php`, `ListStoreInventoryRequest`, `SaleCompletedNotificationTest`, `UsersRepository`, `PaymentsRepositoryInterface.php`, `PromotionsRepositoryInterface.php`, `RefundsReturnsRepositoryInterface.php`, `AppServiceProvider.php`, `RefundsReturnsRepositoryInterface.php`, `StoreRepository`, `CreateProductAction`, `Category`, `SelectStoreContextTest`, `ListAdminShiftsAction`, `UpdateProductAction`, `ListCategoriesAction`, `ShowProductAction`, `StoreRepository`, `OperationalRouteAccessTest`, `SaleStatus.php`, `DeleteProductAction`, `CatalogResource`, `CreateCustomerAction`, `CreateSaleAction`, `OperationalRouteAccessTest`, `CustomerErrorCodeTest`, `ListStoresTest`, `PaymentMethod`, `SaleResource`, `Promotion.php`, `ListStoresTest`?**
  _High betweenness centrality (0.206) - this node is a cross-community bridge._
- **Why does `Controller` connect `RemoveSaleLineAction` to `User`, `DomainScaffolder`, `AttachCustomerToSaleAction`, `CashShiftRepository.php`, `CatalogRepository.php`, `RefundsReturnsRepository.php`, `ListStoreInventoryRequest`, `SaleCompletedNotificationTest`, `CustomersRepositoryInterface.php`, `InventoryRepositoryInterface.php`, `PromotionsRepositoryInterface.php`, `AppServiceProvider.php`, `AdminStoreAccessIdorTest`, `ShowCurrentUserController.php`, `EnsureStoreContext.php`, `StoreDomainException`, `CreateSaleAction`, `SelectStoreContextTest`, `OperationalStoreContextTest`, `UpdateProductAction`, `CatalogRepositoryInterface.php`, `SaleResource`, `Model`, `ShowCategoryAction`, `ShowProductAction`, `AdminRouteAccessTest`, `OperationalRouteAccessTest`, `UpdateCustomerAction`, `SaleStatus.php`, `ListCategoriesAction`, `DeleteCategoryAction`, `DeleteProductAction`, `ShowCategoryAction`, `CurrentCashShiftController.php`, `ShowProductAction`, `CatalogResource`, `UpdateSaleLineAction`, `ListCustomersAction`, `CustomerResource`, `FindCustomerByCpfAction`, `ShowCustomerAction`, `CustomerErrorCodeTest`, `ResumeSaleController.php`, `ListPromotionsAction`, `PaymentMethod`, `SaleResource`, `ApplyPromotionToSaleAction`, `Sale.php`?**
  _High betweenness centrality (0.144) - this node is a cross-community bridge._
- **Why does `TestCase` connect `OperationalRouteAccessTest` to `ApiErrorResponse.php`, `PromotionDiscountCalculator`, `PromotionErrorCodeTest`, `StoreContext`, `autoload-dev`, `Controller`, `HasFactory`, `AuthErrorCodeTest`, `StoreErrorCodeTest`, `DatabaseSeeder.php`, `HoldSaleAction`, `RefundErrorCodeTest`, `FormRequest`, `PromotionsRepository.php`, `BusOrchestratorTest`, `CashShiftRepositoryInterface.php`, `InventoryErrorCodeTest`, `PromotionErrorCodeTest`, `RefundsReturnsRepositoryInterface.php`, `CompleteSaleTest`, `RefundsReturnsRepositoryInterface.php`, `StoreInventoryFactory`, `Category`, `AdminAuditLogTest`, `CatalogErrorCodeTest`, `InventoryErrorCodeTest`, `StoreRepository`, `CreateCustomerAction`, `CreateSaleAction`, `ShowSaleAction`, `Promotion.php`, `SalePaymentValidator`?**
  _High betweenness centrality (0.132) - this node is a cross-community bridge._
- **Are the 194 inferred relationships involving `User` (e.g. with `.execute()` and `.handle()`) actually correct?**
  _`User` has 194 INFERRED edges - model-reasoned connections that need verification._
- **Are the 128 inferred relationships involving `Store` (e.g. with `.topCustomersBySpend()` and `.definition()`) actually correct?**
  _`Store` has 128 INFERRED edges - model-reasoned connections that need verification._
- **Are the 70 inferred relationships involving `Product` (e.g. with `.definition()` and `.run()`) actually correct?**
  _`Product` has 70 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _68 weakly-connected nodes found - possible documentation gaps or missing edges._