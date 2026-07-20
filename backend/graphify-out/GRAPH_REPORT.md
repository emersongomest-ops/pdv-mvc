# Graph Report - projects\pdv\backend  (2026-07-20)

## Corpus Check
- 481 files · ~66,199 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2585 nodes · 5094 edges · 230 communities (189 shown, 41 thin omitted)
- Extraction: 86% EXTRACTED · 14% INFERRED · 0% AMBIGUOUS · INFERRED: 705 edges (avg confidence: 0.8)
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
- CardInstrumentFormatGuardTest
- post-autoload-dump
- SelectStoreContextTest
- AdminDashboardController.php
- RemovePromotionFromSaleAction
- ActsWithOperationalSession.php
- ProbeQueuedJob
- WebhookRetryQueueInterface.php
- SaleAnalyticsRecorderInterface
- DatabaseSeeder
- AdminAnalyticsTest
- AdminInventoryTest
- ActsWithOperationalSession.php
- CatalogResource

## God Nodes (most connected - your core abstractions)
1. `User` - 319 edges
2. `Store` - 164 edges
3. `TestCase` - 151 edges
4. `Controller` - 133 edges
5. `Product` - 105 edges
6. `Sale` - 105 edges
7. `Customer` - 64 edges
8. `Money` - 50 edges
9. `Promotion` - 50 edges
10. `AssertManagerStoreAccess` - 43 edges

## Surprising Connections (you probably didn't know these)
- `findCategoryById()` --references--> `Category`  [EXTRACTED]
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

## Communities (230 total, 41 thin omitted)

### Community 0 - "User"
Cohesion: 0.16
Nodes (7): CreateSaleAction, SalesRepositoryInterface, SalesRepositoryInterface, ShowSaleAction, JsonResponse, UpdateSaleLineController, UpdateSaleLineRequest

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
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 6 - "AuthenticationDomainException"
Cohesion: 0.11
Nodes (15): addLine(), attachCustomer(), complete(), createInProgress(), findById(), hold(), listForAdmin(), listHeldForShift() (+7 more)

### Community 7 - "UserFactory"
Cohesion: 0.06
Nodes (16): CashShiftFactory, static, CategoryFactory, static, CustomerFactory, static, ProductFactory, static (+8 more)

### Community 8 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 9 - "DomainScaffolder"
Cohesion: 0.15
Nodes (6): AuditLogEntry, ListAdminAuditLogsController, JsonResponse, ListAdminAuditLogsRequest, AuditLogResource, AuditAction

### Community 10 - "AppServiceProvider.php"
Cohesion: 0.23
Nodes (9): NotifyManagersOfSaleCompleted, SaleCompleted, AbstractQueuedJob, Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels (+1 more)

### Community 11 - "ListAccessibleStoresAction"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "DatabaseSeeder.php"
Cohesion: 0.21
Nodes (6): AuditLogRepositoryInterface, CatalogRepositoryInterface, UpdateProductAction, JsonResponse, UpdateProductController, UpdateProductRequest

### Community 15 - "AnalyticsRepositoryInterface"
Cohesion: 0.21
Nodes (3): BelongsToMany, HasMany, DemoPromotionSeeder

### Community 16 - "CashShiftRepository.php"
Cohesion: 0.05
Nodes (26): CloseCashShiftAction, CashShiftRepositoryInterface, OpenCashShiftAction, CashShiftRepositoryInterface, ShiftClosingReport, buildClosingReport(), close(), createOpen() (+18 more)

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
Cohesion: 0.11
Nodes (8): attachToSale(), findApplied(), updateAppliedAmount(), Collection, PromotionsRepository, BelongsTo, SalePromotion, PromotionsRepositoryInterface

### Community 23 - "RefundsReturnsRepository.php"
Cohesion: 0.13
Nodes (3): CatalogRepository, Collection, CatalogRepositoryInterface

### Community 24 - "SalesRepository.php"
Cohesion: 0.13
Nodes (10): createProduct(), deleteCategory(), deleteProduct(), findCategoryById(), findProductById(), listCategories(), listProducts(), Collection (+2 more)

