# Graph Report - projects\pdv\backend  (2026-07-20)

## Corpus Check
- 474 files · ~65,166 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2549 nodes · 5016 edges · 237 communities (190 shown, 47 thin omitted)
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
3. `TestCase` - 145 edges
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

## Communities (237 total, 47 thin omitted)

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

### Community 6 - "AuthenticationDomainException"
Cohesion: 0.10
Nodes (17): addLine(), attachCustomer(), complete(), createInProgress(), findById(), findLineById(), findLineByProduct(), hold() (+9 more)

### Community 7 - "UserFactory"
Cohesion: 0.06
Nodes (16): CashShiftFactory, static, CategoryFactory, static, CustomerFactory, static, ProductFactory, static (+8 more)

### Community 8 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 9 - "DomainScaffolder"
Cohesion: 0.05
Nodes (28): ConsumePaymentWebhookAction, PaymentsRepositoryInterface, PaymentWebhookPayloadNormalizerInterface, PaymentWebhookSignatureVerifierInterface, PendingPaymentOutboxInterface, ErrorCode, PaymentGatewayInterface, PaymentsRepositoryInterface (+20 more)

### Community 10 - "AppServiceProvider.php"
Cohesion: 0.23
Nodes (9): NotifyManagersOfSaleCompleted, SaleCompleted, AbstractQueuedJob, Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels (+1 more)

### Community 11 - "ListAccessibleStoresAction"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "DatabaseSeeder.php"
Cohesion: 0.22
Nodes (5): PaymentWebhookEvent, BelongsTo, BelongsToMany, HasFactory, Model

### Community 15 - "AnalyticsRepositoryInterface"
Cohesion: 0.14
Nodes (5): BelongsToMany, HasMany, static, PromotionFactory, DemoPromotionSeeder

### Community 16 - "CashShiftRepository.php"
Cohesion: 0.07
Nodes (21): OpenCashShiftAction, CashShiftRepositoryInterface, ShiftClosingReport, buildClosingReport(), close(), createOpen(), findById(), findOpenForUser() (+13 more)

### Community 17 - "CatalogRepository.php"
Cohesion: 0.28
Nodes (3): ListAdminShiftsController, JsonResponse, ListAdminShiftsRequest

### Community 18 - "CustomersRepository.php"
Cohesion: 0.23
Nodes (3): CustomersRepository, Collection, CustomersRepositoryInterface

### Community 20 - "InventoryRepository.php"
Cohesion: 0.12
Nodes (6): PendingPaymentOutboxEntry, self, push(), InMemoryPendingPaymentOutbox, RedisPendingPaymentOutbox, PendingPaymentOutboxInterface

### Community 21 - "PaymentsRepository.php"
Cohesion: 0.23
Nodes (8): AuthenticationDomainException, ErrorCode, ApiErrorResponse, ErrorCode, JsonResponse, Request, AuthenticationException, AuthorizationException

### Community 22 - "PromotionsRepository.php"
Cohesion: 0.11
Nodes (8): attachToSale(), findApplied(), updateAppliedAmount(), Collection, PromotionsRepository, BelongsTo, SalePromotion, PromotionsRepositoryInterface

### Community 24 - "SalesRepository.php"
Cohesion: 0.13
Nodes (10): createCategory(), createProduct(), deleteCategory(), deleteProduct(), findCategoryById(), findProductById(), listCategories(), listProducts() (+2 more)

### Community 30 - "artisan"
Cohesion: 0.14
Nodes (6): InventoryRepository, Collection, BelongsTo, StoreInventory, InventoryResource, InventoryRepositoryInterface

### Community 32 - "AnalyticsRepositoryInterface.php"
Cohesion: 0.15
Nodes (5): self, WebhookRetryItem, InMemoryWebhookRetryQueue, RedisWebhookRetryQueue, WebhookRetryQueueInterface

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
Cohesion: 0.15
Nodes (8): GetAdminAnalyticsAction, AnalyticsRepositoryInterface, AdminAnalyticsSnapshot, CustomerSpendRow, adminSnapshot(), listCampaignCustomers(), Collection, AnalyticsResource

