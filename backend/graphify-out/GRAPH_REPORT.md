# Graph Report - projects\pdv\backend  (2026-07-20)

## Corpus Check
- 489 files · ~67,569 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2625 nodes · 5170 edges · 241 communities (195 shown, 46 thin omitted)
- Extraction: 86% EXTRACTED · 14% INFERRED · 0% AMBIGUOUS · INFERRED: 717 edges (avg confidence: 0.8)
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
- HoldSaleTest
- CardInstrumentFormatGuardTest
- post-autoload-dump
- .completedSaleFixture
- SelectStoreContextTest
- AdminDashboardController.php
- DemoUserSeedersTest
- RemovePromotionFromSaleAction
- ActsWithOperationalSession.php
- ProbeQueuedJob
- .__invoke
- WebhookRetryQueueInterface.php
- SaleAnalyticsRecorderInterface
- DatabaseSeeder
- WebhookRetryQueueInterface.php
- AdminAnalyticsTest
- HmacPaymentWebhookSignatureVerifier
- DispatchSaleSideEffectsTest
- AdminInventoryTest
- CatalogResource
- .__invoke
- User.php
- actingAsManagerForStore

## God Nodes (most connected - your core abstractions)
1. `User` - 325 edges
2. `Store` - 165 edges
3. `TestCase` - 157 edges
4. `Controller` - 133 edges
5. `Sale` - 106 edges
6. `Product` - 105 edges
7. `Customer` - 71 edges
8. `Money` - 50 edges
9. `Promotion` - 50 edges
10. `AssertManagerStoreAccess` - 43 edges

## Surprising Connections (you probably didn't know these)
- `findCategoryById()` --references--> `Category`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Category.php
- `deleteCategory()` --references--> `Category`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Category.php
- `findProductById()` --references--> `Product`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Product.php
- `createProduct()` --references--> `Product`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Product.php
- `updateProduct()` --references--> `Product`  [EXTRACTED]
  projects/pdv/backend/app/Domain/Catalog/Repositories/CatalogRepositoryInterface.php → projects/pdv/backend/app/Models/Product.php

## Import Cycles
- None detected.

## Communities (241 total, 46 thin omitted)

### Community 0 - "User"
Cohesion: 0.32
Nodes (3): JsonResponse, UpdateSaleLineController, UpdateSaleLineRequest

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
Cohesion: 0.12
Nodes (14): addLine(), attachCustomer(), complete(), createInProgress(), findById(), hold(), listForAdmin(), listHeldForShift() (+6 more)

### Community 7 - "UserFactory"
Cohesion: 0.06
Nodes (18): CashShiftFactory, static, CategoryFactory, static, CustomerFactory, static, ProductFactory, static (+10 more)

### Community 8 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 9 - "DomainScaffolder"
Cohesion: 0.07
Nodes (14): AdminAuditFilters, AuditLogEntry, append(), listForAdmin(), ListAdminAuditLogsController, JsonResponse, ListAdminAuditLogsRequest, AuditLogRepository (+6 more)

### Community 10 - "AppServiceProvider.php"
Cohesion: 0.23
Nodes (9): NotifyManagersOfSaleCompleted, SaleCompleted, AbstractQueuedJob, Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels (+1 more)

### Community 11 - "ListAccessibleStoresAction"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "DatabaseSeeder.php"
Cohesion: 0.14
Nodes (9): DatabaseSeeder, DemoCatalogSeeder, DemoCustomerSeeder, DemoInventorySeeder, DemoStoreSeeder, ManagerUserSeeder, OperatorUserSeeder, Seeder (+1 more)

### Community 15 - "AnalyticsRepositoryInterface"
Cohesion: 0.19
Nodes (3): UpdatePromotionRequest, BelongsToMany, HasMany

### Community 16 - "CashShiftRepository.php"
Cohesion: 0.05
Nodes (26): CloseCashShiftAction, CashShiftRepositoryInterface, OpenCashShiftAction, CashShiftRepositoryInterface, ShiftClosingReport, buildClosingReport(), close(), createOpen() (+18 more)

### Community 17 - "CatalogRepository.php"
Cohesion: 0.19
Nodes (6): ListAdminShiftsAction, CashShiftRepositoryInterface, Collection, ListAdminShiftsController, JsonResponse, ListAdminShiftsRequest

