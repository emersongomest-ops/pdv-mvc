# Graph Report - projects\pdv\backend  (2026-07-20)

## Corpus Check
- 476 files · ~65,341 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2558 nodes · 5025 edges · 237 communities (192 shown, 45 thin omitted)
- Extraction: 86% EXTRACTED · 14% INFERRED · 0% AMBIGUOUS · INFERRED: 689 edges (avg confidence: 0.8)
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
- artisan
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
- post-autoload-dump
- 2026_07_15_300001_create_sales_table.php
- SelectStoreContextTest
- AdminDashboardController.php
- ListAdminSalesAction
- RemovePromotionFromSaleAction
- RemoveSaleLineController.php
- ActsWithOperationalSession.php
- ProbeQueuedJob
- OperationalRouteAccessTest
- WebhookRetryQueueInterface.php
- SaleAnalyticsRecorderInterface
- DatabaseSeeder
- actingAsManagerForStore
- AdminAnalyticsTest
- AdminInventoryTest
- .completedSaleFixture
- ActsWithOperationalSession.php
- CatalogResource

## God Nodes (most connected - your core abstractions)
1. `User` - 310 edges
2. `Store` - 157 edges
3. `TestCase` - 147 edges
4. `Controller` - 131 edges
5. `Product` - 105 edges
6. `Sale` - 105 edges
7. `Customer` - 64 edges
8. `Money` - 50 edges
9. `Promotion` - 50 edges
10. `AssertManagerStoreAccess` - 43 edges

## Surprising Connections (you probably didn't know these)
- `findCategoryById()` --references--> `Category`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Category.php
- `createCategory()` --references--> `Category`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Category.php
- `deleteCategory()` --references--> `Category`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Category.php
- `findProductById()` --references--> `Product`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Product.php
- `createProduct()` --references--> `Product`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Product.php

## Import Cycles
- None detected.

## Communities (237 total, 45 thin omitted)

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
Cohesion: 0.13
Nodes (6): CardInstrumentFormatGuard, assertValidForCharge(), CardInstrument, NotImplementedCardInstrumentValidator, CardInstrumentValidatorInterface, CardInstrumentFormatGuardTest

### Community 5 - "scripts"
Cohesion: 0.17
Nodes (12): scripts, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall, @php artisan key:generate --ansi (+4 more)

### Community 6 - "AuthenticationDomainException"
Cohesion: 0.08
Nodes (21): addLine(), attachCustomer(), complete(), createInProgress(), findById(), findLineById(), findLineByProduct(), hold() (+13 more)

### Community 7 - "UserFactory"
Cohesion: 0.06
Nodes (16): CashShiftFactory, static, CategoryFactory, static, CustomerFactory, static, ProductFactory, static (+8 more)

### Community 8 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 9 - "DomainScaffolder"
Cohesion: 0.06
Nodes (26): ConsumePaymentWebhookAction, PaymentsRepositoryInterface, PaymentWebhookPayloadNormalizerInterface, PaymentWebhookSignatureVerifierInterface, PendingPaymentOutboxInterface, ErrorCode, PaymentGatewayInterface, PaymentsRepositoryInterface (+18 more)

### Community 10 - "AppServiceProvider.php"
Cohesion: 0.23
Nodes (9): NotifyManagersOfSaleCompleted, SaleCompleted, AbstractQueuedJob, Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels (+1 more)

### Community 11 - "ListAccessibleStoresAction"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "DatabaseSeeder.php"
Cohesion: 0.18
Nodes (5): BelongsTo, BelongsToMany, BelongsToMany, HasFactory, Notifiable

### Community 15 - "AnalyticsRepositoryInterface"
Cohesion: 0.21
Nodes (3): BelongsToMany, HasMany, DemoPromotionSeeder

### Community 16 - "CashShiftRepository.php"
Cohesion: 0.19
Nodes (11): buildClosingReport(), close(), createOpen(), findById(), findOpenForUser(), findOpenForUserAtStore(), listForStore(), Collection (+3 more)