### Community 30 - "artisan"
Cohesion: 0.18
Nodes (8): SalesRepositoryInterface, UpdateSaleLineAction, findLineById(), findLineByProduct(), removeLine(), updateLineQuantity(), BelongsTo, SaleLine

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
Cohesion: 0.28
Nodes (3): GetAdminAnalyticsController, JsonResponse, GetAdminAnalyticsRequest

### Community 39 - "PromotionsRepositoryInterface.php"
Cohesion: 0.22
Nodes (6): AddSaleLineAction, CatalogRepositoryInterface, SalesRepositoryInterface, AddSaleLineController, JsonResponse, AddSaleLineRequest

### Community 40 - "RefundsReturnsRepositoryInterface.php"
Cohesion: 0.29
Nodes (4): LoginUserAction, Session, MfaPendingSession, Session

### Community 41 - "SalesRepositoryInterface.php"
Cohesion: 0.32
Nodes (3): JsonResponse, VerifyManagerMfaChallengeController, MfaCodeRequest

### Community 57 - "CreateSaleAction"
Cohesion: 0.20
Nodes (5): ListProductsAction, CatalogRepositoryInterface, ListProductsController, JsonResponse, ListProductsRequest

### Community 61 - "Sale"
Cohesion: 0.07
Nodes (19): IdempotencyGuard, IdempotencyRecordRepositoryInterface, JsonResponse, Request, claimProcessing(), delete(), deleteCreatedBefore(), findByScopeAndKey() (+11 more)

### Community 62 - "Category"
Cohesion: 0.13
Nodes (3): LoginTest, SessionGateTest, TotpAuthenticatorInterface

### Community 63 - "CreateProductAction"
Cohesion: 0.06
Nodes (20): AnalyticsRepositoryInterface, AdminAnalyticsSnapshot, CustomerSpendRow, adminSnapshot(), listCampaignCustomers(), Collection, AnalyticsRepository, Collection (+12 more)

### Community 66 - "SaleErrorCodeTest"
Cohesion: 0.46
Nodes (3): ApplyPromotionToSaleAction, PromotionsRepositoryInterface, SalesRepositoryInterface

### Community 73 - "UpdateProductAction"
Cohesion: 0.13
Nodes (3): AuditLog, BelongsTo, AdminAuditLogTest

### Community 74 - "CatalogRepositoryInterface.php"
Cohesion: 0.22
Nodes (5): CatalogRepositoryInterface, UpdateCategoryAction, JsonResponse, UpdateCategoryController, UpdateCategoryRequest

### Community 75 - "SaleResource"
Cohesion: 0.11
Nodes (12): ListOperationalProductsAction, CatalogRepositoryInterface, InventoryRepositoryInterface, ListHeldSalesAction, Collection, SalesRepositoryInterface, ListOperationalProductsController, JsonResponse (+4 more)

### Community 76 - "SalesRepository"
Cohesion: 0.14
Nodes (4): AdminSaleFilters, Collection, SalesRepository, SalesRepositoryInterface

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
Nodes (4): Product, ProductCrudTest, ApplyPromotionToSaleTest, CompleteSaleTest

### Community 83 - "AdminRouteAccessTest"
Cohesion: 0.33
Nodes (4): PromotionsRepositoryInterface, ShowPromotionAction, JsonResponse, ShowPromotionController

### Community 84 - "LoginTest"
Cohesion: 0.13
Nodes (10): PromotionAuditSnapshot, create(), findByCode(), findById(), list(), listAppliedForSale(), Collection, update() (+2 more)

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
Cohesion: 0.22
Nodes (3): HoldSaleAction, SalesRepositoryInterface, SaleCartGuard

### Community 92 - "ListCategoriesAction"
Cohesion: 0.20
Nodes (6): ListCategoriesAction, CatalogRepositoryInterface, Collection, ListCategoriesController, JsonResponse, CatalogResource

### Community 93 - "ResumeSaleAction"
Cohesion: 0.13
Nodes (17): scripts, dev, post-autoload-dump, post-update-cmd, pre-package-uninstall, serve, test, Composer\\Config::disableProcessTimeout (+9 more)

### Community 94 - "DeleteCategoryAction"
Cohesion: 0.12
Nodes (15): ListAdminShiftsController, JsonResponse, JsonResponse, Request, ShowAdminShiftReportController, Controller, ListAdminNotificationsController, JsonResponse (+7 more)

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
Cohesion: 0.52
Nodes (4): ListRefundsForSaleAction, Collection, RefundsReturnsRepositoryInterface, SalesRepositoryInterface

