export type ApiError = {
  code: string
  message: string
}

export type User = {
  id: number
  name: string
  email: string
  role: 'operator' | 'manager'
}

export type UserRole = 'operator' | 'manager'

export type AdminUserStore = {
  id: number
  name: string
  code: string
}

export type AdminUser = {
  id: number
  name: string
  email: string
  role: UserRole
  is_active: boolean
  mfa_enabled: boolean
  stores: AdminUserStore[]
  created_at: string | null
  updated_at: string | null
}

export type CreateAdminUserPayload = {
  name: string
  email: string
  password: string
  password_confirmation: string
  role: UserRole
  is_active?: boolean
  store_ids: number[]
}

export type UpdateAdminUserPayload = {
  name?: string
  email?: string
  password?: string
  password_confirmation?: string
  role?: UserRole
  is_active?: boolean
  store_ids?: number[]
}

export type Store = {
  id: number
  name: string
  code: string
}

export type Product = {
  id: number
  sku: string
  name: string
  category_id: number | null
  base_price: string
  track_stock: boolean
  available_quantity: number | null
}

export type AdminProduct = {
  id: number
  sku: string
  name: string
  category_id: number | null
  category_name: string | null
  base_price: string
  is_active: boolean
}

export type SaleLine = {
  id: number
  product_id: number
  quantity: number
  unit_price: string
  line_discount: string
  line_total: string
}

export type Sale = {
  id: number
  store_id: number
  operator_id: number
  cash_shift_id: number
  customer_id: number | null
  status: string
  hold_label?: string | null
  held_at?: string | null
  subtotal: string
  discount_total: string
  total: string
  completed_at?: string | null
  lines: SaleLine[]
  payments?: Array<{
    id: number
    method: string
    amount: string
  }>
  promotions?: Array<{
    promotion_id: number
    code: string | null
    discount_amount: string
    stacking_mode?: string | null
  }>
}

export type CustomerStoreStat = {
  store_id: number
  purchase_count: number
  total_spend: string
}

export type Customer = {
  id: number
  name: string
  email: string
  cpf: string
  phone: string
  birth_date: string | null
  address: string | null
  lifetime_spend: string
  store_stats?: CustomerStoreStat[]
}

export type CustomerPayload = {
  name: string
  email: string
  cpf: string
  phone: string
  birth_date: string
  address: string
}

export type DiscountType = 'percent' | 'fixed'
export type StackingMode = 'unique' | 'accumulable'

export type Promotion = {
  id: number
  code: string
  name: string
  discount_type: DiscountType
  discount_value: string
  stacking_mode: StackingMode
  applies_to_all_customers: boolean
  is_active: boolean
  starts_at: string | null
  ends_at: string | null
  customer_ids: number[]
}

export type PromotionPayload = {
  code: string
  name: string
  discount_type: DiscountType
  discount_value: string
  stacking_mode: StackingMode
  applies_to_all_customers: boolean
  is_active: boolean
  starts_at: string | null
  ends_at: string | null
  customer_ids: number[]
}

export type StoreInventoryRow = {
  store_id: number
  product_id: number
  product_name: string | null
  product_sku: string | null
  quantity: number
  track_stock: boolean
}

export type AdjustInventoryPayload = {
  store_id: number
  product_id: number
  quantity: number
  reason: string
}

export type AdminDashboardMetrics = {
  products_total: number
  products_active: number
  products_inactive: number
  customers_total: number
  sales_completed: number
  open_shifts: number
}

export type AdminNotification = {
  id: string
  kind: string | null
  data: {
    kind?: string
    sale_id?: number
    store_id?: number
    operator_id?: number
    total?: string
    message?: string
  }
  read_at: string | null
  created_at: string | null
}

export type AdminSaleSummary = {
  id: number
  store_id: number
  store_code: string | null
  operator_id: number
  operator_name: string | null
  customer_id: number | null
  customer_name: string | null
  cash_shift_id: number
  status: string
  subtotal: string
  discount_total: string
  total: string
  completed_at: string | null
  payment_methods: string[]
}

export type AdminSalesFilters = {
  from?: string
  to?: string
  store_id?: number
  operator_id?: number
  customer_id?: number
  payment_method?: string
  status?: string
}

export type RefundType =
  | 'full_refund'
  | 'partial_refund'
  | 'full_return'
  | 'partial_return'

export type RefundLine = {
  sale_line_id: number
  quantity: number
  amount: string
  restocked: boolean
}

export type Refund = {
  id: number
  sale_id: number
  store_id: number
  user_id: number
  operator_name: string | null
  type: RefundType
  reason: string
  amount: string
  payment_refund_reference: string | null
  created_at: string | null
  lines: RefundLine[]
}

export type CreateRefundPayload = {
  type: RefundType
  reason: string
  lines?: Array<{ sale_line_id: number; quantity: number }>
}

export type AdminShiftSummary = {
  id: number
  store_id: number
  store_code: string | null
  operator_id: number
  operator_name: string | null
  status: string
  opening_cash_amount: string
  closing_cash_amount: string | null
  opened_at: string | null
  closed_at: string | null
}

export type ShiftClosingReport = {
  shift_id: number
  store_id: number
  store_code: string | null
  operator_id: number
  operator_name: string | null
  status: string
  sales_count: number
  sales_total: string
  totals_by_payment_method: Array<{ method: string; amount: string }>
  opening_cash_amount: string
  expected_cash_amount: string
  closing_cash_amount: string | null
  cash_variance: string | null
  opened_at: string | null
  closed_at: string | null
}

export type AuditAction =
  | 'catalog.product.price_changed'
  | 'inventory.stock_adjusted'
  | 'refund.created'
  | 'return.created'
  | 'promotion.created'
  | 'promotion.updated'
  | 'cash_shift.reopened'
  | 'identity.mfa_reset'

export type AuditLogEntry = {
  id: number
  action: AuditAction
  actor: { id: number; name: string; email: string } | null
  store: { id: number; name: string; code: string } | null
  subject_type: string
  subject_id: number
  old_values: Record<string, unknown> | null
  new_values: Record<string, unknown> | null
  metadata: Record<string, unknown> | null
  occurred_at: string | null
}

export type AdminAuditFilters = {
  from?: string
  to?: string
  action?: AuditAction
  actor_id?: number
  store_id?: number
  subject_type?: string
  subject_id?: number
  cursor?: string
  per_page?: number
}

export type RegistrationBucket = {
  date: string
  count: number
}

export type RecurrenceMetrics = {
  customers_with_purchases: number
  customers_with_repeat: number
  index: string
}

export type CustomerStoreSpend = {
  store_id: number
  store_code: string | null
  purchase_count: number
  total_spend: string
}

export type CustomerSpendSummary = {
  customer_id: number
  name: string
  cpf: string
  lifetime_spend: string
  store_spend: CustomerStoreSpend[]
}

export type AdminAnalytics = {
  registrations_over_time: RegistrationBucket[]
  recurrence: RecurrenceMetrics
  top_customers_by_spend: CustomerSpendSummary[]
}

export type CampaignCustomerFilters = {
  birth_month?: number
  region?: string
}