### Community 18 - "CustomersRepository.php"
Cohesion: 0.11
Nodes (9): Model, PiiEncryptedDate, Model, PiiEncryptedString, PiiCrypto, CarbonInterface, CastsAttributes, DemoPromotionSeeder (+1 more)

### Community 20 - "InventoryRepository.php"
Cohesion: 0.12
Nodes (6): PendingPaymentOutboxEntry, self, push(), InMemoryPendingPaymentOutbox, RedisPendingPaymentOutbox, PendingPaymentOutboxInterface

### Community 21 - "PaymentsRepository.php"
Cohesion: 0.23
Nodes (8): AuthenticationDomainException, ErrorCode, ApiErrorResponse, ErrorCode, JsonResponse, Request, AuthenticationException, AuthorizationException

### Community 22 - "PromotionsRepository.php"
Cohesion: 0.12
Nodes (12): attachToSale(), create(), findApplied(), findByCode(), findById(), list(), listAppliedForSale(), Collection (+4 more)

### Community 23 - "RefundsReturnsRepository.php"
Cohesion: 0.13
Nodes (3): CatalogRepository, Collection, CatalogRepositoryInterface

### Community 24 - "SalesRepository.php"
Cohesion: 0.14
Nodes (9): createProduct(), deleteCategory(), deleteProduct(), findCategoryById(), findProductById(), listCategories(), listProducts(), Collection (+1 more)

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
Cohesion: 0.28
Nodes (3): ListAdminSalesController, JsonResponse, ListAdminSalesRequest

### Community 38 - "PaymentsRepositoryInterface.php"
Cohesion: 0.21
Nodes (6): AuditLogRepositoryInterface, CatalogRepositoryInterface, UpdateProductAction, JsonResponse, UpdateProductController, UpdateProductRequest

### Community 39 - "PromotionsRepositoryInterface.php"
Cohesion: 0.32
Nodes (3): AddSaleLineController, JsonResponse, AddSaleLineRequest

### Community 40 - "RefundsReturnsRepositoryInterface.php"
Cohesion: 0.22
Nodes (3): SalesRepositoryInterface, RemoveSaleLineAction, SaleCartGuard

### Community 41 - "SalesRepositoryInterface.php"
Cohesion: 0.23
Nodes (5): ConfirmManagerMfaSetupController, JsonResponse, JsonResponse, VerifyManagerMfaChallengeController, MfaCodeRequest

### Community 57 - "CreateSaleAction"
Cohesion: 0.20
Nodes (5): ListProductsAction, CatalogRepositoryInterface, ListProductsController, JsonResponse, ListProductsRequest

### Community 61 - "Sale"
Cohesion: 0.12
Nodes (11): claimProcessing(), delete(), deleteCreatedBefore(), findByScopeAndKey(), markCompleted(), DateTimeInterface, EloquentIdempotencyRecordRepository, DateTimeInterface (+3 more)

### Community 62 - "Category"
Cohesion: 0.13
Nodes (3): LoginTest, SessionGateTest, TotpAuthenticatorInterface

### Community 63 - "CreateProductAction"
Cohesion: 0.15
Nodes (8): GetAdminAnalyticsAction, AnalyticsRepositoryInterface, AdminAnalyticsSnapshot, CustomerSpendRow, adminSnapshot(), listCampaignCustomers(), Collection, AnalyticsResource

### Community 66 - "SaleErrorCodeTest"
Cohesion: 0.20
Nodes (6): ApplyPromotionToSaleAction, PromotionsRepositoryInterface, SalesRepositoryInterface, ApplyPromotionToSaleController, JsonResponse, ApplyPromotionToSaleRequest

### Community 73 - "UpdateProductAction"
Cohesion: 0.27
Nodes (5): IdempotencyGuard, IdempotencyRecordRepositoryInterface, JsonResponse, Request, QueryException

### Community 74 - "CatalogRepositoryInterface.php"
Cohesion: 0.22
Nodes (5): CatalogRepositoryInterface, UpdateCategoryAction, JsonResponse, UpdateCategoryController, UpdateCategoryRequest

### Community 75 - "SaleResource"
Cohesion: 0.29
Nodes (6): ListHeldSalesAction, Collection, SalesRepositoryInterface, ListHeldSalesController, JsonResponse, Request