### Community 39 - "PromotionsRepositoryInterface.php"
Cohesion: 0.15
Nodes (10): AddSaleLineAction, CatalogRepositoryInterface, SalesRepositoryInterface, CreateSaleAction, SalesRepositoryInterface, SalesRepositoryInterface, ShowSaleAction, AddSaleLineController (+2 more)

### Community 40 - "RefundsReturnsRepositoryInterface.php"
Cohesion: 0.26
Nodes (4): LoginUserAction, Session, MfaPendingSession, Session

### Community 41 - "SalesRepositoryInterface.php"
Cohesion: 0.23
Nodes (5): ConfirmManagerMfaSetupController, JsonResponse, JsonResponse, VerifyManagerMfaChallengeController, MfaCodeRequest

### Community 57 - "CreateSaleAction"
Cohesion: 0.20
Nodes (5): ListProductsAction, CatalogRepositoryInterface, ListProductsController, JsonResponse, ListProductsRequest

### Community 61 - "Sale"
Cohesion: 0.06
Nodes (21): IdempotencyGuard, IdempotencyRecordRepositoryInterface, JsonResponse, Request, claimProcessing(), delete(), deleteCreatedBefore(), findByScopeAndKey() (+13 more)

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
Cohesion: 0.15
Nodes (4): AdminSaleFilters, Collection, SalesRepository, SalesRepositoryInterface

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
Cohesion: 0.05
Nodes (8): CatalogRepository, Collection, Product, CatalogRepositoryInterface, AdminAuditLogTest, OperationalCatalogTest, ProductCrudTest, ApplyPromotionToSaleTest

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
Cohesion: 0.19
Nodes (5): CustomersRepositoryInterface, UpdateCustomerAction, CustomerPayloadNormalizer, JsonResponse, UpdateCustomerController

### Community 87 - "SaleStatus.php"
Cohesion: 0.10
Nodes (16): Request, StoreRepositoryInterface, SelectStoreContextAction, OperationalPosController, JsonResponse, Request, EnsureStoreContext, Closure (+8 more)

### Community 88 - "CloseCashShiftTest"
Cohesion: 0.13
Nodes (5): SaleCartGuard, BelongsTo, HasMany, Sale, HasOne

### Community 92 - "ListCategoriesAction"
Cohesion: 0.31
Nodes (5): ListCategoriesAction, CatalogRepositoryInterface, Collection, ListCategoriesController, JsonResponse

### Community 93 - "ResumeSaleAction"
Cohesion: 0.22
Nodes (9): dev, serve, test, Composer\\Config::disableProcessTimeout, npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74,#86efac\" \"php artisan serve\" \"php artisan queue:listen redis --tries=3 --timeout=0\" \"php artisan reverb:start\" \"php artisan pail --timeout=0\" \"npm run dev\" --names=server,queue,reverb,logs,vite --kill-others, @php artisan config:clear --ansi @no_additional_args, @php artisan serve --host=127.0.0.1 --port=8000, @php artisan test (+1 more)

### Community 94 - "DeleteCategoryAction"
Cohesion: 0.60
Nodes (3): JsonResponse, Request, ShowSaleController

### Community 95 - "DeleteProductAction"
Cohesion: 0.05
Nodes (20): CreateUserAction, UsersRepositoryInterface, ListUsersAction, UsersRepositoryInterface, UsersRepositoryInterface, ShowUserAction, UsersRepositoryInterface, UpdateUserAction (+12 more)

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
Cohesion: 0.52
Nodes (4): ListRefundsForSaleAction, Collection, RefundsReturnsRepositoryInterface, SalesRepositoryInterface

### Community 102 - "UpdateSaleLineAction"
Cohesion: 0.22
Nodes (6): create(), findByCpf(), findById(), list(), Collection, update()

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
Cohesion: 0.31
Nodes (5): FindCustomerByCpfAction, CustomersRepositoryInterface, FindOperationalCustomerController, JsonResponse, Request

