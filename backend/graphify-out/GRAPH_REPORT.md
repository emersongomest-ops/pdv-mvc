# Graph Report - projects\pdv\backend  (2026-07-20)

## Corpus Check
- 462 files · ~62,817 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2469 nodes · 4834 edges · 226 communities (188 shown, 38 thin omitted)
- Extraction: 86% EXTRACTED · 14% INFERRED · 0% AMBIGUOUS · INFERRED: 658 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `9a3f1881`
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
- AuthSecurityBaselineTest
- SelectStoreContextTest
- SelectStoreContextTest
- ListAdminAuditLogsAction
- ListAdminShiftsAction
- MoneyTest
- StoreErrorCodeTest
- CreatePromotionAction
- OperationalStoreContextTest
- HoldSaleAction
- RemoveSaleLineAction
- ShowAdminShiftReportController.php
- ListAdminNotificationsController.php
- RemoveSaleLineController.php
- ResumeSaleController.php
- ApplyPromotionToSaleAction
- CatalogResource
- HasFactory
- EnsureUserHasRole.php
- CardInstrumentFormatGuardTest
- LoginTest
- 2026_07_15_300001_create_sales_table.php
- SelectStoreContextTest
- ListCampaignCustomersAction
- CustomerPayloadNormalizer
- ListAdminSalesAction
- RemovePromotionFromSaleAction
- ListAdminNotificationsController.php
- ActsWithOperationalSession.php
- ProbeQueuedJob

## God Nodes (most connected - your core abstractions)
1. `User` - 302 edges
2. `Store` - 151 edges
3. `TestCase` - 136 edges
4. `Controller` - 131 edges
5. `Sale` - 102 edges
6. `Product` - 100 edges
7. `Customer` - 64 edges
8. `Money` - 50 edges
9. `Promotion` - 50 edges
10. `AssertManagerStoreAccess` - 43 edges

## Surprising Connections (you probably didn't know these)
- `createCategory()` --references--> `Category`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Category.php
- `updateCategory()` --references--> `Category`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Category.php
- `deleteCategory()` --references--> `Category`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Category.php
- `findProductById()` --references--> `Product`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Product.php
- `createProduct()` --references--> `Product`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Product.php

## Import Cycles
- None detected.

## Communities (226 total, 38 thin omitted)

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
Cohesion: 0.09
Nodes (17): PaymentRequest, PaymentMethod, PaymentResult, RefundRequest, RefundResult, charge(), PaymentChargeStatus, queryChargeStatus() (+9 more)

### Community 4 - "StoreContext"
Cohesion: 0.17
Nodes (5): CardInstrumentFormatGuard, assertValidForCharge(), CardInstrument, NotImplementedCardInstrumentValidator, CardInstrumentValidatorInterface

### Community 5 - "scripts"
Cohesion: 0.17
Nodes (12): scripts, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall, @php artisan key:generate --ansi (+4 more)

### Community 7 - "UserFactory"
Cohesion: 0.06
Nodes (16): CashShiftFactory, static, CategoryFactory, static, CustomerFactory, static, ProductFactory, static (+8 more)

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

### Community 14 - "DatabaseSeeder.php"
Cohesion: 0.11
Nodes (6): InventoryRepository, Collection, BelongsTo, StoreInventory, InventoryRepositoryInterface, OperationalCatalogTest

### Community 15 - "AnalyticsRepositoryInterface"
Cohesion: 0.14
Nodes (5): BelongsToMany, HasMany, static, PromotionFactory, DemoPromotionSeeder

### Community 16 - "CashShiftRepository.php"
Cohesion: 0.05
Nodes (22): OpenCashShiftAction, CashShiftRepositoryInterface, ShiftClosingReport, buildClosingReport(), close(), createOpen(), findById(), findOpenForUser() (+14 more)

### Community 17 - "CatalogRepository.php"
Cohesion: 0.53
Nodes (3): ListAdminShiftsAction, CashShiftRepositoryInterface, Collection

### Community 18 - "CustomersRepository.php"
Cohesion: 0.08
Nodes (11): Model, PiiEncryptedDate, Model, PiiEncryptedString, CustomersRepository, Collection, PiiCrypto, CarbonInterface (+3 more)

