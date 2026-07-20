import type { AdminSalesFilters, Store } from '../../../shared/api/types'
import styles from './SalesFilters.module.css'

type SalesFiltersProps = {
  draft: AdminSalesFilters
  stores: Store[]
  onChange: (next: AdminSalesFilters) => void
  onApply: () => void
  onClear: () => void
}

const paymentMethods = [
  'cash',
  'pix',
  'debit_card',
  'credit_card',
  'voucher',
  'store_credit',
  'other',
]

/** Presentational admin sales filters (dumb). */
export function SalesFilters({ draft, stores, onChange, onApply, onClear }: SalesFiltersProps) {
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
          <option value="">All assigned</option>
          {stores.map((store) => (
            <option key={store.id} value={store.id}>
              {store.name} ({store.code})
            </option>
          ))}
        </select>
      </label>
      <label>
        Operator ID
        <input
          type="number"
          min={1}
          value={draft.operator_id ?? ''}
          onChange={(event) =>
            onChange({
              ...draft,
              operator_id: event.target.value ? Number(event.target.value) : undefined,
            })
          }
        />
      </label>
      <label>
        Customer ID
        <input
          type="number"
          min={1}
          value={draft.customer_id ?? ''}
          onChange={(event) =>
            onChange({
              ...draft,
              customer_id: event.target.value ? Number(event.target.value) : undefined,
            })
          }
        />
      </label>
      <label>
        Payment
        <select
          value={draft.payment_method ?? ''}
          onChange={(event) =>
            onChange({
              ...draft,
              payment_method: event.target.value || undefined,
            })
          }
        >
          <option value="">Any</option>
          {paymentMethods.map((method) => (
            <option key={method} value={method}>
              {method}
            </option>
          ))}
        </select>
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