### Community 109 - "ShowCustomerAction"
Cohesion: 0.33
Nodes (4): CustomersRepositoryInterface, ShowCustomerAction, JsonResponse, ShowCustomerController

### Community 110 - "OperationalRouteAccessTest"
Cohesion: 0.07
Nodes (12): BaseTestCase, ListAdminNotificationsTest, AdminRouteAccessTest, ExampleTest, CorrelationIdMiddlewareTest, PurgeExpiredIdempotencyRecordsTest, ListStoresTest, static (+4 more)

### Community 111 - "CustomerErrorCodeTest"
Cohesion: 0.32
Nodes (3): LoginController, JsonResponse, LoginRequest

### Community 112 - "ResumeSaleController.php"
Cohesion: 0.24
Nodes (4): UpdateCustomerRequest, CompleteSaleRequest, FormRequest, Validator

### Community 113 - "ListStoresTest"
Cohesion: 0.50
Nodes (3): Session, TotpAuthenticatorInterface, VerifyManagerMfaChallengeAction

### Community 117 - "ListPromotionsAction"
Cohesion: 0.20
Nodes (5): ListPromotionsAction, PromotionsRepositoryInterface, ListPromotionsController, JsonResponse, ListPromotionsRequest

### Community 118 - "PaymentMethod"
Cohesion: 0.28
Nodes (3): GetAdminAnalyticsController, JsonResponse, GetAdminAnalyticsRequest

### Community 119 - "SaleResource"
Cohesion: 0.14
Nodes (4): updateCategory(), Category, HasMany, CategoryCrudTest

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
Cohesion: 0.11
Nodes (8): Model, PiiEncryptedDate, Model, PiiEncryptedString, PiiCrypto, CarbonInterface, CastsAttributes, Encrypter

### Community 130 - "PromotionDiscountCalculator"
Cohesion: 0.09
Nodes (6): Collection, PromotionDiscountCalculator, Money, PromotionResource, SaleResource, MoneyTest

### Community 131 - "PromotionErrorCodeTest"
Cohesion: 0.05
Nodes (6): Store, StorePolicy, OperationalCustomerTest, HoldSaleTest, SalesCartTest, DemoUserSeedersTest

### Community 132 - "RemovePromotionFromSaleController.php"
Cohesion: 0.42
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
Cohesion: 0.13
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
Cohesion: 0.22
Nodes (6): findLineByTransactionReference(), markLineStatus(), markWebhookProcessed(), DateTimeInterface, PaymentLineStatus, recordWebhookEvent()

### Community 145 - "AssertManagerStoreAccess"
Cohesion: 0.23
Nodes (6): GetAdminShiftReportAction, CashShiftRepositoryInterface, SalesRepositoryInterface, ShowAdminSaleAction, AssertManagerStoreAccess, StoreRepositoryInterface

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
Cohesion: 0.19
Nodes (6): ListStoreInventoryAction, Collection, InventoryRepositoryInterface, ListStoreInventoryController, JsonResponse, ListStoreInventoryRequest

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
Cohesion: 0.33
Nodes (5): LogoutUserAction, Request, LogoutController, JsonResponse, Request

### Community 174 - "ProbeQueuedJob"
Cohesion: 0.53
Nodes (4): EnsureUserHasRole, Closure, Request, Response

### Community 176 - "ShowCurrentUserController.php"
Cohesion: 0.27
Nodes (6): DeleteProductController, JsonResponse, Controller, ListRefundsForSaleController, JsonResponse, Request

### Community 177 - "EnsureStoreContext.php"
Cohesion: 0.28
Nodes (4): HmacPaymentWebhookSignatureVerifier, AppServiceProvider, PaymentWebhookSignatureVerifierInterface, ServiceProvider

### Community 187 - "StoreInventoryFactory"
Cohesion: 0.05
Nodes (13): assignedStoreIds(), findById(), listAccessibleForUser(), userCanAccessStore(), BelongsToMany, User, Authenticatable, Notifiable (+5 more)

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
Cohesion: 0.32
Nodes (3): HoldSaleController, JsonResponse, HoldSaleRequest

