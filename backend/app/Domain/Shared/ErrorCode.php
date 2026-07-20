<?php

declare(strict_types=1);

namespace App\Domain\Shared;

enum ErrorCode: string
{
    case AuthUnauthenticated = 'AUTH_UNAUTHENTICATED';
    case AuthInvalidCredentials = 'AUTH_INVALID_CREDENTIALS';
    case AuthTokenMissing = 'AUTH_TOKEN_MISSING';
    case AuthTokenInvalid = 'AUTH_TOKEN_INVALID';
    case AuthTokenExpired = 'AUTH_TOKEN_EXPIRED';
    case AuthForbidden = 'AUTH_FORBIDDEN';
    case AuthRoleDenied = 'AUTH_ROLE_DENIED';
    case AuthAdminOnly = 'AUTH_ADMIN_ONLY';
    case AuthOperatorOnly = 'AUTH_OPERATOR_ONLY';
    case AuthAccountInactive = 'AUTH_ACCOUNT_INACTIVE';
    case AuthAccountLocked = 'AUTH_ACCOUNT_LOCKED';
    case AuthEmailNotVerified = 'AUTH_EMAIL_NOT_VERIFIED';
    case AuthPasswordExpired = 'AUTH_PASSWORD_EXPIRED';
    case AuthStoreAccessDenied = 'AUTH_STORE_ACCESS_DENIED';
    case AuthStoreContextRequired = 'AUTH_STORE_CONTEXT_REQUIRED';
    case AuthSessionExpired = 'AUTH_SESSION_EXPIRED';
    case AuthTooManyAttempts = 'AUTH_TOO_MANY_ATTEMPTS';
    case AuthUserNotFound = 'AUTH_USER_NOT_FOUND';
    case AuthEmailDuplicate = 'AUTH_EMAIL_DUPLICATE';
    case AuthCannotModifySelf = 'AUTH_CANNOT_MODIFY_SELF';
    case AuthMfaRequired = 'AUTH_MFA_REQUIRED';
    case AuthMfaSetupRequired = 'AUTH_MFA_SETUP_REQUIRED';
    case AuthMfaInvalidCode = 'AUTH_MFA_INVALID_CODE';
    case AuthMfaNotPending = 'AUTH_MFA_NOT_PENDING';
    case AuthMfaAlreadyEnabled = 'AUTH_MFA_ALREADY_ENABLED';

    case StoreNotFound = 'STORE_NOT_FOUND';
    case StoreContextRequired = 'STORE_CONTEXT_REQUIRED';
    case StoreInactive = 'STORE_INACTIVE';
    case StoreNotAssigned = 'STORE_NOT_ASSIGNED';

    case ShiftNotOpen = 'SHIFT_NOT_OPEN';
    case ShiftAlreadyOpen = 'SHIFT_ALREADY_OPEN';
    case ShiftStoreMismatch = 'SHIFT_STORE_MISMATCH';
    case ShiftReopenDenied = 'SHIFT_REOPEN_DENIED';
    case ShiftNotFound = 'SHIFT_NOT_FOUND';

    case SaleNotFound = 'SALE_NOT_FOUND';
    case SaleLineNotFound = 'SALE_LINE_NOT_FOUND';
    case SaleEmptyCart = 'SALE_EMPTY_CART';
    case SaleAlreadyCompleted = 'SALE_ALREADY_COMPLETED';
    case SaleNegativeTotal = 'SALE_NEGATIVE_TOTAL';
    case SalePaymentMismatch = 'SALE_PAYMENT_MISMATCH';
    case SaleNoPayment = 'SALE_NO_PAYMENT';
    case SaleFiscalReceiptFailed = 'SALE_FISCAL_RECEIPT_FAILED';
    case SaleNotHeld = 'SALE_NOT_HELD';
    case SaleCartHeld = 'SALE_CART_HELD';

    case CatProductNotFound = 'CAT_PRODUCT_NOT_FOUND';
    case CatCategoryNotFound = 'CAT_CATEGORY_NOT_FOUND';
    case CatSkuDuplicate = 'CAT_SKU_DUPLICATE';
    case CatCategoryNameDuplicate = 'CAT_CATEGORY_NAME_DUPLICATE';
    case CatCategoryInUse = 'CAT_CATEGORY_IN_USE';
    case CatProductInUse = 'CAT_PRODUCT_IN_USE';