### Community 76 - "SalesRepository"
Cohesion: 0.10
Nodes (10): AdminSaleFilters, findLineById(), findLineByProduct(), removeLine(), updateLineQuantity(), Collection, SalesRepository, BelongsTo (+2 more)

### Community 77 - "Model"
Cohesion: 0.22
Nodes (5): CreateCategoryAction, CatalogRepositoryInterface, CreateCategoryController, JsonResponse, StoreCategoryRequest

### Community 78 - "ListCategoriesAction"
Cohesion: 0.22
Nodes (6): AuditLogRepositoryInterface, UsersRepositoryInterface, ResetManagerMfaAction, JsonResponse, ResetManagerMfaController, ResetManagerMfaRequest

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
Cohesion: 0.05
Nodes (3): Product, ProductCrudTest, CompleteSaleTest

### Community 83 - "AdminRouteAccessTest"
Cohesion: 0.33
Nodes (4): PromotionsRepositoryInterface, ShowPromotionAction, JsonResponse, ShowPromotionController

### Community 84 - "LoginTest"
Cohesion: 0.12
Nodes (6): PromotionAuditSnapshot, Collection, PromotionsRepository, DateTimeInterface, Promotion, PromotionsRepositoryInterface

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
Cohesion: 0.21
Nodes (3): CustomersRepository, Collection, CustomersRepositoryInterface

### Community 92 - "ListCategoriesAction"
Cohesion: 0.31
Nodes (5): ListCategoriesAction, CatalogRepositoryInterface, Collection, ListCategoriesController, JsonResponse

### Community 93 - "ResumeSaleAction"
Cohesion: 0.22
Nodes (9): dev, serve, test, Composer\\Config::disableProcessTimeout, npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74,#86efac\" \"php artisan serve\" \"php artisan queue:listen redis --tries=3 --timeout=0\" \"php artisan reverb:start\" \"php artisan pail --timeout=0\" \"npm run dev\" --names=server,queue,reverb,logs,vite --kill-others, @php artisan config:clear --ansi @no_additional_args, @php artisan serve --host=127.0.0.1 --port=8000, @php artisan test (+1 more)

### Community 94 - "DeleteCategoryAction"
Cohesion: 0.33
Nodes (4): DeleteCategoryAction, CatalogRepositoryInterface, DeleteCategoryController, JsonResponse

### Community 95 - "DeleteProductAction"
Cohesion: 0.20
Nodes (5): ListUsersAction, UsersRepositoryInterface, ListUsersController, JsonResponse, ListUsersRequest

### Community 96 - "ShowCategoryAction"
Cohesion: 0.33
Nodes (4): CatalogRepositoryInterface, ShowCategoryAction, JsonResponse, ShowCategoryController

### Community 97 - "CurrentCashShiftController.php"
Cohesion: 0.53
Nodes (4): CurrentCashShiftController, CashShiftRepositoryInterface, JsonResponse, Request

### Community 99 - "CatalogResource"
Cohesion: 0.52
Nodes (4): ListRefundsForSaleAction, Collection, RefundsReturnsRepositoryInterface, SalesRepositoryInterface

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
Cohesion: 0.22
Nodes (5): CreateCustomerAction, CustomersRepositoryInterface, CreateCustomerController, JsonResponse, StoreCustomerRequest

### Community 106 - "CreateSaleAction"
Cohesion: 0.36
Nodes (5): NormalizedPaymentWebhook, normalize(), JsonPaymentWebhookPayloadNormalizer, PaymentWebhookEventType, PaymentWebhookPayloadNormalizerInterface

### Community 107 - "ShowSaleAction"
Cohesion: 0.25
Nodes (5): DispatchSaleSideEffects, BusOrchestrator, Batch, PendingBatch, PendingChain

### Community 108 - "FindCustomerByCpfAction"
Cohesion: 0.18
Nodes (6): FindCustomerByCpfAction, CustomersRepositoryInterface, CustomerPayloadNormalizer, FindOperationalCustomerController, JsonResponse, Request

### Community 109 - "ShowCustomerAction"
Cohesion: 0.19
Nodes (5): CustomersRepositoryInterface, ShowCustomerAction, JsonResponse, ShowCustomerController, CustomerResource

### Community 110 - "OperationalRouteAccessTest"
Cohesion: 0.11
Nodes (15): ActsWithOperationalSession, InteractsWithStatefulApi, ResetManagerMfaTest, CloseCashShiftReportTest, CloseCashShiftTest, OpenCashShiftTest, OperationalShiftGateTest, OperationalCatalogTest (+7 more)