### Community 102 - "UpdateSaleLineAction"
Cohesion: 0.25
Nodes (5): ListAdminAuditLogsAction, AuditLogRepositoryInterface, AdminAuditFilters, append(), listForAdmin()

### Community 103 - "CreateCustomerAction"
Cohesion: 0.31
Nodes (5): ConfirmManagerMfaSetupAction, Session, TotpAuthenticatorInterface, ConfirmManagerMfaSetupController, JsonResponse

### Community 104 - "ListCustomersAction"
Cohesion: 0.20
Nodes (5): ListCustomersAction, CustomersRepositoryInterface, ListCustomersController, JsonResponse, ListCustomersRequest

### Community 105 - "CustomerResource"
Cohesion: 0.39
Nodes (4): CreateCustomerAction, CustomersRepositoryInterface, CreateCustomerController, JsonResponse

### Community 106 - "CreateSaleAction"
Cohesion: 0.60
Nodes (3): ListRefundsForSaleController, JsonResponse, Request

### Community 107 - "ShowSaleAction"
Cohesion: 0.25
Nodes (5): DispatchSaleSideEffects, BusOrchestrator, Batch, PendingBatch, PendingChain

### Community 108 - "FindCustomerByCpfAction"
Cohesion: 0.36
Nodes (5): FindCustomerByCpfAction, CustomersRepositoryInterface, FindOperationalCustomerController, JsonResponse, Request

### Community 109 - "ShowCustomerAction"
Cohesion: 0.33
Nodes (4): CustomersRepositoryInterface, ShowCustomerAction, JsonResponse, ShowCustomerController

### Community 110 - "OperationalRouteAccessTest"
Cohesion: 0.10
Nodes (15): ActsWithOperationalSession, InteractsWithStatefulApi, OperationalRouteAccessTest, CloseCashShiftReportTest, CloseCashShiftTest, OpenCashShiftTest, OperationalShiftGateTest, OperationalCatalogTest (+7 more)

### Community 111 - "CustomerErrorCodeTest"
Cohesion: 0.14
Nodes (6): ListAdminShiftsRequest, StoreCustomerRequest, LoginController, JsonResponse, LoginRequest, FormRequest

### Community 112 - "ResumeSaleController.php"
Cohesion: 0.27
Nodes (4): CompleteSaleController, JsonResponse, CompleteSaleRequest, Validator

### Community 113 - "ListStoresTest"
Cohesion: 0.27
Nodes (4): Session, TotpAuthenticatorInterface, VerifyManagerMfaChallengeAction, MfaRecoveryCodeVault

### Community 117 - "ListPromotionsAction"
Cohesion: 0.20
Nodes (5): ListPromotionsAction, PromotionsRepositoryInterface, ListPromotionsController, JsonResponse, ListPromotionsRequest

### Community 118 - "PaymentMethod"
Cohesion: 0.18
Nodes (5): BelongsTo, BelongsToMany, BelongsToMany, HasFactory, Notifiable

### Community 119 - "SaleResource"
Cohesion: 0.25
Nodes (3): createCategory(), Category, HasMany

### Community 120 - "Promotion.php"
Cohesion: 0.06
Nodes (23): NormalizedPaymentWebhook, findLineByTransactionReference(), markLineStatus(), markWebhookProcessed(), DateTimeInterface, PaymentLineStatus, recordWebhookEvent(), normalize() (+15 more)

### Community 121 - "ApplyPromotionToSaleAction"
Cohesion: 0.28
Nodes (4): HmacPaymentWebhookSignatureVerifier, AppServiceProvider, PaymentWebhookSignatureVerifierInterface, ServiceProvider

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
Nodes (11): assignedStoreIds(), findById(), listAccessibleForUser(), userCanAccessStore(), Store, User, StorePolicy, Authenticatable (+3 more)

### Community 132 - "RemovePromotionFromSaleController.php"
Cohesion: 0.42
Nodes (7): CompleteSaleAction, FiscalReceiptGeneratorInterface, InventoryRepositoryInterface, PaymentGatewayInterface, PaymentsRepositoryInterface, PendingPaymentOutboxInterface, SalesRepositoryInterface