### Community 17 - "CatalogRepository.php"
Cohesion: 0.19
Nodes (6): ListAdminShiftsAction, CashShiftRepositoryInterface, Collection, ListAdminShiftsController, JsonResponse, ListAdminShiftsRequest

### Community 18 - "CustomersRepository.php"
Cohesion: 0.23
Nodes (3): CustomersRepository, Collection, CustomersRepositoryInterface

### Community 20 - "InventoryRepository.php"
Cohesion: 0.23
Nodes (3): PendingPaymentOutboxEntry, self, RedisPendingPaymentOutbox

### Community 21 - "PaymentsRepository.php"
Cohesion: 0.23
Nodes (8): AuthenticationDomainException, ErrorCode, ApiErrorResponse, ErrorCode, JsonResponse, Request, AuthenticationException, AuthorizationException

### Community 22 - "PromotionsRepository.php"
Cohesion: 0.11
Nodes (8): attachToSale(), findApplied(), updateAppliedAmount(), Collection, PromotionsRepository, BelongsTo, SalePromotion, PromotionsRepositoryInterface

### Community 23 - "RefundsReturnsRepository.php"
Cohesion: 0.15
Nodes (3): CatalogRepository, Collection, CatalogRepositoryInterface

### Community 24 - "SalesRepository.php"
Cohesion: 0.13
Nodes (10): createCategory(), createProduct(), deleteCategory(), deleteProduct(), findCategoryById(), findProductById(), listCategories(), listProducts() (+2 more)

### Community 30 - "artisan"
Cohesion: 0.09
Nodes (5): BelongsTo, StoreInventory, OperationalCatalogTest, AdminInventoryTest, RefundThrottleAndSalesIdorTest

### Community 32 - "AnalyticsRepositoryInterface.php"
Cohesion: 0.12
Nodes (6): self, WebhookRetryItem, push(), InMemoryWebhookRetryQueue, RedisWebhookRetryQueue, WebhookRetryQueueInterface

### Community 34 - "CatalogRepositoryInterface.php"
Cohesion: 0.29
Nodes (6): BeginManagerMfaSetupAction, Session, TotpAuthenticatorInterface, BeginManagerMfaSetupController, JsonResponse, Request

### Community 35 - "CustomersRepositoryInterface.php"
Cohesion: 0.19
Nodes (6): ListCampaignCustomersAction, AnalyticsRepositoryInterface, Collection, ListCampaignCustomersController, JsonResponse, ListCampaignCustomersRequest

### Community 37 - "InventoryRepositoryInterface.php"
Cohesion: 0.19
Nodes (6): ListAdminSalesAction, Collection, SalesRepositoryInterface, ListAdminSalesController, JsonResponse, ListAdminSalesRequest

### Community 38 - "PaymentsRepositoryInterface.php"
Cohesion: 0.10
Nodes (11): GetAdminAnalyticsAction, AnalyticsRepositoryInterface, AdminAnalyticsSnapshot, CustomerSpendRow, adminSnapshot(), listCampaignCustomers(), Collection, GetAdminAnalyticsController (+3 more)

### Community 39 - "PromotionsRepositoryInterface.php"
Cohesion: 0.22
Nodes (6): AddSaleLineAction, CatalogRepositoryInterface, SalesRepositoryInterface, AddSaleLineController, JsonResponse, AddSaleLineRequest

### Community 40 - "RefundsReturnsRepositoryInterface.php"
Cohesion: 0.29
Nodes (4): LoginUserAction, Session, MfaPendingSession, Session

### Community 41 - "SalesRepositoryInterface.php"
Cohesion: 0.16
Nodes (9): Controller, ConfirmManagerMfaSetupController, JsonResponse, ListAdminNotificationsController, JsonResponse, Request, JsonResponse, VerifyManagerMfaChallengeController (+1 more)

### Community 57 - "CreateSaleAction"
Cohesion: 0.20
Nodes (5): ListProductsAction, CatalogRepositoryInterface, ListProductsController, JsonResponse, ListProductsRequest

