<?php

use App\Http\Analytics\Controllers\GetAdminAnalyticsController;
use App\Http\Analytics\Controllers\ListCampaignCustomersController;
use App\Http\Audit\Controllers\ListAdminAuditLogsController;
use App\Http\CashShift\Controllers\CloseCashShiftController;
use App\Http\CashShift\Controllers\CurrentCashShiftController;
use App\Http\CashShift\Controllers\ListAdminShiftsController;
use App\Http\CashShift\Controllers\OpenCashShiftController;
use App\Http\CashShift\Controllers\ReopenCashShiftController;
use App\Http\CashShift\Controllers\ShowAdminShiftReportController;
use App\Http\Catalog\Controllers\CreateCategoryController;
use App\Http\Catalog\Controllers\CreateProductController;
use App\Http\Catalog\Controllers\DeleteCategoryController;
use App\Http\Catalog\Controllers\DeleteProductController;
use App\Http\Catalog\Controllers\ListCategoriesController;
use App\Http\Catalog\Controllers\ListOperationalProductsController;
use App\Http\Catalog\Controllers\ListProductsController;
use App\Http\Catalog\Controllers\ShowCategoryController;
use App\Http\Catalog\Controllers\ShowProductController;
use App\Http\Catalog\Controllers\UpdateCategoryController;
use App\Http\Catalog\Controllers\UpdateProductController;
use App\Http\Customers\Controllers\CreateCustomerController;
use App\Http\Customers\Controllers\FindOperationalCustomerController;
use App\Http\Customers\Controllers\ListCustomersController;
use App\Http\Customers\Controllers\ShowCustomerController;
use App\Http\Customers\Controllers\UpdateCustomerController;
use App\Http\IdentityAccess\Controllers\AdminDashboardController;
use App\Http\IdentityAccess\Controllers\BeginManagerMfaSetupController;
use App\Http\IdentityAccess\Controllers\ConfirmManagerMfaSetupController;
use App\Http\IdentityAccess\Controllers\CreateUserController;
use App\Http\IdentityAccess\Controllers\ListAdminNotificationsController;
use App\Http\IdentityAccess\Controllers\ListUsersController;
use App\Http\IdentityAccess\Controllers\LoginController;
use App\Http\IdentityAccess\Controllers\LogoutController;
use App\Http\IdentityAccess\Controllers\OperationalPosController;
use App\Http\IdentityAccess\Controllers\ResetManagerMfaController;
use App\Http\IdentityAccess\Controllers\ShowCurrentUserController;
use App\Http\IdentityAccess\Controllers\ShowUserController;
use App\Http\IdentityAccess\Controllers\UpdateUserController;
use App\Http\IdentityAccess\Controllers\VerifyManagerMfaChallengeController;
use App\Http\Inventory\Controllers\AdjustStoreInventoryController;
use App\Http\Inventory\Controllers\ListStoreInventoryController;
use App\Http\Payments\Controllers\ConsumePaymentWebhookController;
use App\Http\Payments\Controllers\ReconcileAdminPaymentsController;
use App\Http\Payments\Controllers\ReconcileOperationalPaymentsController;
use App\Http\Promotions\Controllers\CreatePromotionController;
use App\Http\Promotions\Controllers\ListPromotionsController;
use App\Http\Promotions\Controllers\ShowPromotionController;
use App\Http\Promotions\Controllers\UpdatePromotionController;
use App\Http\RefundsReturns\Controllers\CreateRefundController;
use App\Http\RefundsReturns\Controllers\ListRefundsForSaleController;
use App\Http\Sales\Controllers\AddSaleLineController;
use App\Http\Sales\Controllers\ApplyPromotionToSaleController;
use App\Http\Sales\Controllers\AttachCustomerToSaleController;
use App\Http\Sales\Controllers\CompleteSaleController;
use App\Http\Sales\Controllers\CreateSaleController;
use App\Http\Sales\Controllers\HoldSaleController;
use App\Http\Sales\Controllers\ListAdminSalesController;
use App\Http\Sales\Controllers\ListHeldSalesController;
use App\Http\Sales\Controllers\RemovePromotionFromSaleController;
use App\Http\Sales\Controllers\RemoveSaleLineController;
use App\Http\Sales\Controllers\ResumeSaleController;
use App\Http\Sales\Controllers\ShowAdminSaleController;
use App\Http\Sales\Controllers\ShowSaleController;
use App\Http\Sales\Controllers\UpdateSaleLineController;
use App\Http\Store\Controllers\ListStoresController;
use App\Http\Store\Controllers\SelectStoreContextController;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', LoginController::class)->middleware('throttle:login');

Route::post('auth/mfa/setup', BeginManagerMfaSetupController::class)->middleware('throttle:mfa');
Route::post('auth/mfa/confirm', ConfirmManagerMfaSetupController::class)->middleware('throttle:mfa');
Route::post('auth/mfa/verify', VerifyManagerMfaChallengeController::class)->middleware('throttle:mfa');