### Community 20 - "InventoryRepository.php"
Cohesion: 0.12
Nodes (6): PendingPaymentOutboxEntry, self, push(), InMemoryPendingPaymentOutbox, RedisPendingPaymentOutbox, PendingPaymentOutboxInterface

### Community 21 - "PaymentsRepository.php"
Cohesion: 0.23
Nodes (8): AuthenticationDomainException, ErrorCode, ApiErrorResponse, ErrorCode, JsonResponse, Request, AuthenticationException, AuthorizationException

### Community 22 - "PromotionsRepository.php"
Cohesion: 0.13
Nodes (6): PromotionAuditSnapshot, Collection, PromotionsRepository, DateTimeInterface, Promotion, PromotionsRepositoryInterface

### Community 23 - "RefundsReturnsRepository.php"
Cohesion: 0.21
Nodes (5): GetAdminDashboardMetricsAction, AdminDashboardMetrics, AdminDashboardController, JsonResponse, Request

### Community 24 - "SalesRepository.php"
Cohesion: 0.13
Nodes (10): createCategory(), createProduct(), deleteCategory(), deleteProduct(), findProductById(), listCategories(), listProducts(), Collection (+2 more)

### Community 32 - "AnalyticsRepositoryInterface.php"
Cohesion: 0.15
Nodes (5): self, WebhookRetryItem, InMemoryWebhookRetryQueue, RedisWebhookRetryQueue, WebhookRetryQueueInterface

### Community 34 - "CatalogRepositoryInterface.php"
Cohesion: 0.29
Nodes (6): BeginManagerMfaSetupAction, Session, TotpAuthenticatorInterface, BeginManagerMfaSetupController, JsonResponse, Request

### Community 35 - "CustomersRepositoryInterface.php"
Cohesion: 0.16
Nodes (4): ListCampaignCustomersController, JsonResponse, ListCampaignCustomersRequest, CustomerResource

### Community 37 - "InventoryRepositoryInterface.php"
Cohesion: 0.28
Nodes (3): ListAdminSalesController, JsonResponse, ListAdminSalesRequest

### Community 38 - "PaymentsRepositoryInterface.php"
Cohesion: 0.09
Nodes (12): AnalyticsRepositoryInterface, AdminAnalyticsSnapshot, CustomerSpendRow, adminSnapshot(), listCampaignCustomers(), Collection, GetAdminAnalyticsController, JsonResponse (+4 more)

### Community 39 - "PromotionsRepositoryInterface.php"
Cohesion: 0.14
Nodes (10): AddSaleLineAction, CatalogRepositoryInterface, SalesRepositoryInterface, CreateSaleAction, SalesRepositoryInterface, SalesRepositoryInterface, ShowSaleAction, AddSaleLineController (+2 more)

### Community 40 - "RefundsReturnsRepositoryInterface.php"
Cohesion: 0.29
Nodes (4): LoginUserAction, Session, MfaPendingSession, Session

### Community 41 - "SalesRepositoryInterface.php"
Cohesion: 0.23
Nodes (5): ConfirmManagerMfaSetupController, JsonResponse, JsonResponse, VerifyManagerMfaChallengeController, MfaCodeRequest

### Community 57 - "CreateSaleAction"
Cohesion: 0.20
Nodes (5): ListProductsAction, CatalogRepositoryInterface, ListProductsController, JsonResponse, ListProductsRequest

### Community 62 - "Category"
Cohesion: 0.13
Nodes (4): Google2faTotpAuthenticator, Google2FA, SessionGateTest, TotpAuthenticatorInterface

### Community 63 - "CreateProductAction"
Cohesion: 0.15
Nodes (3): AuditLog, BelongsTo, AdminAuditLogTest

### Community 66 - "SaleErrorCodeTest"
Cohesion: 0.12
Nodes (8): ListAdminShiftsRequest, ApplyPromotionToSaleController, JsonResponse, HoldSaleController, JsonResponse, ApplyPromotionToSaleRequest, HoldSaleRequest, FormRequest

### Community 73 - "UpdateProductAction"
Cohesion: 0.14
Nodes (9): ListAdminAuditLogsAction, AuditLogRepositoryInterface, AdminAuditFilters, AuditLogEntry, append(), listForAdmin(), AuditLogRepository, AuditAction (+1 more)

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