### Community 61 - "Sale"
Cohesion: 0.12
Nodes (11): claimProcessing(), delete(), deleteCreatedBefore(), findByScopeAndKey(), markCompleted(), DateTimeInterface, EloquentIdempotencyRecordRepository, DateTimeInterface (+3 more)

### Community 62 - "Category"
Cohesion: 0.12
Nodes (3): LoginTest, SessionGateTest, TotpAuthenticatorInterface

### Community 63 - "CreateProductAction"
Cohesion: 0.14
Nodes (8): DatabaseSeeder, DemoCatalogSeeder, DemoInventorySeeder, DemoStoreSeeder, ManagerUserSeeder, OperatorUserSeeder, Seeder, WithoutModelEvents

### Community 66 - "SaleErrorCodeTest"
Cohesion: 0.20
Nodes (6): ApplyPromotionToSaleAction, PromotionsRepositoryInterface, SalesRepositoryInterface, ApplyPromotionToSaleController, JsonResponse, ApplyPromotionToSaleRequest

### Community 73 - "UpdateProductAction"
Cohesion: 0.06
Nodes (19): AuditLogRepositoryInterface, CatalogRepositoryInterface, UpdateProductAction, AdminAuditFilters, AuditLogEntry, append(), listForAdmin(), ListAdminAuditLogsController (+11 more)

### Community 74 - "CatalogRepositoryInterface.php"
Cohesion: 0.22
Nodes (5): CatalogRepositoryInterface, UpdateCategoryAction, JsonResponse, UpdateCategoryController, UpdateCategoryRequest

### Community 75 - "SaleResource"
Cohesion: 0.20
Nodes (6): ListOperationalProductsAction, CatalogRepositoryInterface, InventoryRepositoryInterface, ListOperationalProductsController, JsonResponse, ListOperationalProductsRequest

### Community 76 - "SalesRepository"
Cohesion: 0.14
Nodes (3): AdminSaleFilters, Collection, SalesRepository

### Community 77 - "Model"
Cohesion: 0.33
Nodes (4): CreateCategoryAction, CatalogRepositoryInterface, CreateCategoryController, JsonResponse

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
Cohesion: 0.18
Nodes (6): generateForSale(), StubFiscalReceiptGenerator, FiscalReceipt, BelongsTo, FiscalReceiptGeneratorInterface, Model

### Community 82 - "StoreRepository"
Cohesion: 0.07
Nodes (5): Product, AdminAuditLogTest, ProductCrudTest, ApplyPromotionToSaleTest, CompleteSaleTest

### Community 83 - "AdminRouteAccessTest"
Cohesion: 0.33
Nodes (4): PromotionsRepositoryInterface, ShowPromotionAction, JsonResponse, ShowPromotionController

### Community 84 - "LoginTest"
Cohesion: 0.13
Nodes (10): PromotionAuditSnapshot, create(), findByCode(), findById(), list(), listAppliedForSale(), Collection, update() (+2 more)

### Community 85 - "OperationalRouteAccessTest"
Cohesion: 0.24
Nodes (6): CreatePromotionAction, AuditLogRepositoryInterface, PromotionsRepositoryInterface, CreatePromotionController, JsonResponse, StorePromotionRequest

### Community 86 - "UpdateCustomerAction"
Cohesion: 0.24
Nodes (5): CustomersRepositoryInterface, UpdateCustomerAction, JsonResponse, UpdateCustomerController, UpdateCustomerRequest

### Community 87 - "SaleStatus.php"
Cohesion: 0.10
Nodes (16): Request, StoreRepositoryInterface, SelectStoreContextAction, OperationalPosController, JsonResponse, Request, EnsureStoreContext, Closure (+8 more)

### Community 88 - "CloseCashShiftTest"
Cohesion: 0.16
Nodes (3): SalesRepositoryInterface, RemoveSaleLineAction, SaleCartGuard

### Community 92 - "ListCategoriesAction"
Cohesion: 0.31
Nodes (5): ListCategoriesAction, CatalogRepositoryInterface, Collection, ListCategoriesController, JsonResponse

