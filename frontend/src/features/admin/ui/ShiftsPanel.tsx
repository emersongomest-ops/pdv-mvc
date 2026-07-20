import type { AdminShiftSummary, ShiftClosingReport, Store } from '../../../shared/api/types'
import { MoneyText } from '../../../shared/ui/MoneyText'
import styles from './ShiftsPanel.module.css'

type ShiftsPanelProps = {
  stores: Store[]
  storeIdInput: string
  onStoreIdChange: (value: string) => void
  onLoadShifts: () => void
  shifts: AdminShiftSummary[]
  report: ShiftClosingReport | null
  selectedShiftId: number | null
  onSelectShift: (shiftId: number) => void
  onReopenShift: (shiftId: number) => void
  loading: boolean
  reopeningId: number | null
  success: string | null
}

function formatWhen(value: string | null): string {
  if (!value) return '—'
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value
  return date.toLocaleString()
}

/** Presentational admin shifts panel (dumb). */
export function ShiftsPanel({
  stores,
  storeIdInput,
  onStoreIdChange,
  onLoadShifts,
  shifts,
  report,
  selectedShiftId,
  onSelectShift,
  onReopenShift,
  loading,
  reopeningId,
  success,
}: ShiftsPanelProps) {
  return (
    <div className={styles.stack}>
      {success ? <p className={styles.success}>{success}</p> : null}
      <form
        className={`panel ${styles.lookup}`}
        onSubmit={(event) => {
          event.preventDefault()
          onLoadShifts()
        }}
      >
        <label>
          Store
          <select
            value={storeIdInput}
            onChange={(event) => onStoreIdChange(event.target.value)}
          >
            <option value="">Select assigned store</option>
            {stores.map((store) => (
              <option key={store.id} value={store.id}>
                {store.name} ({store.code})
              </option>
            ))}
          </select>
        </label>
        <button type="submit" className="btn" disabled={loading || !storeIdInput}>
          {loading ? 'Loading…' : 'List shifts'}
        </button>
      </form>

      <section className={`panel ${styles.list}`}>
        <h2>Shifts</h2>
        {shifts.length === 0 ? (
          <p className={styles.empty}>No shifts loaded for this store.</p>
        ) : (
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Status</th>
                <th>Operator</th>
                <th>Opened</th>
                <th>Closed</th>
                <th />
              </tr>
            </thead>
            <tbody>
              {shifts.map((shift) => (
                <tr
                  key={shift.id}
                  className={selectedShiftId === shift.id ? styles.selected : undefined}
                >
                  <td className={styles.mono}>#{shift.id}</td>
                  <td>{shift.status}</td>
                  <td>{shift.operator_name ?? shift.operator_id}</td>
                  <td>{formatWhen(shift.opened_at)}</td>
                  <td>{formatWhen(shift.closed_at)}</td>
                  <td>
                    <button
                      type="button"
                      className="btn btn-ghost"
                      onClick={() => onSelectShift(shift.id)}
                    >
                      Report
                    </button>
                    {shift.status === 'closed' ? (
                      <button
                        type="button"
                        className="btn btn-ghost"
                        disabled={reopeningId === shift.id}
                        onClick={() => onReopenShift(shift.id)}
                      >
                        {reopeningId === shift.id ? 'Reopening…' : 'Reopen'}
                      </button>
                    ) : null}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </section>

      {report && (
        <section className={`panel ${styles.report}`}>
          <h2>Closing report · shift #{report.shift_id}</h2>
          <p className={styles.meta}>
            Store {report.store_code ?? report.store_id} · Operator{' '}
            {report.operator_name ?? report.operator_id} · {report.status}
          </p>
          <dl className={styles.grid}>
            <div>
              <dt>Sales</dt>
              <dd>
                {report.sales_count} · <MoneyText value={report.sales_total} />
              </dd>
            </div>
            <div>
              <dt>Opening cash</dt>
              <dd>
                <MoneyText value={report.opening_cash_amount} />
              </dd>
            </div>
            <div>
              <dt>Expected cash</dt>
              <dd>
                <MoneyText value={report.expected_cash_amount} />
              </dd>
            </div>
            <div>
              <dt>Closing cash</dt>
              <dd>
                {report.closing_cash_amount ? (
                  <MoneyText value={report.closing_cash_amount} />
                ) : (
                  '—'
                )}
              </dd>
            </div>
            <div>
              <dt>Cash variance</dt>
              <dd>{report.cash_variance ?? '—'}</dd>
            </div>
          </dl>
          <h3>By payment method</h3>
          {report.totals_by_payment_method.length === 0 ? (
            <p className={styles.empty}>No payments in this shift.</p>
          ) : (
            <ul>
              {report.totals_by_payment_method.map((row) => (
                <li key={row.method}>
                  <span>{row.method}</span>
                  <MoneyText value={row.amount} />
                </li>
              ))}
            </ul>
          )}
        </section>
      )}
    </div>
  )
}