    case InvProductInactive = 'INV_PRODUCT_INACTIVE';
    case InvInsufficientStock = 'INV_INSUFFICIENT_STOCK';
    case InvAdjustmentReasonRequired = 'INV_ADJUSTMENT_REASON_REQUIRED';

    case CustNotFound = 'CUST_NOT_FOUND';
    case CustCpfDuplicate = 'CUST_CPF_DUPLICATE';
    case CustRequiredFieldMissing = 'CUST_REQUIRED_FIELD_MISSING';

    case PromoNotFound = 'PROMO_NOT_FOUND';
    case PromoNotApplicable = 'PROMO_NOT_APPLICABLE';
    case PromoExpired = 'PROMO_EXPIRED';
    case PromoNotAssigned = 'PROMO_NOT_ASSIGNED';
    case PromoNotCombinable = 'PROMO_NOT_COMBINABLE';

    case RefAmountExceedsSale = 'REF_AMOUNT_EXCEEDS_SALE';
    case RefSaleNotFound = 'REF_SALE_NOT_FOUND';
    case RefAlreadyFullyRefunded = 'REF_ALREADY_FULLY_REFUNDED';
    case RefReturnQtyInvalid = 'REF_RETURN_QTY_INVALID';

    case PayMethodUnsupported = 'PAY_METHOD_UNSUPPORTED';
    case PayCashInsufficient = 'PAY_CASH_INSUFFICIENT';
    case PayGatewayUnavailable = 'PAY_GATEWAY_UNAVAILABLE';
    case PayMethodNotImplemented = 'PAY_METHOD_NOT_IMPLEMENTED';
    case PayCardHolderNameInvalid = 'PAY_CARD_HOLDER_NAME_INVALID';
    case PayCardNumberInvalid = 'PAY_CARD_NUMBER_INVALID';
    case PayCardExpiryInvalid = 'PAY_CARD_EXPIRY_INVALID';
    case PayCardHolderMismatch = 'PAY_CARD_HOLDER_MISMATCH';
    case PayCardOwnershipUnconfirmed = 'PAY_CARD_OWNERSHIP_UNCONFIRMED';
    case PayWebhookInvalidSignature = 'PAY_WEBHOOK_INVALID_SIGNATURE';
    case PayWebhookPayloadInvalid = 'PAY_WEBHOOK_PAYLOAD_INVALID';
    case PayWebhookProviderUnsupported = 'PAY_WEBHOOK_PROVIDER_UNSUPPORTED';
    case PayWebhookUnknownReference = 'PAY_WEBHOOK_UNKNOWN_REFERENCE';
    case PayWebhookAmountMismatch = 'PAY_WEBHOOK_AMOUNT_MISMATCH';
    case PayWebhookInvalidTransition = 'PAY_WEBHOOK_INVALID_TRANSITION';

    case IdempotencyKeyRequired = 'IDEMPOTENCY_KEY_REQUIRED';
    case IdempotencyKeyReuse = 'IDEMPOTENCY_KEY_REUSE';
    case IdempotencyRequestInFlight = 'IDEMPOTENCY_REQUEST_IN_FLIGHT';