### Community 93 - "ResumeSaleAction"
Cohesion: 0.22
Nodes (9): dev, serve, test, Composer\\Config::disableProcessTimeout, npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74,#86efac\" \"php artisan serve\" \"php artisan queue:listen redis --tries=3 --timeout=0\" \"php artisan reverb:start\" \"php artisan pail --timeout=0\" \"npm run dev\" --names=server,queue,reverb,logs,vite --kill-others, @php artisan config:clear --ansi @no_additional_args, @php artisan serve --host=127.0.0.1 --port=8000, @php artisan test (+1 more)

### Community 94 - "DeleteCategoryAction"
Cohesion: 0.31
Nodes (5): SalesRepositoryInterface, ShowSaleAction, JsonResponse, Request, ShowSaleController

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
Cohesion: 0.27
Nodes (5): IdempotencyGuard, IdempotencyRecordRepositoryInterface, JsonResponse, Request, QueryException

### Community 103 - "CreateCustomerAction"
Cohesion: 0.27
Nodes (4): ConfirmManagerMfaSetupAction, Session, TotpAuthenticatorInterface, MfaRecoveryCodeVault

### Community 104 - "ListCustomersAction"
Cohesion: 0.20
Nodes (5): ListCustomersAction, CustomersRepositoryInterface, ListCustomersController, JsonResponse, ListCustomersRequest

### Community 105 - "CustomerResource"
Cohesion: 0.22
Nodes (5): CreateCustomerAction, CustomersRepositoryInterface, CreateCustomerController, JsonResponse, StoreCustomerRequest

### Community 106 - "CreateSaleAction"
Cohesion: 0.36
Nodes (5): NormalizedPaymentWebhook, normalize(), JsonPaymentWebhookPayloadNormalizer, PaymentWebhookEventType, PaymentWebhookPayloadNormalizerInterface

### Community 107 - "ShowSaleAction"
Cohesion: 0.19
Nodes (6): DispatchSaleSideEffects, BusOrchestrator, Batch, PendingBatch, PendingChain, DispatchSaleSideEffectsTest

### Community 108 - "FindCustomerByCpfAction"
Cohesion: 0.17
Nodes (6): FindCustomerByCpfAction, CustomersRepositoryInterface, CustomerPayloadNormalizer, FindOperationalCustomerController, JsonResponse, Request

### Community 109 - "ShowCustomerAction"
Cohesion: 0.33
Nodes (4): CustomersRepositoryInterface, ShowCustomerAction, JsonResponse, ShowCustomerController

### Community 110 - "OperationalRouteAccessTest"
Cohesion: 0.07
Nodes (12): BaseTestCase, ListAdminNotificationsTest, ExampleTest, CorrelationIdMiddlewareTest, PurgeExpiredIdempotencyRecordsTest, ListStoresTest, static, TestResponse (+4 more)

### Community 111 - "CustomerErrorCodeTest"
Cohesion: 0.32
Nodes (3): LoginController, JsonResponse, LoginRequest

### Community 112 - "ResumeSaleController.php"
Cohesion: 0.17
Nodes (5): StoreCategoryRequest, AttachCustomerToSaleRequest, CompleteSaleRequest, FormRequest, Validator

### Community 113 - "ListStoresTest"
Cohesion: 0.50
Nodes (3): Session, TotpAuthenticatorInterface, VerifyManagerMfaChallengeAction

### Community 117 - "ListPromotionsAction"
Cohesion: 0.20
Nodes (5): ListPromotionsAction, PromotionsRepositoryInterface, ListPromotionsController, JsonResponse, ListPromotionsRequest

### Community 118 - "PaymentMethod"
Cohesion: 0.22
Nodes (5): OpenCashShiftAction, CashShiftRepositoryInterface, OpenCashShiftController, JsonResponse, OpenCashShiftRequest

### Community 119 - "SaleResource"
Cohesion: 0.16
Nodes (4): updateCategory(), Category, HasMany, CatalogResource