### Community 111 - "CustomerErrorCodeTest"
Cohesion: 0.32
Nodes (3): LoginController, JsonResponse, LoginRequest

### Community 112 - "ResumeSaleController.php"
Cohesion: 0.24
Nodes (4): AttachCustomerToSaleRequest, CompleteSaleRequest, FormRequest, Validator

### Community 113 - "ListStoresTest"
Cohesion: 0.50
Nodes (3): Session, TotpAuthenticatorInterface, VerifyManagerMfaChallengeAction

### Community 117 - "ListPromotionsAction"
Cohesion: 0.20
Nodes (5): ListPromotionsAction, PromotionsRepositoryInterface, ListPromotionsController, JsonResponse, ListPromotionsRequest

### Community 118 - "PaymentMethod"
Cohesion: 0.13
Nodes (7): BelongsTo, BelongsTo, StockAdjustment, BelongsToMany, HasFactory, HasOne, Model

### Community 119 - "SaleResource"
Cohesion: 0.17
Nodes (5): createCategory(), updateCategory(), Category, HasMany, CatalogResource

### Community 120 - "Promotion.php"
Cohesion: 0.15
Nodes (7): findLineByTransactionReference(), markLineStatus(), markWebhookProcessed(), DateTimeInterface, PaymentLineStatus, recordWebhookEvent(), PaymentWebhookEvent

### Community 121 - "ApplyPromotionToSaleAction"
Cohesion: 0.19
Nodes (6): HmacPaymentWebhookSignatureVerifier, LogSaleAnalyticsRecorder, AppServiceProvider, PaymentWebhookSignatureVerifierInterface, SaleAnalyticsRecorderInterface, ServiceProvider

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

### Community 130 - "PromotionDiscountCalculator"
Cohesion: 0.09
Nodes (6): Collection, PromotionDiscountCalculator, Money, PromotionResource, SaleResource, MoneyTest

### Community 131 - "PromotionErrorCodeTest"
Cohesion: 0.05
Nodes (9): assignedStoreIds(), findById(), listAccessibleForUser(), userCanAccessStore(), Store, User, StorePolicy, Authenticatable (+1 more)

### Community 132 - "RemovePromotionFromSaleController.php"
Cohesion: 0.42
Nodes (7): CompleteSaleAction, FiscalReceiptGeneratorInterface, InventoryRepositoryInterface, PaymentGatewayInterface, PaymentsRepositoryInterface, PendingPaymentOutboxInterface, SalesRepositoryInterface

### Community 133 - "autoload-dev"
Cohesion: 0.20
Nodes (6): ListOperationalProductsAction, CatalogRepositoryInterface, InventoryRepositoryInterface, ListOperationalProductsController, JsonResponse, ListOperationalProductsRequest

### Community 134 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 135 - "require"
Cohesion: 0.20
Nodes (10): require, bacon/bacon-qr-code, laravel/framework, laravel/horizon, laravel/pulse, laravel/reverb, laravel/sanctum, laravel/tinker (+2 more)

### Community 136 - "HasFactory"
Cohesion: 0.09
Nodes (11): AnonymizeExpiredCustomersAction, create(), findByCpf(), findById(), list(), Collection, update(), Customer (+3 more)

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
Cohesion: 0.17
Nodes (8): ListAdminAuditLogsAction, AuditLogRepositoryInterface, GetAdminShiftReportAction, CashShiftRepositoryInterface, SalesRepositoryInterface, ShowAdminSaleAction, AssertManagerStoreAccess, StoreRepositoryInterface

### Community 147 - "DeleteCategoryAction"
Cohesion: 0.20
Nodes (9): pulse.cache, pulse.exceptions, pulse.queues, pulse.servers, pulse.slow-jobs, pulse.slow-outgoing-requests, pulse.slow-queries, pulse.slow-requests (+1 more)

### Community 148 - "RemoveSaleLineAction"
Cohesion: 0.19
Nodes (5): AnalyticsRepositoryInterface, AnalyticsRepository, Collection, CustomerStoreStat, BelongsTo

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
Cohesion: 0.19
Nodes (6): ListStoreInventoryAction, Collection, InventoryRepositoryInterface, ListStoreInventoryController, JsonResponse, ListStoreInventoryRequest