### Community 78 - "ListCategoriesAction"
Cohesion: 0.32
Nodes (5): adjustQuantity(), findForStoreProduct(), listForStore(), mapForStoreProducts(), Collection

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
Cohesion: 0.07
Nodes (6): BelongsTo, Product, ProductCrudTest, AdminInventoryTest, ApplyPromotionToSaleTest, SalesCartTest

### Community 83 - "AdminRouteAccessTest"
Cohesion: 0.33
Nodes (4): PromotionsRepositoryInterface, ShowPromotionAction, JsonResponse, ShowPromotionController

### Community 84 - "LoginTest"
Cohesion: 0.10
Nodes (13): attachToSale(), create(), findApplied(), findByCode(), findById(), list(), listAppliedForSale(), Collection (+5 more)

### Community 85 - "OperationalRouteAccessTest"
Cohesion: 0.24
Nodes (6): CreatePromotionAction, AuditLogRepositoryInterface, PromotionsRepositoryInterface, CreatePromotionController, JsonResponse, StorePromotionRequest

### Community 86 - "UpdateCustomerAction"
Cohesion: 0.22
Nodes (5): CustomersRepositoryInterface, UpdateCustomerAction, JsonResponse, UpdateCustomerController, UpdateCustomerRequest

### Community 87 - "SaleStatus.php"
Cohesion: 0.10
Nodes (16): Request, StoreRepositoryInterface, SelectStoreContextAction, OperationalPosController, JsonResponse, Request, EnsureStoreContext, Closure (+8 more)

### Community 88 - "CloseCashShiftTest"
Cohesion: 0.11
Nodes (15): addLine(), attachCustomer(), complete(), createInProgress(), findById(), hold(), listForAdmin(), listHeldForShift() (+7 more)

### Community 92 - "ListCategoriesAction"
Cohesion: 0.20
Nodes (6): ListCategoriesAction, CatalogRepositoryInterface, Collection, ListCategoriesController, JsonResponse, CatalogResource

### Community 93 - "ResumeSaleAction"
Cohesion: 0.22
Nodes (9): dev, serve, test, Composer\\Config::disableProcessTimeout, npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74,#86efac\" \"php artisan serve\" \"php artisan queue:listen redis --tries=3 --timeout=0\" \"php artisan reverb:start\" \"php artisan pail --timeout=0\" \"npm run dev\" --names=server,queue,reverb,logs,vite --kill-others, @php artisan config:clear --ansi @no_additional_args, @php artisan serve --host=127.0.0.1 --port=8000, @php artisan test (+1 more)

### Community 94 - "DeleteCategoryAction"
Cohesion: 0.13
Nodes (10): JsonResponse, Request, RemovePromotionFromSaleController, JsonResponse, Request, ResumeSaleController, JsonResponse, Request (+2 more)

### Community 95 - "DeleteProductAction"
Cohesion: 0.20
Nodes (5): ListUsersAction, UsersRepositoryInterface, ListUsersController, JsonResponse, ListUsersRequest

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
Cohesion: 0.32
Nodes (3): CreateSaleController, JsonResponse, CreateSaleRequest

### Community 103 - "CreateCustomerAction"
Cohesion: 0.27
Nodes (4): ConfirmManagerMfaSetupAction, Session, TotpAuthenticatorInterface, MfaRecoveryCodeVault

### Community 104 - "ListCustomersAction"
Cohesion: 0.20
Nodes (5): ListCustomersAction, CustomersRepositoryInterface, ListCustomersController, JsonResponse, ListCustomersRequest

### Community 105 - "CustomerResource"
Cohesion: 0.24
Nodes (5): CreateCustomerAction, CustomersRepositoryInterface, CreateCustomerController, JsonResponse, StoreCustomerRequest

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
Cohesion: 0.09
Nodes (10): BaseTestCase, ListAdminNotificationsTest, AdminRouteAccessTest, AdminCatalogAccessTest, ExampleTest, CorrelationIdMiddlewareTest, TestCase, CreateCustomerActionTest (+2 more)

### Community 111 - "CustomerErrorCodeTest"
Cohesion: 0.32
Nodes (3): LoginController, JsonResponse, LoginRequest

### Community 112 - "ResumeSaleController.php"
Cohesion: 0.27
Nodes (4): CompleteSaleController, JsonResponse, CompleteSaleRequest, Validator