### Community 133 - "autoload-dev"
Cohesion: 0.32
Nodes (3): ApplyPromotionToSaleController, JsonResponse, ApplyPromotionToSaleRequest

### Community 134 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 135 - "require"
Cohesion: 0.20
Nodes (10): require, bacon/bacon-qr-code, laravel/framework, laravel/horizon, laravel/pulse, laravel/reverb, laravel/sanctum, laravel/tinker (+2 more)

### Community 136 - "HasFactory"
Cohesion: 0.06
Nodes (11): create(), findByCpf(), findById(), list(), Collection, update(), Customer, HasMany (+3 more)

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
Nodes (5): UsersRepositoryInterface, UpdateUserAction, JsonResponse, UpdateUserController, UpdateUserRequest

### Community 145 - "AssertManagerStoreAccess"
Cohesion: 0.25
Nodes (6): GetAdminAnalyticsAction, AnalyticsRepositoryInterface, GetAdminShiftReportAction, CashShiftRepositoryInterface, AssertManagerStoreAccess, StoreRepositoryInterface

### Community 147 - "DeleteCategoryAction"
Cohesion: 0.20
Nodes (9): pulse.cache, pulse.exceptions, pulse.queues, pulse.servers, pulse.slow-jobs, pulse.slow-outgoing-requests, pulse.slow-queries, pulse.slow-requests (+1 more)

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
Cohesion: 0.28
Nodes (3): ListStoreInventoryController, JsonResponse, ListStoreInventoryRequest

### Community 162 - "UsersRepository"
Cohesion: 0.60
Nodes (3): AuditLogRepositoryInterface, CashShiftRepositoryInterface, ReopenCashShiftAction

### Community 169 - "AppServiceProvider.php"
Cohesion: 0.32
Nodes (3): AdjustStoreInventoryController, JsonResponse, AdjustStoreInventoryRequest

### Community 172 - "AdminStoreAccessIdorTest"
Cohesion: 0.29
Nodes (5): LogoutUserAction, Request, LogoutController, JsonResponse, Request

### Community 174 - "ProbeQueuedJob"
Cohesion: 0.53
Nodes (4): EnsureUserHasRole, Closure, Request, Response

### Community 176 - "ShowCurrentUserController.php"
Cohesion: 0.57
Nodes (4): AdjustStoreInventoryAction, AuditLogRepositoryInterface, CatalogRepositoryInterface, InventoryRepositoryInterface

### Community 178 - "StoreDomainException"
Cohesion: 0.33
Nodes (4): DeleteProductAction, CatalogRepositoryInterface, DeleteProductController, JsonResponse

### Community 183 - "AdminStoreAccessIdorTest"
Cohesion: 0.53
Nodes (3): ListCampaignCustomersAction, AnalyticsRepositoryInterface, Collection

### Community 184 - "CustomersRepositoryInterface.php"
Cohesion: 0.23
Nodes (5): UsersRepositoryInterface, ShowUserAction, JsonResponse, ShowUserController, UserResource

### Community 185 - "AdjustStoreInventoryAction"
Cohesion: 0.07
Nodes (11): BaseTestCase, ExampleTest, DemoUserSeedersTest, ListStoresTest, static, TestResponse, TestCase, CreateCustomerActionTest (+3 more)

### Community 191 - "AdminAuditLogTest"
Cohesion: 0.06
Nodes (14): adjustQuantity(), findForStoreProduct(), listForStore(), mapForStoreProducts(), Collection, InventoryRepository, Collection, BelongsTo (+6 more)

### Community 192 - "AuthSecurityBaselineTest"
Cohesion: 0.53
Nodes (3): ListStoreInventoryAction, Collection, InventoryRepositoryInterface

### Community 193 - "SelectStoreContextTest"
Cohesion: 0.36
Nodes (4): ShowCurrentUserAction, JsonResponse, Request, ShowCurrentUserController

### Community 194 - "SelectStoreContextTest"
Cohesion: 0.09
Nodes (21): CashShiftDomainException, ErrorCode, CatalogDomainException, ErrorCode, CustomerDomainException, ErrorCode, InventoryDomainException, ErrorCode (+13 more)

### Community 197 - "MoneyTest"
Cohesion: 0.32
Nodes (3): HoldSaleController, JsonResponse, HoldSaleRequest