### Community 160 - "SaleCompletedNotificationTest"
Cohesion: 0.31
Nodes (5): SalesRepositoryInterface, ShowSaleAction, JsonResponse, Request, ShowSaleController

### Community 162 - "UsersRepository"
Cohesion: 0.60
Nodes (3): AuditLogRepositoryInterface, CashShiftRepositoryInterface, ReopenCashShiftAction

### Community 169 - "AppServiceProvider.php"
Cohesion: 0.22
Nodes (7): AdjustStoreInventoryAction, AuditLogRepositoryInterface, CatalogRepositoryInterface, InventoryRepositoryInterface, AdjustStoreInventoryController, JsonResponse, AdjustStoreInventoryRequest

### Community 172 - "AdminStoreAccessIdorTest"
Cohesion: 0.33
Nodes (5): LogoutUserAction, Request, LogoutController, JsonResponse, Request

### Community 174 - "ProbeQueuedJob"
Cohesion: 0.53
Nodes (4): EnsureUserHasRole, Closure, Request, Response

### Community 176 - "ShowCurrentUserController.php"
Cohesion: 0.24
Nodes (6): PaymentsRepository, DateTimeInterface, PaymentLineStatus, PaymentLine, BelongsTo, PaymentsRepositoryInterface

### Community 178 - "StoreDomainException"
Cohesion: 0.33
Nodes (4): DeleteProductAction, CatalogRepositoryInterface, DeleteProductController, JsonResponse

### Community 183 - "AdminStoreAccessIdorTest"
Cohesion: 0.31
Nodes (5): AddSaleLineAction, CatalogRepositoryInterface, SalesRepositoryInterface, CreateSaleAction, SalesRepositoryInterface

### Community 184 - "CustomersRepositoryInterface.php"
Cohesion: 0.23
Nodes (5): UsersRepositoryInterface, ShowUserAction, JsonResponse, ShowUserController, UserResource

### Community 185 - "AdjustStoreInventoryAction"
Cohesion: 0.07
Nodes (11): BaseTestCase, AdminRouteAccessTest, ExampleTest, CorrelationIdMiddlewareTest, static, TestResponse, TestCase, CreateCustomerActionTest (+3 more)

### Community 191 - "AdminAuditLogTest"
Cohesion: 0.07
Nodes (13): adjustQuantity(), findForStoreProduct(), listForStore(), mapForStoreProducts(), Collection, InventoryRepository, Collection, BelongsTo (+5 more)

### Community 192 - "AuthSecurityBaselineTest"
Cohesion: 0.24
Nodes (5): LoginUserAction, Session, TurnstileVerifierInterface, MfaPendingSession, Session

### Community 193 - "SelectStoreContextTest"
Cohesion: 0.36
Nodes (4): ShowCurrentUserAction, JsonResponse, Request, ShowCurrentUserController

### Community 194 - "SelectStoreContextTest"
Cohesion: 0.09
Nodes (21): CashShiftDomainException, ErrorCode, CatalogDomainException, ErrorCode, CustomerDomainException, ErrorCode, InventoryDomainException, ErrorCode (+13 more)

### Community 196 - "ListAdminShiftsAction"
Cohesion: 0.09
Nodes (7): ActsWithAdminStoreAccess, AdminAnalyticsTest, AdminDashboardMetricsTest, AdminShiftReportTest, ListAdminSalesTest, ShowAdminSaleTest, RefundThrottleAndSalesIdorTest

### Community 197 - "MoneyTest"
Cohesion: 0.32
Nodes (3): HoldSaleController, JsonResponse, HoldSaleRequest

### Community 199 - "CreatePromotionAction"
Cohesion: 0.39
Nodes (5): AuditLogRepositoryInterface, PromotionsRepositoryInterface, UpdatePromotionAction, JsonResponse, UpdatePromotionController

### Community 202 - "RemoveSaleLineAction"
Cohesion: 0.27
Nodes (6): JsonResponse, ShowProductController, Controller, JsonResponse, Request, RemoveSaleLineController

### Community 203 - "ShowAdminShiftReportController.php"
Cohesion: 0.28
Nodes (3): GetAdminAnalyticsController, JsonResponse, GetAdminAnalyticsRequest