### Community 113 - "ListStoresTest"
Cohesion: 0.50
Nodes (3): Session, TotpAuthenticatorInterface, VerifyManagerMfaChallengeAction

### Community 117 - "ListPromotionsAction"
Cohesion: 0.20
Nodes (5): ListPromotionsAction, PromotionsRepositoryInterface, ListPromotionsController, JsonResponse, ListPromotionsRequest

### Community 118 - "PaymentMethod"
Cohesion: 0.12
Nodes (9): DatabaseSeeder, DemoCatalogSeeder, DemoCustomerSeeder, DemoInventorySeeder, DemoStoreSeeder, ManagerUserSeeder, OperatorUserSeeder, Seeder (+1 more)

### Community 119 - "SaleResource"
Cohesion: 0.19
Nodes (9): CreateRefundAction, AuditLogRepositoryInterface, InventoryRepositoryInterface, PaymentGatewayInterface, RefundsReturnsRepositoryInterface, CreateRefundController, JsonResponse, StoreRefundRequest (+1 more)

### Community 120 - "Promotion.php"
Cohesion: 0.15
Nodes (6): PaymentsRepository, DateTimeInterface, PaymentLineStatus, PaymentLine, BelongsTo, PaymentsRepositoryInterface

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

### Community 128 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 129 - "ApiErrorResponse.php"
Cohesion: 0.57
Nodes (4): AdjustStoreInventoryAction, AuditLogRepositoryInterface, CatalogRepositoryInterface, InventoryRepositoryInterface

### Community 130 - "PromotionDiscountCalculator"
Cohesion: 0.12
Nodes (4): Collection, PromotionDiscountCalculator, Money, MoneyTest

### Community 131 - "PromotionErrorCodeTest"
Cohesion: 0.05
Nodes (9): Store, StorePolicy, ReopenCashShiftTest, OperationalCustomerTest, CompleteSaleTest, HoldSaleTest, actingAsManagerForStore(), attachManagerToStore() (+1 more)

### Community 132 - "RemovePromotionFromSaleController.php"
Cohesion: 0.42
Nodes (7): CompleteSaleAction, FiscalReceiptGeneratorInterface, InventoryRepositoryInterface, PaymentGatewayInterface, PaymentsRepositoryInterface, PendingPaymentOutboxInterface, SalesRepositoryInterface

### Community 133 - "autoload-dev"
Cohesion: 0.10
Nodes (15): ActsWithOperationalSession, InteractsWithStatefulApi, RefreshDatabase, OperationalRouteAccessTest, CloseCashShiftReportTest, CloseCashShiftTest, OpenCashShiftTest, OperationalShiftGateTest (+7 more)

### Community 134 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 135 - "require"
Cohesion: 0.20
Nodes (10): require, bacon/bacon-qr-code, laravel/framework, laravel/horizon, laravel/pulse, laravel/reverb, laravel/sanctum, laravel/tinker (+2 more)

### Community 136 - "HasFactory"
Cohesion: 0.08
Nodes (12): create(), findByCpf(), findById(), list(), Collection, update(), Customer, HasMany (+4 more)

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
Nodes (6): findCategoryById(), CatalogRepository, Collection, Category, HasMany, CatalogRepositoryInterface

### Community 145 - "AssertManagerStoreAccess"
Cohesion: 0.19
Nodes (8): GetAdminAnalyticsAction, AnalyticsRepositoryInterface, GetAdminShiftReportAction, CashShiftRepositoryInterface, SalesRepositoryInterface, ShowAdminSaleAction, AssertManagerStoreAccess, StoreRepositoryInterface

### Community 147 - "DeleteCategoryAction"
Cohesion: 0.20
Nodes (9): pulse.cache, pulse.exceptions, pulse.queues, pulse.servers, pulse.slow-jobs, pulse.slow-outgoing-requests, pulse.slow-queries, pulse.slow-requests (+1 more)

### Community 148 - "RemoveSaleLineAction"
Cohesion: 0.12
Nodes (15): ListAdminShiftsController, JsonResponse, JsonResponse, Request, ReopenCashShiftController, JsonResponse, Request, ShowAdminShiftReportController (+7 more)

### Community 150 - "AuthenticationDomainException"
Cohesion: 0.15
Nodes (8): create(), findCompletedSale(), listForSale(), Collection, BelongsTo, HasMany, Refund, RefundResource