### Community 199 - "CreatePromotionAction"
Cohesion: 0.24
Nodes (6): AuditLogRepositoryInterface, PromotionsRepositoryInterface, UpdatePromotionAction, JsonResponse, UpdatePromotionController, UpdatePromotionRequest

### Community 200 - "OperationalStoreContextTest"
Cohesion: 0.53
Nodes (3): ListAdminSalesAction, Collection, SalesRepositoryInterface

### Community 207 - "ResumeSaleController.php"
Cohesion: 0.33
Nodes (4): DeleteCategoryAction, CatalogRepositoryInterface, DeleteCategoryController, JsonResponse

### Community 208 - "ApplyPromotionToSaleAction"
Cohesion: 0.09
Nodes (8): ActsWithAdminStoreAccess, AdminAnalyticsTest, AdminDashboardMetricsTest, AdminShiftReportTest, ListAdminSalesTest, ShowAdminSaleTest, ResetManagerMfaTest, RefundThrottleAndSalesIdorTest

### Community 209 - "CatalogResource"
Cohesion: 0.67
Nodes (3): actingAsManagerForStore(), attachManagerToStore(), static

### Community 212 - "CardInstrumentFormatGuardTest"
Cohesion: 0.60
Nodes (3): JsonResponse, Request, ShowAdminSaleController

### Community 213 - "post-autoload-dump"
Cohesion: 0.60
Nodes (3): JsonResponse, Request, ReopenCashShiftController

### Community 219 - "AdminDashboardController.php"
Cohesion: 0.21
Nodes (5): GetAdminDashboardMetricsAction, AdminDashboardMetrics, AdminDashboardController, JsonResponse, Request

### Community 221 - "RemovePromotionFromSaleAction"
Cohesion: 0.31
Nodes (6): PromotionsRepositoryInterface, SalesRepositoryInterface, RemovePromotionFromSaleAction, JsonResponse, Request, RemovePromotionFromSaleController

### Community 223 - "ActsWithOperationalSession.php"
Cohesion: 0.60
Nodes (3): JsonResponse, Request, ResumeSaleController

### Community 227 - "WebhookRetryQueueInterface.php"
Cohesion: 0.07
Nodes (8): RefreshDatabase, ListAdminNotificationsTest, AdminRouteAccessTest, AdminCatalogAccessTest, CategoryCrudTest, CorrelationIdMiddlewareTest, PasswordUncompromisedTest, PurgeExpiredIdempotencyRecordsTest

### Community 232 - "AdminAnalyticsTest"
Cohesion: 0.05
Nodes (28): ConsumePaymentWebhookAction, PaymentsRepositoryInterface, PaymentWebhookPayloadNormalizerInterface, PaymentWebhookSignatureVerifierInterface, PendingPaymentOutboxInterface, ErrorCode, PaymentGatewayInterface, PaymentsRepositoryInterface (+20 more)

### Community 235 - "ActsWithOperationalSession.php"
Cohesion: 0.70
Nodes (4): actingAsOperatorAtStore(), actingAsOperatorWithOpenShift(), static, withOpenShift()

## Knowledge Gaps
- **79 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+74 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **41 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `PromotionErrorCodeTest` to `User`, `ApiErrorResponse.php`, `AuthenticationDomainException`, `HasFactory`, `post-create-project-cmd`, `AppServiceProvider.php`, `DatabaseSeeder.php`, `CashShiftRepository.php`, `AssertManagerStoreAccess`, `CatalogRepository.php`, `HoldSaleAction`, `FormRequest`, `AuthenticationDomainException`, `UsersRepository`, `CatalogRepositoryInterface.php`, `RefundsReturnsRepositoryInterface.php`, `ShowCurrentUserController.php`, `CustomersRepositoryInterface.php`, `RefundsReturnsRepositoryInterface.php`, `StoreInventoryFactory`, `Sale`, `StoreRepository`, `CreateProductAction`, `AuthSecurityBaselineTest`, `SelectStoreContextTest`, `Category`, `AdminAuditLogTest`, `ListAdminShiftsAction`, `CreatePromotionAction`, `OperationalStoreContextTest`, `UpdateProductAction`, `ListAdminNotificationsController.php`, `ListCategoriesAction`, `ShowProductAction`, `ApplyPromotionToSaleAction`, `StoreRepository`, `HasFactory`, `LoginTest`, `OperationalRouteAccessTest`, `CatalogResource`, `SaleStatus.php`, `SelectStoreContextTest`, `AdminDashboardController.php`, `CatalogResource`, `WebhookRetryQueueInterface.php`, `UpdateSaleLineAction`, `CreateCustomerAction`, `ActsWithOperationalSession.php`, `OperationalRouteAccessTest`, `ListStoresTest`, `PaymentMethod`, `SaleResource`, `Promotion.php`, `ListStoresTest`, `Sale.php`?**
  _High betweenness centrality (0.215) - this node is a cross-community bridge._
