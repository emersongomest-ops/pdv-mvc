import type { Customer, Promotion } from '../../../shared/api/types'
import type { PromotionFormState } from '../hooks/useAdminPromotions'
import styles from './UsersPanel.module.css'

type PromotionsPanelProps = {
  promotions: Promotion[]
  customers: Customer[]
  form: PromotionFormState
  editingId: number | null
  onFormChange: <K extends keyof PromotionFormState>(key: K, value: PromotionFormState[K]) => void
  onToggleCustomer: (customerId: number) => void
  onStartCreate: () => void
  onStartEdit: (promotion: Promotion) => void
  onSave: () => void
  loading: boolean
  loadingMore?: boolean
  nextCursor?: string | null
  onLoadMore?: () => void
  saving: boolean
  success: string | null
}

export function PromotionsPanel({
  promotions,
  customers,
  form,
  editingId,
  onFormChange,
  onToggleCustomer,
  onStartCreate,
  onStartEdit,
  onSave,
  loading,
  loadingMore = false,
  nextCursor = null,
  onLoadMore,
  saving,
  success,
}: PromotionsPanelProps) {
  return (
    <div className={styles.stack}>
      <div className={`panel ${styles.toolbar}`}>
        <button type="button" className="btn" onClick={onStartCreate} disabled={loading}>
          New promotion
        </button>
      </div>

      <section className={`panel ${styles.list}`}>
        <h2>Promotions</h2>
        {promotions.length === 0 ? (
          <p className={styles.empty}>No promotions yet.</p>
        ) : (
          <table>
            <thead>
              <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Discount</th>
                <th>Stacking</th>
                <th>Status</th>
                <th />
              </tr>
            </thead>
            <tbody>
              {promotions.map((promotion) => (
                <tr key={promotion.id}>
                  <td className={styles.mono}>{promotion.code}</td>
                  <td>{promotion.name}</td>
                  <td>
                    {promotion.discount_type === 'percent'
                      ? `${promotion.discount_value}%`
                      : `R$ ${promotion.discount_value}`}
                  </td>
                  <td>{promotion.stacking_mode}</td>
                  <td>
                    <span
                      className={`${styles.badge} ${promotion.is_active ? styles.active : styles.inactive}`}
                    >
                      {promotion.is_active ? 'Active' : 'Inactive'}
                    </span>
                  </td>
                  <td>
                    <button
                      type="button"
                      className="btn btn-ghost"
                      onClick={() => onStartEdit(promotion)}
                    >
                      Edit
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
        {nextCursor && onLoadMore ? (
          <div className={styles.footer}>
            <button type="button" className="btn" disabled={loadingMore} onClick={onLoadMore}>
              {loadingMore ? 'Loading…' : 'Load more'}
            </button>
          </div>
        ) : null}
      </section>

      <section className={`panel ${styles.form}`}>
        <h2>{editingId === null ? 'Create promotion' : `Edit promotion #${editingId}`}</h2>
        {success ? <p className={styles.success}>{success}</p> : null}
        <div className={styles.grid}>
          <label>
            Code
            <input value={form.code} onChange={(e) => onFormChange('code', e.target.value)} />
          </label>
          <label>
            Name
            <input value={form.name} onChange={(e) => onFormChange('name', e.target.value)} />
          </label>
          <label>
            Discount type
            <select
              value={form.discount_type}
              onChange={(e) => onFormChange('discount_type', e.target.value as 'percent' | 'fixed')}
            >
              <option value="percent">Percent</option>
              <option value="fixed">Fixed</option>
            </select>
          </label>
          <label>
            Discount value
            <input
              value={form.discount_value}
              onChange={(e) => onFormChange('discount_value', e.target.value)}
            />
          </label>
          <label>
            Stacking
            <select
              value={form.stacking_mode}
              onChange={(e) =>
                onFormChange('stacking_mode', e.target.value as 'unique' | 'accumulable')
              }
            >
              <option value="unique">Unique (exclusive)</option>
              <option value="accumulable">Accumulable</option>
            </select>
          </label>
          <label>
            Starts at
            <input
              type="date"
              value={form.starts_at ?? ''}
              onChange={(e) => onFormChange('starts_at', e.target.value)}
            />
          </label>
          <label>
            Ends at
            <input
              type="date"
              value={form.ends_at ?? ''}
              onChange={(e) => onFormChange('ends_at', e.target.value)}
            />
          </label>
        </div>
        <label className={styles.check}>
          <input
            type="checkbox"
            checked={form.is_active}
            onChange={(e) => onFormChange('is_active', e.target.checked)}
          />
          Active
        </label>
        <label className={styles.check}>
          <input
            type="checkbox"
            checked={form.applies_to_all_customers}
            onChange={(e) => onFormChange('applies_to_all_customers', e.target.checked)}
          />
          Applies to all customers
        </label>
        {!form.applies_to_all_customers ? (
          <div className={styles.stores}>
            {customers.map((customer) => (
              <label key={customer.id} className={styles.check}>
                <input
                  type="checkbox"
                  checked={form.customer_ids.includes(customer.id)}
                  onChange={() => onToggleCustomer(customer.id)}
                />
                {customer.name} ({customer.cpf})
              </label>
            ))}
          </div>
        ) : null}
        <button type="button" className="btn" disabled={saving} onClick={onSave}>
          {saving ? 'Saving…' : editingId === null ? 'Create' : 'Save'}
        </button>
      </section>
    </div>
  )
}