### Community 120 - "Promotion.php"
Cohesion: 0.09
Nodes (13): findLineByTransactionReference(), markLineStatus(), markWebhookProcessed(), DateTimeInterface, PaymentLineStatus, recordWebhookEvent(), PaymentsRepository, DateTimeInterface (+5 more)

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
Cohesion: 0.22
Nodes (5): CreateUserAction, UsersRepositoryInterface, CreateUserController, JsonResponse, StoreUserRequest

### Community 128 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 129 - "ApiErrorResponse.php"
Cohesion: 0.11
Nodes (8): Model, PiiEncryptedDate, Model, PiiEncryptedString, PiiCrypto, CarbonInterface, CastsAttributes, Encrypter

### Community 130 - "PromotionDiscountCalculator"
Cohesion: 0.09
Nodes (6): Collection, PromotionDiscountCalculator, Money, PromotionResource, SaleResource, MoneyTest

### Community 131 - "PromotionErrorCodeTest"
Cohesion: 0.06
Nodes (8): Store, StorePolicy, OperationalCustomerTest, HoldSaleTest, SalesCartTest, actingAsManagerForStore(), attachManagerToStore(), static

### Community 132 - "RemovePromotionFromSaleController.php"
Cohesion: 0.56
Nodes (7): CompleteSaleAction, FiscalReceiptGeneratorInterface, InventoryRepositoryInterface, PaymentGatewayInterface, PaymentsRepositoryInterface, PendingPaymentOutboxInterface, SalesRepositoryInterface

### Community 133 - "autoload-dev"
Cohesion: 0.14
Nodes (12): ActsWithOperationalSession, InteractsWithStatefulApi, CloseCashShiftReportTest, CloseCashShiftTest, OpenCashShiftTest, OperationalShiftGateTest, CustomerPiiEncryptionTest, InventorySaleTest (+4 more)

### Community 134 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 135 - "require"
Cohesion: 0.20
Nodes (10): require, bacon/bacon-qr-code, laravel/framework, laravel/horizon, laravel/pulse, laravel/reverb, laravel/sanctum, laravel/tinker (+2 more)

### Community 136 - "HasFactory"
Cohesion: 0.09
Nodes (10): create(), findByCpf(), findById(), list(), Collection, update(), Customer, HasMany (+2 more)

### Community 137 - "post-create-project-cmd"
Cohesion: 0.29
Nodes (5): create(), findById(), list(), Collection, update()

### Community 139 - "AssignCorrelationId.php"
Cohesion: 0.53
Nodes (4): AssignCorrelationId, Closure, Request, Response

### Community 142 - "AttachCustomerToSaleAction"
Cohesion: 0.33
Nodes (5): AttachCustomerToSaleAction, CustomersRepositoryInterface, SalesRepositoryInterface, AttachCustomerToSaleController, JsonResponse

### Community 144 - "HoldSaleAction"
Cohesion: 0.22
Nodes (5): UsersRepositoryInterface, UpdateUserAction, JsonResponse, UpdateUserController, UpdateUserRequest

### Community 145 - "AssertManagerStoreAccess"
Cohesion: 0.13
Nodes (8): GetAdminDashboardMetricsAction, AdminDashboardMetrics, GetAdminShiftReportAction, CashShiftRepositoryInterface, SalesRepositoryInterface, ShowAdminSaleAction, AssertManagerStoreAccess, StoreRepositoryInterface

### Community 147 - "DeleteCategoryAction"
Cohesion: 0.20
Nodes (9): pulse.cache, pulse.exceptions, pulse.queues, pulse.servers, pulse.slow-jobs, pulse.slow-outgoing-requests, pulse.slow-queries, pulse.slow-requests (+1 more)

### Community 148 - "RemoveSaleLineAction"
Cohesion: 0.60
Nodes (3): JsonResponse, Request, ShowAdminShiftReportController

### Community 150 - "AuthenticationDomainException"
Cohesion: 0.06
Nodes (22): CreateRefundAction, AuditLogRepositoryInterface, InventoryRepositoryInterface, PaymentGatewayInterface, RefundsReturnsRepositoryInterface, create(), findCompletedSale(), listForSale() (+14 more)