    public function httpStatus(): int
    {
        return match ($this) {
            self::AuthUnauthenticated,
            self::AuthInvalidCredentials,
            self::AuthTokenMissing,
            self::AuthTokenInvalid,
            self::AuthTokenExpired,
            self::AuthSessionExpired,
            self::AuthMfaInvalidCode,
            self::AuthMfaNotPending,
            self::PayWebhookInvalidSignature => 401,
            self::AuthTooManyAttempts => 429,
            self::AuthAccountLocked => 423,
            self::AuthStoreContextRequired,
            self::StoreContextRequired,
            self::ShiftNotOpen,
            self::ShiftStoreMismatch => 422,
            self::ShiftAlreadyOpen,
            self::SaleAlreadyCompleted,
            self::CatSkuDuplicate,
            self::CatCategoryNameDuplicate,
            self::CatCategoryInUse,
            self::CatProductInUse,
            self::CustCpfDuplicate,
            self::AuthEmailDuplicate,
            self::RefAlreadyFullyRefunded,
            self::IdempotencyKeyReuse,
            self::IdempotencyRequestInFlight => 409,
            self::StoreNotFound,
            self::ShiftNotFound,
            self::SaleNotFound,
            self::SaleLineNotFound,
            self::CatProductNotFound,
            self::CatCategoryNotFound,
            self::CustNotFound,
            self::PromoNotFound,
            self::AuthUserNotFound,
            self::RefSaleNotFound,
            self::PayWebhookUnknownReference => 404,
            self::SaleEmptyCart,
            self::SaleNegativeTotal,
            self::SalePaymentMismatch,
            self::SaleNoPayment,
            self::PayMethodUnsupported,
            self::PayCashInsufficient,
            self::PayCardHolderNameInvalid,
            self::PayCardNumberInvalid,
            self::PayCardExpiryInvalid,
            self::PayCardHolderMismatch,
            self::PayCardOwnershipUnconfirmed,
            self::PayWebhookPayloadInvalid,
            self::PayWebhookProviderUnsupported,
            self::PayWebhookAmountMismatch,
            self::PayWebhookInvalidTransition,
            self::SaleNotHeld,
            self::SaleCartHeld,
            self::InvProductInactive,
            self::InvInsufficientStock,
            self::InvAdjustmentReasonRequired,
            self::CustRequiredFieldMissing,
            self::PromoNotApplicable,
            self::PromoExpired,
            self::PromoNotAssigned,
            self::PromoNotCombinable,
            self::RefAmountExceedsSale,
            self::AuthCannotModifySelf,
            self::AuthMfaRequired,
            self::AuthMfaSetupRequired,
            self::AuthMfaAlreadyEnabled,
            self::RefReturnQtyInvalid,
            self::IdempotencyKeyRequired => 422,
            self::SaleFiscalReceiptFailed => 500,
            self::PayGatewayUnavailable => 503,
            self::PayMethodNotImplemented => 501,
            default => 403,
        };
    }