### Community 204 - "ListAdminNotificationsController.php"
Cohesion: 0.60
Nodes (3): JsonResponse, Request, ReopenCashShiftController

### Community 208 - "ApplyPromotionToSaleAction"
Cohesion: 0.06
Nodes (10): RefreshDatabase, ListAdminNotificationsTest, AdminCatalogAccessTest, CategoryCrudTest, ApiVersionAliasTest, DemoCatalogSeedersTest, DemoUserSeedersTest, PurgeExpiredIdempotencyRecordsTest (+2 more)

### Community 212 - "CardInstrumentFormatGuardTest"
Cohesion: 0.53
Nodes (3): ListAdminSalesAction, Collection, SalesRepositoryInterface

### Community 217 - "SelectStoreContextTest"
Cohesion: 0.60
Nodes (3): JsonResponse, Request, ShowAdminShiftReportController

### Community 219 - "AdminDashboardController.php"
Cohesion: 0.21
Nodes (5): GetAdminDashboardMetricsAction, AdminDashboardMetrics, AdminDashboardController, JsonResponse, Request

### Community 221 - "RemovePromotionFromSaleAction"
Cohesion: 0.31
Nodes (6): PromotionsRepositoryInterface, SalesRepositoryInterface, RemovePromotionFromSaleAction, JsonResponse, Request, RemovePromotionFromSaleController

### Community 226 - ".__invoke"
Cohesion: 0.60
Nodes (3): ListAdminNotificationsController, JsonResponse, Request

### Community 228 - "SaleAnalyticsRecorderInterface"
Cohesion: 0.70
Nodes (4): actingAsOperatorAtStore(), actingAsOperatorWithOpenShift(), static, withOpenShift()

### Community 229 - "DatabaseSeeder"
Cohesion: 0.60
Nodes (3): ListRefundsForSaleController, JsonResponse, Request

### Community 230 - "WebhookRetryQueueInterface.php"
Cohesion: 0.60
Nodes (3): JsonResponse, Request, ResumeSaleController

### Community 232 - "AdminAnalyticsTest"
Cohesion: 0.05
Nodes (29): ConsumePaymentWebhookAction, PaymentsRepositoryInterface, PaymentWebhookPayloadNormalizerInterface, PaymentWebhookSignatureVerifierInterface, PendingPaymentOutboxInterface, ErrorCode, PaymentGatewayInterface, PaymentsRepositoryInterface (+21 more)

### Community 233 - "HmacPaymentWebhookSignatureVerifier"
Cohesion: 0.60
Nodes (3): JsonResponse, Request, ShowAdminSaleController

### Community 239 - "actingAsManagerForStore"
Cohesion: 0.67
Nodes (3): actingAsManagerForStore(), attachManagerToStore(), static

## Knowledge Gaps
- **79 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+74 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **46 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `PromotionErrorCodeTest` to `ApiErrorResponse.php`, `AuthenticationDomainException`, `HasFactory`, `post-create-project-cmd`, `AppServiceProvider.php`, `DomainScaffolder`, `CashShiftRepository.php`, `AssertManagerStoreAccess`, `CatalogRepository.php`, `HoldSaleAction`, `FormRequest`, `AuthenticationDomainException`, `SaleCompletedNotification`, `artisan`, `UsersRepository`, `CatalogRepositoryInterface.php`, `PaymentsRepositoryInterface.php`, `AppServiceProvider.php`, `EnsureStoreContext.php`, `AdminStoreAccessIdorTest`, `CustomersRepositoryInterface.php`, `AdjustStoreInventoryAction`, `RefundsReturnsRepositoryInterface.php`, `StoreInventoryFactory`, `Sale`, `StoreRepository`, `CreateProductAction`, `AuthSecurityBaselineTest`, `SelectStoreContextTest`, `Category`, `AdminAuditLogTest`, `ListAdminShiftsAction`, `CreatePromotionAction`, `OperationalStoreContextTest`, `RemoveSaleLineController.php`, `ListCategoriesAction`, `ResumeSaleController.php`, `ShowProductAction`, `ApplyPromotionToSaleAction`, `StoreRepository`, `CatalogResource`, `LoginTest`, `OperationalRouteAccessTest`, `CardInstrumentFormatGuardTest`, `SaleStatus.php`, `HasFactory`, `HoldSaleTest`, `.completedSaleFixture`, `AdminDashboardController.php`, `CatalogResource`, `SaleAnalyticsRecorderInterface`, `CreateCustomerAction`, `AdminInventoryTest`, `User.php`, `OperationalRouteAccessTest`, `actingAsManagerForStore`, `ListStoresTest`, `PaymentMethod`, `SaleResource`, `ListStoresTest`, `Sale.php`?**
  _High betweenness centrality (0.218) - this node is a cross-community bridge._