Route::post('webhooks/payments/{provider}', ConsumePaymentWebhookController::class)
    ->middleware('throttle:60,1');

Route::middleware('auth')->group(function (): void {
    Route::get('auth/me', ShowCurrentUserController::class);
    Route::post('auth/logout', LogoutController::class);

    Route::get('stores', ListStoresController::class);
    Route::post('stores/context', SelectStoreContextController::class);

    Route::prefix('admin')
        ->middleware('role:manager')
        ->group(function (): void {
            Route::get('dashboard', AdminDashboardController::class);
            Route::get('notifications', ListAdminNotificationsController::class);
            Route::get('audit-logs', ListAdminAuditLogsController::class);
            Route::get('analytics', GetAdminAnalyticsController::class);
            Route::get('campaigns/customers', ListCampaignCustomersController::class);

            Route::prefix('catalog')->group(function (): void {
                Route::get('categories', ListCategoriesController::class);
                Route::post('categories', CreateCategoryController::class);
                Route::get('categories/{categoryId}', ShowCategoryController::class);
                Route::patch('categories/{categoryId}', UpdateCategoryController::class);
                Route::delete('categories/{categoryId}', DeleteCategoryController::class);

                Route::get('products', ListProductsController::class);
                Route::post('products', CreateProductController::class);
                Route::get('products/{productId}', ShowProductController::class);
                Route::patch('products/{productId}', UpdateProductController::class);
                Route::delete('products/{productId}', DeleteProductController::class);
            });

            Route::prefix('inventory')->group(function (): void {
                Route::get('/', ListStoreInventoryController::class);
                Route::post('adjustments', AdjustStoreInventoryController::class);
            });

            Route::prefix('customers')->group(function (): void {
                Route::get('/', ListCustomersController::class);
                Route::post('/', CreateCustomerController::class);
                Route::get('{customerId}', ShowCustomerController::class);
                Route::patch('{customerId}', UpdateCustomerController::class);
            });

            Route::prefix('promotions')->group(function (): void {
                Route::get('/', ListPromotionsController::class);
                Route::post('/', CreatePromotionController::class);
                Route::get('{promotionId}', ShowPromotionController::class);
                Route::patch('{promotionId}', UpdatePromotionController::class);
            });

            Route::prefix('sales')->group(function (): void {
                Route::get('/', ListAdminSalesController::class);
                Route::get('{saleId}', ShowAdminSaleController::class);
                Route::get('{saleId}/refunds', ListRefundsForSaleController::class);
                Route::post('{saleId}/refunds', CreateRefundController::class)
                    ->middleware('throttle:refunds');
            });

            Route::post('payments/reconcile', ReconcileAdminPaymentsController::class);

            Route::prefix('shifts')->group(function (): void {
                Route::get('/', ListAdminShiftsController::class);
                Route::get('{shiftId}/report', ShowAdminShiftReportController::class);
                Route::post('{shiftId}/reopen', ReopenCashShiftController::class);
            });

            Route::prefix('users')->group(function (): void {
                Route::get('/', ListUsersController::class);
                Route::post('/', CreateUserController::class);
                Route::get('{userId}', ShowUserController::class);
                Route::patch('{userId}', UpdateUserController::class);
                Route::post('{userId}/mfa/reset', ResetManagerMfaController::class);
            });
        });

    Route::prefix('operational')
        ->middleware(['role:manager,operator', 'store.context'])
        ->group(function (): void {
            Route::get('catalog/products', ListOperationalProductsController::class);
            Route::get('customers', FindOperationalCustomerController::class);
            Route::post('customers', CreateCustomerController::class);

            Route::post('payments/reconcile', ReconcileOperationalPaymentsController::class);

            Route::post('shifts/open', OpenCashShiftController::class);
            Route::post('shifts/close', CloseCashShiftController::class);
            Route::get('shifts/current', CurrentCashShiftController::class);

            Route::middleware('shift.open')->group(function (): void {
                Route::get('pos', OperationalPosController::class);

                Route::post('sales', CreateSaleController::class);
                Route::get('sales/held', ListHeldSalesController::class);
                Route::get('sales/{saleId}', ShowSaleController::class);
                Route::post('sales/{saleId}/hold', HoldSaleController::class);
                Route::post('sales/{saleId}/resume', ResumeSaleController::class);
                Route::post('sales/{saleId}/customer', AttachCustomerToSaleController::class);
                Route::post('sales/{saleId}/promotions', ApplyPromotionToSaleController::class);
                Route::delete('sales/{saleId}/promotions/{promotionId}', RemovePromotionFromSaleController::class);
                Route::post('sales/{saleId}/lines', AddSaleLineController::class);
                Route::patch('sales/{saleId}/lines/{lineId}', UpdateSaleLineController::class);
                Route::delete('sales/{saleId}/lines/{lineId}', RemoveSaleLineController::class);
                Route::post('sales/{saleId}/complete', CompleteSaleController::class);
            });
        });
});