    public function message(): string
    {
        return match ($this) {
            self::AuthUnauthenticated => 'Authentication required.',
            self::AuthInvalidCredentials => 'Invalid email or password.',
            self::AuthTokenMissing => 'Authentication token is missing.',
            self::AuthTokenInvalid => 'Authentication token is invalid.',
            self::AuthTokenExpired => 'Authentication token has expired.',
            self::AuthForbidden => 'You do not have permission for this action.',
            self::AuthRoleDenied => 'Your role cannot access this area.',
            self::AuthAdminOnly => 'This area is restricted to managers.',
            self::AuthOperatorOnly => 'This area is restricted to cash operators.',
            self::AuthAccountInactive => 'Your account is inactive.',
            self::AuthAccountLocked => 'Your account is temporarily locked.',
            self::AuthEmailNotVerified => 'Email address is not verified.',
            self::AuthPasswordExpired => 'Password has expired; please reset.',
            self::AuthStoreAccessDenied => 'You do not have access to this store.',
            self::AuthStoreContextRequired => 'Store context must be selected.',
            self::AuthSessionExpired => 'Session has expired.',
            self::AuthTooManyAttempts => 'Too many login attempts. Try again later.',
            self::AuthUserNotFound => 'User not found.',
            self::AuthEmailDuplicate => 'Email already registered.',
            self::AuthCannotModifySelf => 'You cannot deactivate or demote your own account.',
            self::AuthMfaRequired => 'Multi-factor authentication is required.',
            self::AuthMfaSetupRequired => 'Multi-factor authentication setup is required before continuing.',
            self::AuthMfaInvalidCode => 'Invalid or expired authentication code.',
            self::AuthMfaNotPending => 'No multi-factor authentication challenge is pending.',
            self::AuthMfaAlreadyEnabled => 'Multi-factor authentication is already enabled.',
            self::StoreNotFound => 'Store not found.',
            self::StoreContextRequired => 'Store context must be selected.',
            self::StoreInactive => 'Store is inactive and cannot be selected.',
            self::StoreNotAssigned => 'Store is not assigned to this user.',
            self::ShiftNotOpen => 'Cannot sell without an open cash shift.',
            self::ShiftAlreadyOpen => 'You already have an open shift.',
            self::ShiftStoreMismatch => 'Shift belongs to another store.',
            self::ShiftReopenDenied => 'Manager authorization required to reopen shift.',
            self::ShiftNotFound => 'Cash shift not found.',
            self::SaleNotFound => 'Sale not found.',
            self::SaleLineNotFound => 'Sale line not found.',
            self::SaleEmptyCart => 'Cannot complete sale with an empty cart.',
            self::SaleAlreadyCompleted => 'Sale is already completed and cannot be modified.',
            self::SaleNegativeTotal => 'Sale total cannot be negative.',
            self::SalePaymentMismatch => 'Payment total does not match sale amount.',
            self::SaleNoPayment => 'At least one payment is required.',
            self::SaleFiscalReceiptFailed => 'Fiscal receipt could not be generated.',
            self::SaleNotHeld => 'Sale is not on hold.',
            self::SaleCartHeld => 'Sale is on hold; resume before modifying.',
            self::CatProductNotFound => 'Product not found.',
            self::CatCategoryNotFound => 'Category not found.',
            self::CatSkuDuplicate => 'Product SKU already exists.',
            self::CatCategoryNameDuplicate => 'Category name already exists.',
            self::CatCategoryInUse => 'Category cannot be deleted while products are assigned.',
            self::CatProductInUse => 'Product cannot be deleted after being sold.',
            self::InvProductInactive => 'Product is inactive and cannot be sold.',
            self::InvInsufficientStock => 'Insufficient stock for this product at this store.',
            self::InvAdjustmentReasonRequired => 'Stock adjustment requires a reason.',
            self::CustNotFound => 'Customer not found.',
            self::CustCpfDuplicate => 'CPF already registered.',
            self::CustRequiredFieldMissing => 'Required customer field is missing.',
            self::PromoNotFound => 'Promotion or coupon not found.',
            self::PromoNotApplicable => 'Promotion does not apply to this customer or sale.',
            self::PromoExpired => 'Promotion has expired.',
            self::PromoNotAssigned => 'Promotion was not assigned to this customer.',
            self::PromoNotCombinable => 'Promotion cannot be combined with another on this sale (unique vs accumulable rule).',
            self::RefAmountExceedsSale => 'Refund amount exceeds refundable balance.',
            self::RefSaleNotFound => 'Original sale not found for refund.',
            self::RefAlreadyFullyRefunded => 'Sale has already been fully refunded.',
            self::RefReturnQtyInvalid => 'Return quantity exceeds sold quantity.',
            self::PayMethodUnsupported => 'Payment method is not supported.',
            self::PayCashInsufficient => 'Cash received is less than amount due.',
            self::PayGatewayUnavailable => 'Payment service temporarily unavailable.',
            self::PayMethodNotImplemented => 'Payment method is not implemented.',
            self::PayCardHolderNameInvalid => 'Cardholder name is invalid.',
            self::PayCardNumberInvalid => 'Card number is invalid.',
            self::PayCardExpiryInvalid => 'Card expiry date is invalid.',
            self::PayCardHolderMismatch => 'Cardholder name does not match the indicated person.',
            self::PayCardOwnershipUnconfirmed => 'Card ownership for the indicated person was not confirmed.',
            self::PayWebhookInvalidSignature => 'Payment webhook signature is invalid.',
            self::PayWebhookPayloadInvalid => 'Payment webhook payload is invalid.',
            self::PayWebhookProviderUnsupported => 'Payment webhook provider is not supported.',
            self::PayWebhookUnknownReference => 'No payment line matches the webhook transaction reference.',
            self::PayWebhookAmountMismatch => 'Webhook amount does not match the payment line.',
            self::PayWebhookInvalidTransition => 'Payment line cannot transition to the webhook status.',
            self::IdempotencyKeyRequired => 'Idempotency-Key header is required.',
            self::IdempotencyKeyReuse => 'Idempotency-Key was already used with a different request payload.',
            self::IdempotencyRequestInFlight => 'A request with this Idempotency-Key is already in progress.',
        };
    }