- **Why does `Controller` connect `DeleteCategoryAction` to `User`, `autoload-dev`, `DomainScaffolder`, `DatabaseSeeder.php`, `AttachCustomerToSaleAction`, `CashShiftRepository.php`, `HoldSaleAction`, `AuthenticationDomainException`, `SaleCompletedNotification`, `CatalogRepositoryInterface.php`, `CustomersRepositoryInterface.php`, `InventoryRepositoryInterface.php`, `PaymentsRepositoryInterface.php`, `PromotionsRepositoryInterface.php`, `SalesRepositoryInterface.php`, `AppServiceProvider.php`, `AdminStoreAccessIdorTest`, `StoreDomainException`, `CustomersRepositoryInterface.php`, `CreateSaleAction`, `Sale`, `SelectStoreContextTest`, `MoneyTest`, `CreatePromotionAction`, `CatalogRepositoryInterface.php`, `SaleResource`, `Model`, `ListCategoriesAction`, `ShowCategoryAction`, `ResumeSaleController.php`, `ShowProductAction`, `AdminRouteAccessTest`, `CardInstrumentFormatGuardTest`, `post-autoload-dump`, `UpdateCustomerAction`, `SaleStatus.php`, `OperationalRouteAccessTest`, `AdminDashboardController.php`, `ListCategoriesAction`, `RemovePromotionFromSaleAction`, `DeleteProductAction`, `ShowCategoryAction`, `CurrentCashShiftController.php`, `ShowProductAction`, `ActsWithOperationalSession.php`, `CreateCustomerAction`, `ListCustomersAction`, `CustomerResource`, `AdminAnalyticsTest`, `CreateSaleAction`, `FindCustomerByCpfAction`, `ShowCustomerAction`, `CustomerErrorCodeTest`, `ResumeSaleController.php`, `ListPromotionsAction`, `Sale.php`?**
  _High betweenness centrality (0.167) - this node is a cross-community bridge._
- **Why does `TestCase` connect `AdjustStoreInventoryAction` to `ApiErrorResponse.php`, `PromotionDiscountCalculator`, `PromotionErrorCodeTest`, `StoreContext`, `HasFactory`, `AuthErrorCodeTest`, `StoreErrorCodeTest`, `RefundErrorCodeTest`, `RemoveSaleLineAction`, `FormRequest`, `BusOrchestratorTest`, `CashShiftRepositoryInterface.php`, `InventoryErrorCodeTest`, `PromotionErrorCodeTest`, `CompleteSaleTest`, `RefundsReturnsRepositoryInterface.php`, `StoreInventoryFactory`, `Category`, `AdminAuditLogTest`, `CatalogErrorCodeTest`, `InventoryErrorCodeTest`, `ListAdminShiftsAction`, `StoreErrorCodeTest`, `UpdateProductAction`, `HoldSaleAction`, `ApplyPromotionToSaleAction`, `StoreRepository`, `HasFactory`, `SelectStoreContextTest`, `WebhookRetryQueueInterface.php`, `AdminInventoryTest`, `ShowSaleAction`, `OperationalRouteAccessTest`, `Promotion.php`, `SalePaymentValidator`?**
  _High betweenness centrality (0.133) - this node is a cross-community bridge._
- **Are the 219 inferred relationships involving `User` (e.g. with `.execute()` and `.handle()`) actually correct?**
  _`User` has 219 INFERRED edges - model-reasoned connections that need verification._
- **Are the 144 inferred relationships involving `Store` (e.g. with `.topCustomersBySpend()` and `.definition()`) actually correct?**
  _`Store` has 144 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _79 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.14285714285714285 - nodes in this community are weakly interconnected._