### Community 151 - "ResumeSaleController.php"
Cohesion: 0.50
Nodes (4): extra, laravel, dont-discover, laravel/telescope

### Community 152 - "2026_07_16_165219_create_telescope_entries_table.php"
Cohesion: 0.83
Nodes (3): down(), getConnection(), up()

### Community 157 - "ListStoreInventoryRequest"
Cohesion: 0.53
Nodes (3): ListStoreInventoryAction, Collection, InventoryRepositoryInterface

### Community 158 - "SaleCompletedNotification"
Cohesion: 0.28
Nodes (4): HmacPaymentWebhookSignatureVerifier, AppServiceProvider, PaymentWebhookSignatureVerifierInterface, ServiceProvider

### Community 160 - "SaleCompletedNotificationTest"
Cohesion: 0.22
Nodes (5): CloseCashShiftAction, CashShiftRepositoryInterface, CloseCashShiftController, JsonResponse, CloseCashShiftRequest

### Community 162 - "UsersRepository"
Cohesion: 0.60
Nodes (3): AuditLogRepositoryInterface, CashShiftRepositoryInterface, ReopenCashShiftAction

### Community 169 - "AppServiceProvider.php"
Cohesion: 0.12
Nodes (7): AdjustStoreInventoryController, JsonResponse, ListStoreInventoryController, JsonResponse, AdjustStoreInventoryRequest, ListStoreInventoryRequest, InventoryResource

### Community 172 - "AdminStoreAccessIdorTest"
Cohesion: 0.29
Nodes (5): LogoutUserAction, Request, LogoutController, JsonResponse, Request

### Community 174 - "ProbeQueuedJob"
Cohesion: 0.53
Nodes (4): EnsureUserHasRole, Closure, Request, Response

### Community 176 - "ShowCurrentUserController.php"
Cohesion: 0.33
Nodes (4): DeleteCategoryAction, CatalogRepositoryInterface, DeleteCategoryController, JsonResponse

### Community 177 - "EnsureStoreContext.php"
Cohesion: 0.21
Nodes (6): AuditLogRepositoryInterface, CatalogRepositoryInterface, UpdateProductAction, JsonResponse, UpdateProductController, UpdateProductRequest

### Community 178 - "StoreDomainException"
Cohesion: 0.33
Nodes (4): DeleteProductAction, CatalogRepositoryInterface, DeleteProductController, JsonResponse

### Community 183 - "AdminStoreAccessIdorTest"
Cohesion: 0.18
Nodes (5): Collection, RefundsReturnsRepository, BelongsTo, RefundLine, RefundsReturnsRepositoryInterface

### Community 184 - "CustomersRepositoryInterface.php"
Cohesion: 0.22
Nodes (5): CreateUserAction, UsersRepositoryInterface, CreateUserController, JsonResponse, StoreUserRequest

### Community 186 - "RefundsReturnsRepositoryInterface.php"
Cohesion: 0.22
Nodes (5): UsersRepositoryInterface, UpdateUserAction, JsonResponse, UpdateUserController, UpdateUserRequest

### Community 187 - "StoreInventoryFactory"
Cohesion: 0.05
Nodes (11): assignedStoreIds(), findById(), listAccessibleForUser(), userCanAccessStore(), User, Authenticatable, AdminUserManagementTest, ListAdminSalesTest (+3 more)

### Community 193 - "SelectStoreContextTest"
Cohesion: 0.36
Nodes (4): ShowCurrentUserAction, JsonResponse, Request, ShowCurrentUserController

### Community 194 - "SelectStoreContextTest"
Cohesion: 0.09
Nodes (19): CashShiftDomainException, ErrorCode, CatalogDomainException, ErrorCode, CustomerDomainException, ErrorCode, InventoryDomainException, ErrorCode (+11 more)

### Community 197 - "MoneyTest"
Cohesion: 0.23
Nodes (5): UsersRepositoryInterface, ShowUserAction, JsonResponse, ShowUserController, UserResource

### Community 199 - "CreatePromotionAction"
Cohesion: 0.27
Nodes (6): AuditLogRepositoryInterface, PromotionsRepositoryInterface, UpdatePromotionAction, JsonResponse, UpdatePromotionController, UpdatePromotionRequest