### Community 199 - "CreatePromotionAction"
Cohesion: 0.27
Nodes (6): AuditLogRepositoryInterface, PromotionsRepositoryInterface, UpdatePromotionAction, JsonResponse, UpdatePromotionController, UpdatePromotionRequest

### Community 207 - "ResumeSaleController.php"
Cohesion: 0.33
Nodes (4): DeleteCategoryAction, CatalogRepositoryInterface, DeleteCategoryController, JsonResponse

### Community 208 - "ApplyPromotionToSaleAction"
Cohesion: 0.53
Nodes (3): ListAdminShiftsAction, CashShiftRepositoryInterface, Collection

### Community 210 - "HasFactory"
Cohesion: 0.53
Nodes (3): ListAdminSalesAction, Collection, SalesRepositoryInterface

### Community 211 - "EnsureUserHasRole.php"
Cohesion: 0.14
Nodes (4): CustomerStoreStat, BelongsTo, DemoCustomerSeeder, DemoCatalogSeedersTest

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

### Community 220 - "ListAdminSalesAction"
Cohesion: 0.60
Nodes (3): ListAdminNotificationsController, JsonResponse, Request

### Community 221 - "RemovePromotionFromSaleAction"
Cohesion: 0.31
Nodes (6): PromotionsRepositoryInterface, SalesRepositoryInterface, RemovePromotionFromSaleAction, JsonResponse, Request, RemovePromotionFromSaleController

### Community 222 - "RemoveSaleLineController.php"
Cohesion: 0.60
Nodes (3): JsonResponse, Request, RemoveSaleLineController

### Community 223 - "ActsWithOperationalSession.php"
Cohesion: 0.60
Nodes (3): JsonResponse, Request, ResumeSaleController

### Community 230 - "actingAsManagerForStore"
Cohesion: 0.67
Nodes (3): actingAsManagerForStore(), attachManagerToStore(), static

### Community 235 - "ActsWithOperationalSession.php"
Cohesion: 0.70
Nodes (4): actingAsOperatorAtStore(), actingAsOperatorWithOpenShift(), static, withOpenShift()

## Knowledge Gaps
- **79 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+74 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **47 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `StoreInventoryFactory` to `PromotionErrorCodeTest`, `AuthenticationDomainException`, `UserFactory`, `HasFactory`, `post-create-project-cmd`, `AppServiceProvider.php`, `DatabaseSeeder.php`, `CashShiftRepository.php`, `AssertManagerStoreAccess`, `FormRequest`, `AuthenticationDomainException`, `RefundsReturnsRepository.php`, `SaleCompletedNotification`, `artisan`, `SaleCompletedNotificationTest`, `UsersRepository`, `CatalogRepositoryInterface.php`, `PaymentsRepositoryInterface.php`, `PromotionsRepositoryInterface.php`, `RefundsReturnsRepositoryInterface.php`, `AppServiceProvider.php`, `DispatchSaleSideEffectsTest`, `AdminStoreAccessIdorTest`, `CustomersRepositoryInterface.php`, `AdjustStoreInventoryAction`, `Sale`, `StoreRepository`, `CreateProductAction`, `Category`, `SelectStoreContextTest`, `AuthSecurityBaselineTest`, `CreatePromotionAction`, `OperationalStoreContextTest`, `UpdateProductAction`, `HoldSaleAction`, `RemoveSaleLineController.php`, `ApplyPromotionToSaleAction`, `ShowProductAction`, `HasFactory`, `EnsureUserHasRole.php`, `LoginTest`, `OperationalRouteAccessTest`, `StoreRepository`, `SaleStatus.php`, `CloseCashShiftTest`, `SelectStoreContextTest`, `DeleteProductAction`, `OperationalRouteAccessTest`, `CatalogResource`, `actingAsManagerForStore`, `CreateCustomerAction`, `AdminAnalyticsTest`, `AdminInventoryTest`, `.completedSaleFixture`, `ActsWithOperationalSession.php`, `OperationalRouteAccessTest`, `ListStoresTest`, `SaleResource`, `Promotion.php`, `ListStoresTest`?**
  _High betweenness centrality (0.203) - this node is a cross-community bridge._