### Community 151 - "ResumeSaleController.php"
Cohesion: 0.50
Nodes (4): extra, laravel, dont-discover, laravel/telescope

### Community 152 - "2026_07_16_165219_create_telescope_entries_table.php"
Cohesion: 0.83
Nodes (3): down(), getConnection(), up()

### Community 158 - "SaleCompletedNotification"
Cohesion: 0.15
Nodes (7): ListStoreInventoryAction, Collection, InventoryRepositoryInterface, ListStoreInventoryController, JsonResponse, ListStoreInventoryRequest, InventoryResource

### Community 160 - "SaleCompletedNotificationTest"
Cohesion: 0.22
Nodes (5): CloseCashShiftAction, CashShiftRepositoryInterface, CloseCashShiftController, JsonResponse, CloseCashShiftRequest

### Community 162 - "UsersRepository"
Cohesion: 0.60
Nodes (3): AuditLogRepositoryInterface, CashShiftRepositoryInterface, ReopenCashShiftAction

### Community 169 - "AppServiceProvider.php"
Cohesion: 0.22
Nodes (7): AdjustStoreInventoryAction, AuditLogRepositoryInterface, CatalogRepositoryInterface, InventoryRepositoryInterface, AdjustStoreInventoryController, JsonResponse, AdjustStoreInventoryRequest

### Community 172 - "AdminStoreAccessIdorTest"
Cohesion: 0.29
Nodes (5): LogoutUserAction, Request, LogoutController, JsonResponse, Request

### Community 174 - "ProbeQueuedJob"
Cohesion: 0.53
Nodes (4): EnsureUserHasRole, Closure, Request, Response

### Community 176 - "ShowCurrentUserController.php"
Cohesion: 0.22
Nodes (5): CreateSaleAction, SalesRepositoryInterface, CreateSaleController, JsonResponse, CreateSaleRequest

### Community 177 - "EnsureStoreContext.php"
Cohesion: 0.24
Nodes (5): HmacPaymentWebhookSignatureVerifier, AppServiceProvider, PaymentWebhookSignatureVerifierInterface, SalesRepositoryInterface, ServiceProvider

### Community 178 - "StoreDomainException"
Cohesion: 0.33
Nodes (4): DeleteProductAction, CatalogRepositoryInterface, DeleteProductController, JsonResponse

### Community 184 - "CustomersRepositoryInterface.php"
Cohesion: 0.23
Nodes (5): UsersRepositoryInterface, ShowUserAction, JsonResponse, ShowUserController, UserResource

### Community 185 - "AdjustStoreInventoryAction"
Cohesion: 0.22
Nodes (3): CashShiftRepository, Collection, CashShiftRepositoryInterface

### Community 187 - "StoreInventoryFactory"
Cohesion: 0.05
Nodes (12): assignedStoreIds(), findById(), listAccessibleForUser(), userCanAccessStore(), User, Authenticatable, AdminUserManagementTest, ListAdminSalesTest (+4 more)

### Community 191 - "AdminAuditLogTest"
Cohesion: 0.31
Nodes (3): InventoryRepository, Collection, InventoryRepositoryInterface

### Community 193 - "SelectStoreContextTest"
Cohesion: 0.36
Nodes (4): ShowCurrentUserAction, JsonResponse, Request, ShowCurrentUserController

### Community 194 - "SelectStoreContextTest"
Cohesion: 0.09
Nodes (21): CashShiftDomainException, ErrorCode, CatalogDomainException, ErrorCode, CustomerDomainException, ErrorCode, InventoryDomainException, ErrorCode (+13 more)

### Community 196 - "ListAdminShiftsAction"
Cohesion: 0.36
Nodes (3): AnalyticsRepositoryInterface, AnalyticsRepository, Collection

### Community 197 - "MoneyTest"
Cohesion: 0.22
Nodes (5): HoldSaleAction, SalesRepositoryInterface, HoldSaleController, JsonResponse, HoldSaleRequest