- **Why does `Controller` connect `RemoveSaleLineAction` to `User`, `autoload-dev`, `DomainScaffolder`, `AttachCustomerToSaleAction`, `CashShiftRepository.php`, `CatalogRepository.php`, `HoldSaleAction`, `AuthenticationDomainException`, `SaleCompletedNotification`, `SaleCompletedNotificationTest`, `CatalogRepositoryInterface.php`, `CustomersRepositoryInterface.php`, `InventoryRepositoryInterface.php`, `PaymentsRepositoryInterface.php`, `PromotionsRepositoryInterface.php`, `SalesRepositoryInterface.php`, `AppServiceProvider.php`, `AdminStoreAccessIdorTest`, `StoreDomainException`, `CustomersRepositoryInterface.php`, `CreateSaleAction`, `SelectStoreContextTest`, `SaleErrorCodeTest`, `MoneyTest`, `CreatePromotionAction`, `CatalogRepositoryInterface.php`, `ShowAdminShiftReportController.php`, `ListAdminNotificationsController.php`, `Model`, `ListCategoriesAction`, `ShowCategoryAction`, `SaleResource`, `ShowProductAction`, `AdminRouteAccessTest`, `OperationalRouteAccessTest`, `UpdateCustomerAction`, `SaleStatus.php`, `SelectStoreContextTest`, `AdminDashboardController.php`, `ListCategoriesAction`, `RemovePromotionFromSaleAction`, `DeleteCategoryAction`, `DeleteProductAction`, `ShowCategoryAction`, `CurrentCashShiftController.php`, `.__invoke`, `DatabaseSeeder`, `UpdateSaleLineAction`, `WebhookRetryQueueInterface.php`, `ListCustomersAction`, `CustomerResource`, `AdminAnalyticsTest`, `HmacPaymentWebhookSignatureVerifier`, `FindCustomerByCpfAction`, `ShowCustomerAction`, `.__invoke`, `CustomerErrorCodeTest`, `ListPromotionsAction`, `Sale.php`?**
  _High betweenness centrality (0.161) - this node is a cross-community bridge._
- **Why does `TestCase` connect `AdjustStoreInventoryAction` to `ApiErrorResponse.php`, `PromotionDiscountCalculator`, `PromotionErrorCodeTest`, `StoreContext`, `HasFactory`, `DomainScaffolder`, `AuthErrorCodeTest`, `StoreErrorCodeTest`, `RefundErrorCodeTest`, `FormRequest`, `artisan`, `BusOrchestratorTest`, `CashShiftRepositoryInterface.php`, `InventoryErrorCodeTest`, `PromotionErrorCodeTest`, `CompleteSaleTest`, `EnsureStoreContext.php`, `RefundsReturnsRepositoryInterface.php`, `StoreInventoryFactory`, `Category`, `AdminAuditLogTest`, `CatalogErrorCodeTest`, `InventoryErrorCodeTest`, `ListAdminShiftsAction`, `StoreErrorCodeTest`, `OperationalStoreContextTest`, `HoldSaleAction`, `RemoveSaleLineController.php`, `ResumeSaleController.php`, `ApplyPromotionToSaleAction`, `CatalogResource`, `StoreRepository`, `HasFactory`, `HoldSaleTest`, `.completedSaleFixture`, `DispatchSaleSideEffectsTest`, `AdminInventoryTest`, `ShowSaleAction`, `OperationalRouteAccessTest`, `SalePaymentValidator`?**
  _High betweenness centrality (0.128) - this node is a cross-community bridge._
- **Are the 225 inferred relationships involving `User` (e.g. with `.execute()` and `.handle()`) actually correct?**
  _`User` has 225 INFERRED edges - model-reasoned connections that need verification._
- **Are the 145 inferred relationships involving `Store` (e.g. with `.topCustomersBySpend()` and `.definition()`) actually correct?**
  _`Store` has 145 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _79 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.14285714285714285 - nodes in this community are weakly interconnected._