import type { AdminAuditFilters, AuditAction, Store } from '../../../shared/api/types'
import styles from './SalesFilters.module.css'

type AuditLogFiltersProps = {
  draft: AdminAuditFilters
  stores: Store[]
  onChange: (next: AdminAuditFilters) => void
  onApply: () => void
  onClear: () => void
}

const actions: Array<{ value: AuditAction; label: string }> = [
  { value: 'catalog.product.price_changed', label: 'Price change' },
  { value: 'inventory.stock_adjusted', label: 'Stock adjust' },
  { value: 'refund.created', label: 'Refund' },
  { value: 'return.created', label: 'Return' },
  { value: 'promotion.created', label: 'Promotion create' },
  { value: 'promotion.updated', label: 'Promotion update' },
  { value: 'cash_shift.reopened', label: 'Shift reopen' },
  { value: 'identity.mfa_reset', label: 'MFA reset' },
]

/** Presentational audit log filters (dumb). */
export function AuditLogFilters({
  draft,
  stores,
  onChange,
  onApply,
  onClear,
}: AuditLogFiltersProps) {
  return (
    <form
      className={`panel ${styles.form}`}
      onSubmit={(event) => {
        event.preventDefault()
        onApply()
      }}
    >
      <label>
        From
        <input
          type="date"
          value={draft.from ?? ''}
          onChange={(event) => onChange({ ...draft, from: event.target.value || undefined })}
        />
      </label>
      <label>
        To
        <input
          type="date"
          value={draft.to ?? ''}
          onChange={(event) => onChange({ ...draft, to: event.target.value || undefined })}
        />
      </label>
      <label>
        Action
        <select
          value={draft.action ?? ''}
          onChange={(event) =>
            onChange({
              ...draft,
              action: (event.target.value || undefined) as AuditAction | undefined,
            })
          }
        >
          <option value="">Any</option>
          {actions.map((action) => (
            <option key={action.value} value={action.value}>
              {action.label}
            </option>
          ))}
        </select>
      </label>
      <label>
        Store
        <select
          value={draft.store_id ?? ''}
          onChange={(event) =>
            onChange({
              ...draft,
              store_id: event.target.value ? Number(event.target.value) : undefined,
            })
          }
        >
          <option value="">Assigned + global</option>
          {stores.map((store) => (
            <option key={store.id} value={store.id}>
              {store.name} ({store.code})
            </option>
          ))}
        </select>
      </label>
      <label>
        Actor ID
        <input
          type="number"
          min={1}
          value={draft.actor_id ?? ''}
          onChange={(event) =>
            onChange({
              ...draft,
              actor_id: event.target.value ? Number(event.target.value) : undefined,
            })
          }
        />
      </label>
      <label>
        Subject type
        <select
          value={draft.subject_type ?? ''}
          onChange={(event) =>
            onChange({
              ...draft,
              subject_type: event.target.value || undefined,
            })
          }
        >
          <option value="">Any</option>
          <option value="product">product</option>
          <option value="refund">refund</option>
          <option value="promotion">promotion</option>
        </select>
      </label>
      <label>
        Subject ID
        <input
          type="number"
          min={1}
          value={draft.subject_id ?? ''}
          onChange={(event) =>
            onChange({
              ...draft,
              subject_id: event.target.value ? Number(event.target.value) : undefined,
            })
          }
        />
      </label>
      <div className={styles.actions}>
        <button type="submit" className="btn">
          Apply filters
        </button>
        <button type="button" className="btn btn-ghost" onClick={onClear}>
          Clear
        </button>
      </div>
    </form>
  )
}