### Community 200 - "OperationalStoreContextTest"
Cohesion: 0.20
Nodes (4): ListAdminAuditLogsController, JsonResponse, ListAdminAuditLogsRequest, AuditLogResource

### Community 201 - "HoldSaleAction"
Cohesion: 0.27
Nodes (4): PaymentWebhookEvent, BelongsTo, StockAdjustment, Model

### Community 203 - "ShowAdminShiftReportController.php"
Cohesion: 0.36
Nodes (5): NormalizedPaymentWebhook, normalize(), JsonPaymentWebhookPayloadNormalizer, PaymentWebhookEventType, PaymentWebhookPayloadNormalizerInterface

### Community 204 - "ListAdminNotificationsController.php"
Cohesion: 0.22
Nodes (6): findLineByTransactionReference(), markLineStatus(), markWebhookProcessed(), DateTimeInterface, PaymentLineStatus, recordWebhookEvent()

### Community 207 - "ResumeSaleController.php"
Cohesion: 0.22
Nodes (3): SalesRepositoryInterface, ResumeSaleAction, SaleCartGuard

### Community 208 - "ApplyPromotionToSaleAction"
Cohesion: 0.46
Nodes (3): ApplyPromotionToSaleAction, PromotionsRepositoryInterface, SalesRepositoryInterface

### Community 210 - "HasFactory"
Cohesion: 0.25
Nodes (4): BelongsToMany, BelongsToMany, HasFactory, Notifiable

### Community 214 - "2026_07_15_300001_create_sales_table.php"
Cohesion: 0.11
Nodes (4): ActsWithAdminStoreAccess, AdminDashboardMetricsTest, AdminShiftReportTest, ShowAdminSaleTest

### Community 218 - "ListCampaignCustomersAction"
Cohesion: 0.53
Nodes (3): ListCampaignCustomersAction, AnalyticsRepositoryInterface, Collection

### Community 220 - "ListAdminSalesAction"
Cohesion: 0.53
Nodes (3): ListAdminSalesAction, Collection, SalesRepositoryInterface

### Community 221 - "RemovePromotionFromSaleAction"
Cohesion: 0.60
Nodes (3): PromotionsRepositoryInterface, SalesRepositoryInterface, RemovePromotionFromSaleAction

### Community 222 - "ListAdminNotificationsController.php"
Cohesion: 0.60
Nodes (3): ListAdminNotificationsController, JsonResponse, Request

### Community 223 - "ActsWithOperationalSession.php"
Cohesion: 0.70
Nodes (4): actingAsOperatorAtStore(), actingAsOperatorWithOpenShift(), static, withOpenShift()

## Knowledge Gaps
- **79 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+74 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **38 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `StoreInventoryFactory` to `ApiErrorResponse.php`, `PromotionErrorCodeTest`, `autoload-dev`, `AuthenticationDomainException`, `UserFactory`, `HasFactory`, `post-create-project-cmd`, `AppServiceProvider.php`, `DatabaseSeeder.php`, `CashShiftRepository.php`, `AssertManagerStoreAccess`, `CatalogRepository.php`, `HoldSaleAction`, `FormRequest`, `PromotionsRepository.php`, `RefundsReturnsRepository.php`, `ListStoreInventoryRequest`, `SaleCompletedNotificationTest`, `UsersRepository`, `CatalogRepositoryInterface.php`, `PaymentsRepositoryInterface.php`, `PromotionsRepositoryInterface.php`, `RefundsReturnsRepositoryInterface.php`, `DispatchSaleSideEffectsTest`, `EnsureStoreContext.php`, `CustomersRepositoryInterface.php`, `AdjustStoreInventoryAction`, `RefundsReturnsRepositoryInterface.php`, `Sale`, `StoreRepository`, `CreateProductAction`, `Category`, `SelectStoreContextTest`, `AuthSecurityBaselineTest`, `ListAdminShiftsAction`, `MoneyTest`, `CreatePromotionAction`, `UpdateProductAction`, `RemoveSaleLineController.php`, `ShowProductAction`, `HasFactory`, `EnsureUserHasRole.php`, `StoreRepository`, `OperationalRouteAccessTest`, `2026_07_15_300001_create_sales_table.php`, `SaleStatus.php`, `CloseCashShiftTest`, `LoginTest`, `SelectStoreContextTest`, `ListAdminSalesAction`, `ActsWithOperationalSession.php`, `CatalogResource`, `CreateCustomerAction`, `OperationalRouteAccessTest`, `ListStoresTest`, `PaymentMethod`, `SaleResource`, `Promotion.php`, `ListStoresTest`?**
  _High betweenness centrality (0.201) - this node is a cross-community bridge._