### Community 199 - "CreatePromotionAction"
Cohesion: 0.27
Nodes (6): AuditLogRepositoryInterface, PromotionsRepositoryInterface, UpdatePromotionAction, JsonResponse, UpdatePromotionController, UpdatePromotionRequest

### Community 207 - "ResumeSaleController.php"
Cohesion: 0.33
Nodes (4): DeleteCategoryAction, CatalogRepositoryInterface, DeleteCategoryController, JsonResponse

### Community 211 - "EnsureUserHasRole.php"
Cohesion: 0.12
Nodes (4): CustomerStoreStat, BelongsTo, AdminAnalyticsTest, DemoCatalogSeedersTest

### Community 212 - "CardInstrumentFormatGuardTest"
Cohesion: 0.60
Nodes (3): JsonResponse, Request, ShowAdminSaleController

### Community 213 - "post-autoload-dump"
Cohesion: 0.60
Nodes (3): JsonResponse, Request, ReopenCashShiftController

### Community 214 - "2026_07_15_300001_create_sales_table.php"
Cohesion: 0.11
Nodes (7): ActsWithAdminStoreAccess, RefreshDatabase, AdminDashboardMetricsTest, AdminShiftReportTest, ShowAdminSaleTest, AdminCatalogAccessTest, CreateCustomerActionTest

### Community 219 - "AdminDashboardController.php"
Cohesion: 0.60
Nodes (3): AdminDashboardController, JsonResponse, Request

### Community 221 - "RemovePromotionFromSaleAction"
Cohesion: 0.36
Nodes (6): PromotionsRepositoryInterface, SalesRepositoryInterface, RemovePromotionFromSaleAction, JsonResponse, Request, RemovePromotionFromSaleController

### Community 222 - "RemoveSaleLineController.php"
Cohesion: 0.60
Nodes (3): JsonResponse, Request, RemoveSaleLineController

### Community 223 - "ActsWithOperationalSession.php"
Cohesion: 0.60
Nodes (3): JsonResponse, Request, ResumeSaleController

### Community 235 - "ActsWithOperationalSession.php"
Cohesion: 0.70
Nodes (4): actingAsOperatorAtStore(), actingAsOperatorWithOpenShift(), static, withOpenShift()

## Knowledge Gaps
- **79 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+74 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **45 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `StoreInventoryFactory` to `PromotionErrorCodeTest`, `AuthenticationDomainException`, `UserFactory`, `HasFactory`, `post-create-project-cmd`, `AppServiceProvider.php`, `DatabaseSeeder.php`, `HoldSaleAction`, `AssertManagerStoreAccess`, `CatalogRepository.php`, `FormRequest`, `AuthenticationDomainException`, `SaleCompletedNotification`, `artisan`, `SaleCompletedNotificationTest`, `UsersRepository`, `CatalogRepositoryInterface.php`, `InventoryRepositoryInterface.php`, `PaymentsRepositoryInterface.php`, `RefundsReturnsRepositoryInterface.php`, `AppServiceProvider.php`, `DispatchSaleSideEffectsTest`, `ShowCurrentUserController.php`, `AdminStoreAccessIdorTest`, `CustomersRepositoryInterface.php`, `Sale`, `StoreRepository`, `CreateProductAction`, `Category`, `SelectStoreContextTest`, `AuthSecurityBaselineTest`, `CreatePromotionAction`, `OperationalStoreContextTest`, `UpdateProductAction`, `RemoveSaleLineAction`, `HoldSaleAction`, `ShowProductAction`, `ApplyPromotionToSaleAction`, `StoreRepository`, `EnsureUserHasRole.php`, `LoginTest`, `OperationalRouteAccessTest`, `HasFactory`, `SaleStatus.php`, `SelectStoreContextTest`, `ListAdminSalesAction`, `OperationalRouteAccessTest`, `CatalogResource`, `actingAsManagerForStore`, `CreateCustomerAction`, `ActsWithOperationalSession.php`, `ListStoresTest`, `PaymentMethod`, `Promotion.php`, `ListStoresTest`, `Sale.php`?**
  _High betweenness centrality (0.212) - this node is a cross-community bridge._