- **Why does `Controller` connect `ShowCurrentUserController.php` to `User`, `DomainScaffolder`, `AttachCustomerToSaleAction`, `CashShiftRepository.php`, `CatalogRepository.php`, `RemoveSaleLineAction`, `AuthenticationDomainException`, `SaleCompletedNotification`, `SaleCompletedNotificationTest`, `CatalogRepositoryInterface.php`, `CustomersRepositoryInterface.php`, `InventoryRepositoryInterface.php`, `PromotionsRepositoryInterface.php`, `SalesRepositoryInterface.php`, `AppServiceProvider.php`, `AdminStoreAccessIdorTest`, `CreateSaleAction`, `Sale`, `SelectStoreContextTest`, `SaleErrorCodeTest`, `MoneyTest`, `CreatePromotionAction`, `UpdateProductAction`, `CatalogRepositoryInterface.php`, `SaleResource`, `Model`, `ShowCategoryAction`, `ResumeSaleController.php`, `ShowProductAction`, `AdminRouteAccessTest`, `CardInstrumentFormatGuardTest`, `post-autoload-dump`, `UpdateCustomerAction`, `SaleStatus.php`, `OperationalRouteAccessTest`, `AdminDashboardController.php`, `ListCategoriesAction`, `ListAdminSalesAction`, `RemovePromotionFromSaleAction`, `DeleteProductAction`, `ShowCategoryAction`, `CurrentCashShiftController.php`, `ShowProductAction`, `RemoveSaleLineController.php`, `ActsWithOperationalSession.php`, `DeleteCategoryAction`, `ListCustomersAction`, `CustomerResource`, `FindCustomerByCpfAction`, `ShowCustomerAction`, `CustomerErrorCodeTest`, `ListPromotionsAction`, `PaymentMethod`, `ApplyPromotionToSaleAction`?**
  _High betweenness centrality (0.165) - this node is a cross-community bridge._
- **Why does `TestCase` connect `OperationalRouteAccessTest` to `PromotionDiscountCalculator`, `PromotionErrorCodeTest`, `StoreContext`, `autoload-dev`, `HasFactory`, `AuthErrorCodeTest`, `StoreErrorCodeTest`, `RefundErrorCodeTest`, `FormRequest`, `BusOrchestratorTest`, `CashShiftRepositoryInterface.php`, `InventoryErrorCodeTest`, `PromotionErrorCodeTest`, `CompleteSaleTest`, `AdminStoreAccessIdorTest`, `CustomersRepositoryInterface.php`, `AdjustStoreInventoryAction`, `RefundsReturnsRepositoryInterface.php`, `StoreInventoryFactory`, `Category`, `AdminAuditLogTest`, `AuthSecurityBaselineTest`, `CatalogErrorCodeTest`, `InventoryErrorCodeTest`, `StoreErrorCodeTest`, `OperationalStoreContextTest`, `HoldSaleAction`, `ShowAdminShiftReportController.php`, `RemoveSaleLineController.php`, `CatalogResource`, `StoreRepository`, `EnsureUserHasRole.php`, `2026_07_15_300001_create_sales_table.php`, `SelectStoreContextTest`, `AdminAnalyticsTest`, `AdminInventoryTest`, `.completedSaleFixture`, `ShowSaleAction`, `SaleResource`, `SalePaymentValidator`?**
  _High betweenness centrality (0.126) - this node is a cross-community bridge._
- **Are the 212 inferred relationships involving `User` (e.g. with `.execute()` and `.handle()`) actually correct?**
  _`User` has 212 INFERRED edges - model-reasoned connections that need verification._
- **Are the 137 inferred relationships involving `Store` (e.g. with `.topCustomersBySpend()` and `.definition()`) actually correct?**
  _`Store` has 137 INFERRED edges - model-reasoned connections that need verification._
- **Are the 76 inferred relationships involving `Product` (e.g. with `.definition()` and `.run()`) actually correct?**
  _`Product` has 76 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _79 weakly-connected nodes found - possible documentation gaps or missing edges._