    /**
     * @return list<self>
     */
    public static function authenticationErrors(): array
    {
        return [
            self::AuthUnauthenticated,
            self::AuthInvalidCredentials,
            self::AuthTokenMissing,
            self::AuthTokenInvalid,
            self::AuthTokenExpired,
            self::AuthForbidden,
            self::AuthRoleDenied,
            self::AuthAdminOnly,
            self::AuthOperatorOnly,
            self::AuthAccountInactive,
            self::AuthAccountLocked,
            self::AuthEmailNotVerified,
            self::AuthPasswordExpired,
            self::AuthStoreAccessDenied,
            self::AuthStoreContextRequired,
            self::AuthSessionExpired,
            self::AuthTooManyAttempts,
            self::AuthUserNotFound,
            self::AuthEmailDuplicate,
            self::AuthCannotModifySelf,
            self::AuthMfaRequired,
            self::AuthMfaSetupRequired,
            self::AuthMfaInvalidCode,
            self::AuthMfaNotPending,
            self::AuthMfaAlreadyEnabled,
        ];
    }

    /**
     * @return list<self>
     */
    public static function storeErrors(): array
    {
        return [
            self::StoreNotFound,
            self::StoreContextRequired,
            self::StoreInactive,
            self::StoreNotAssigned,
        ];
    }

    /**
     * @return list<self>
     */
    public static function shiftErrors(): array
    {
        return [
            self::ShiftNotOpen,
            self::ShiftAlreadyOpen,
            self::ShiftStoreMismatch,
            self::ShiftReopenDenied,
            self::ShiftNotFound,
        ];
    }

    /**
     * @return list<self>
     */
    public static function saleErrors(): array
    {
        return [
            self::SaleNotFound,
            self::SaleLineNotFound,
            self::SaleEmptyCart,
            self::SaleAlreadyCompleted,
            self::SaleNegativeTotal,
            self::SalePaymentMismatch,
            self::SaleNoPayment,
            self::SaleFiscalReceiptFailed,
            self::SaleNotHeld,
            self::SaleCartHeld,
        ];
    }

    /**
     * @return list<self>
     */
    public static function catalogErrors(): array
    {
        return [
            self::CatProductNotFound,
            self::CatCategoryNotFound,
            self::CatSkuDuplicate,
            self::CatCategoryNameDuplicate,
            self::CatCategoryInUse,
            self::CatProductInUse,
        ];
    }

    /**
     * @return list<self>
     */
    public static function inventoryErrors(): array
    {
        return [
            self::InvProductInactive,
            self::InvInsufficientStock,
            self::InvAdjustmentReasonRequired,
        ];
    }

    /**
     * @return list<self>
     */
    public static function customerErrors(): array
    {
        return [
            self::CustNotFound,
            self::CustCpfDuplicate,
            self::CustRequiredFieldMissing,
        ];
    }

    /**
     * @return list<self>
     */
    public static function promotionErrors(): array
    {
        return [
            self::PromoNotFound,
            self::PromoNotApplicable,
            self::PromoExpired,
            self::PromoNotAssigned,
            self::PromoNotCombinable,
        ];
    }

    /**
     * @return list<self>
     */
    public static function refundErrors(): array
    {
        return [
            self::RefAmountExceedsSale,
            self::RefSaleNotFound,
            self::RefAlreadyFullyRefunded,
            self::RefReturnQtyInvalid,
        ];
    }

    /**
     * @return list<self>
     */
    public static function paymentErrors(): array
    {
        return [
            self::PayMethodUnsupported,
            self::PayCashInsufficient,
            self::PayGatewayUnavailable,
            self::PayMethodNotImplemented,
            self::PayCardHolderNameInvalid,
            self::PayCardNumberInvalid,
            self::PayCardExpiryInvalid,
            self::PayCardHolderMismatch,
            self::PayCardOwnershipUnconfirmed,
            self::PayWebhookInvalidSignature,
            self::PayWebhookPayloadInvalid,
            self::PayWebhookProviderUnsupported,
            self::PayWebhookUnknownReference,
            self::PayWebhookAmountMismatch,
            self::PayWebhookInvalidTransition,
        ];
    }

    /**
     * @return list<self>
     */
    public static function idempotencyErrors(): array
    {
        return [
            self::IdempotencyKeyRequired,
            self::IdempotencyKeyReuse,
            self::IdempotencyRequestInFlight,
        ];
    }

    /**
     * @return array{code: string, message: string}
     */
    public function toErrorPayload(): array
    {
        return [
            'code' => $this->value,
            'message' => $this->message(),
        ];
    }
}