- **Why does `Controller` connect `SalesRepositoryInterface.php` to `User`, `DomainScaffolder`, `AttachCustomerToSaleAction`, `HoldSaleAction`, `CatalogRepository.php`, `RemoveSaleLineAction`, `AuthenticationDomainException`, `SaleCompletedNotification`, `SaleCompletedNotificationTest`, `CatalogRepositoryInterface.php`, `CustomersRepositoryInterface.php`, `InventoryRepositoryInterface.php`, `PaymentsRepositoryInterface.php`, `PromotionsRepositoryInterface.php`, `AppServiceProvider.php`, `AdminStoreAccessIdorTest`, `ShowCurrentUserController.php`, `StoreDomainException`, `CustomersRepositoryInterface.php`, `CreateSaleAction`, `SelectStoreContextTest`, `SaleErrorCodeTest`, `MoneyTest`, `CreatePromotionAction`, `UpdateProductAction`, `CatalogRepositoryInterface.php`, `SaleResource`, `Model`, `ShowCategoryAction`, `ResumeSaleController.php`, `ShowProductAction`, `AdminRouteAccessTest`, `CardInstrumentFormatGuardTest`, `post-autoload-dump`, `UpdateCustomerAction`, `SaleStatus.php`, `OperationalRouteAccessTest`, `AdminDashboardController.php`, `ListCategoriesAction`, `RemovePromotionFromSaleAction`, `RemoveSaleLineController.php`, `DeleteProductAction`, `ShowCategoryAction`, `CurrentCashShiftController.php`, `ShowProductAction`, `CatalogResource`, `ActsWithOperationalSession.php`, `DeleteCategoryAction`, `ListCustomersAction`, `CustomerResource`, `AdminAnalyticsTest`, `AdminInventoryTest`, `FindCustomerByCpfAction`, `ShowCustomerAction`, `CustomerErrorCodeTest`, `ListPromotionsAction`, `PaymentMethod`, `ApplyPromotionToSaleAction`, `Sale.php`?**
  _High betweenness centrality (0.164) - this node is a cross-community bridge._
- **Why does `TestCase` connect `OperationalRouteAccessTest` to `PromotionDiscountCalculator`, `PromotionErrorCodeTest`, `StoreContext`, `autoload-dev`, `HasFactory`, `AuthErrorCodeTest`, `StoreErrorCodeTest`, `RefundErrorCodeTest`, `FormRequest`, `artisan`, `BusOrchestratorTest`, `CashShiftRepositoryInterface.php`, `InventoryErrorCodeTest`, `PromotionErrorCodeTest`, `CompleteSaleTest`, `AdminStoreAccessIdorTest`, `RefundsReturnsRepositoryInterface.php`, `StoreInventoryFactory`, `Category`, `AuthSecurityBaselineTest`, `CatalogErrorCodeTest`, `InventoryErrorCodeTest`, `StoreErrorCodeTest`, `OperationalStoreContextTest`, `HoldSaleAction`, `ApplyPromotionToSaleAction`, `CatalogResource`, `StoreRepository`, `EnsureUserHasRole.php`, `HasFactory`, `2026_07_15_300001_create_sales_table.php`, `SelectStoreContextTest`, `ListAdminSalesAction`, `actingAsManagerForStore`, `ShowSaleAction`, `SalePaymentValidator`?**
  _High betweenness centrality (0.130) - this node is a cross-community bridge._
- **Are the 212 inferred relationships involving `User` (e.g. with `.execute()` and `.handle()`) actually correct?**
  _`User` has 212 INFERRED edges - model-reasoned connections that need verification._
- **Are the 137 inferred relationships involving `Store` (e.g. with `.topCustomersBySpend()` and `.definition()`) actually correct?**
  _`Store` has 137 INFERRED edges - model-reasoned connections that need verification._
- **Are the 76 inferred relationships involving `Product` (e.g. with `.definition()` and `.run()`) actually correct?**
  _`Product` has 76 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _79 weakly-connected nodes found - possible documentation gaps or missing edges._