- **Why does `Controller` connect `RemoveSaleLineAction` to `User`, `DomainScaffolder`, `AttachCustomerToSaleAction`, `CashShiftRepository.php`, `RefundsReturnsRepository.php`, `SaleCompletedNotificationTest`, `CatalogRepositoryInterface.php`, `CustomersRepositoryInterface.php`, `InventoryRepositoryInterface.php`, `PaymentsRepositoryInterface.php`, `PromotionsRepositoryInterface.php`, `SalesRepositoryInterface.php`, `AppServiceProvider.php`, `AdminStoreAccessIdorTest`, `ShowCurrentUserController.php`, `EnsureStoreContext.php`, `StoreDomainException`, `CustomersRepositoryInterface.php`, `CreateSaleAction`, `RefundsReturnsRepositoryInterface.php`, `SelectStoreContextTest`, `SaleErrorCodeTest`, `MoneyTest`, `CreatePromotionAction`, `OperationalStoreContextTest`, `CatalogRepositoryInterface.php`, `SaleResource`, `Model`, `ShowCategoryAction`, `ShowProductAction`, `AdminRouteAccessTest`, `OperationalRouteAccessTest`, `UpdateCustomerAction`, `SaleStatus.php`, `ListCategoriesAction`, `ListAdminNotificationsController.php`, `DeleteProductAction`, `ShowCategoryAction`, `CurrentCashShiftController.php`, `ShowProductAction`, `CatalogResource`, `DeleteCategoryAction`, `UpdateSaleLineAction`, `ListCustomersAction`, `CustomerResource`, `FindCustomerByCpfAction`, `ShowCustomerAction`, `CustomerErrorCodeTest`, `ResumeSaleController.php`, `ListPromotionsAction`, `SaleResource`, `ApplyPromotionToSaleAction`?**
  _High betweenness centrality (0.146) - this node is a cross-community bridge._
- **Why does `TestCase` connect `OperationalRouteAccessTest` to `PromotionDiscountCalculator`, `PromotionErrorCodeTest`, `StoreContext`, `autoload-dev`, `AuthenticationDomainException`, `HasFactory`, `AuthErrorCodeTest`, `StoreErrorCodeTest`, `DatabaseSeeder.php`, `CashShiftRepository.php`, `RefundErrorCodeTest`, `FormRequest`, `BusOrchestratorTest`, `CashShiftRepositoryInterface.php`, `InventoryErrorCodeTest`, `PromotionErrorCodeTest`, `CompleteSaleTest`, `AdjustStoreInventoryAction`, `StoreInventoryFactory`, `Sale`, `Category`, `CreateProductAction`, `AuthSecurityBaselineTest`, `AdminAuditLogTest`, `CatalogErrorCodeTest`, `InventoryErrorCodeTest`, `ListAdminShiftsAction`, `StoreErrorCodeTest`, `RemoveSaleLineController.php`, `StoreRepository`, `EnsureUserHasRole.php`, `CardInstrumentFormatGuardTest`, `LoginTest`, `2026_07_15_300001_create_sales_table.php`, `SelectStoreContextTest`, `ShowSaleAction`, `SalePaymentValidator`?**
  _High betweenness centrality (0.101) - this node is a cross-community bridge._
- **Are the 204 inferred relationships involving `User` (e.g. with `.execute()` and `.handle()`) actually correct?**
  _`User` has 204 INFERRED edges - model-reasoned connections that need verification._
- **Are the 131 inferred relationships involving `Store` (e.g. with `.topCustomersBySpend()` and `.definition()`) actually correct?**
  _`Store` has 131 INFERRED edges - model-reasoned connections that need verification._
- **Are the 26 inferred relationships involving `Sale` (e.g. with `.buildClosingReport()` and `.manager_dashboard_returns_parallel_kpi_metrics()`) actually correct?**
  _`Sale` has 26 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _79 weakly-connected nodes found - possible documentation gaps or missing edges._