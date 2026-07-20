import type { LineSelection } from '../hooks/useAdminRefunds'
import type { Refund, RefundType, Sale } from '../../../shared/api/types'
import { MoneyText } from '../../../shared/ui/MoneyText'
import styles from './RefundsPanel.module.css'

type RefundsPanelProps = {
  saleIdInput: string
  onSaleIdChange: (value: string) => void
  onLookup: () => void
  sale: Sale | null
  refunds: Refund[]
  lineSelections: LineSelection[]
  onLineChange: (saleLineId: number, patch: Partial<LineSelection>) => void
  type: RefundType
  onTypeChange: (type: RefundType) => void
  reason: string
  onReasonChange: (reason: string) => void
  needsLines: boolean
  loading: boolean
  submitting: boolean
  success: string | null
  onSubmit: () => void
}

const refundTypes: Array<{ value: RefundType; label: string }> = [
  { value: 'full_refund', label: 'Full refund' },
  { value: 'partial_refund', label: 'Partial refund' },
  { value: 'full_return', label: 'Full return' },
  { value: 'partial_return', label: 'Partial return' },
]

/** Presentational admin refunds panel (dumb). */
export function RefundsPanel({
  saleIdInput,
  onSaleIdChange,
  onLookup,
  sale,
  refunds,
  lineSelections,
  onLineChange,
  type,
  onTypeChange,
  reason,
  onReasonChange,
  needsLines,
  loading,
  submitting,
  success,
  onSubmit,
}: RefundsPanelProps) {
  return (
    <div className={styles.stack}>
      <form
        className={`panel ${styles.lookup}`}
        onSubmit={(event) => {
          event.preventDefault()
          onLookup()
        }}
      >
        <label>
          Sale ID
          <input
            type="number"
            min={1}
            value={saleIdInput}
            onChange={(event) => onSaleIdChange(event.target.value)}
            placeholder="e.g. 42"
          />
        </label>
        <button type="submit" className="btn" disabled={loading}>
          {loading ? 'Loading…' : 'Load sale'}
        </button>
      </form>

      {sale && (
        <>
          <section className={`panel ${styles.sale}`}>
            <header>
              <h2>Sale #{sale.id}</h2>
              <p>
                Store {sale.store_id} · Operator {sale.operator_id} · {sale.status} · Total{' '}
                <MoneyText value={sale.total} />
              </p>
            </header>

            <table>
              <thead>
                <tr>
                  {needsLines && <th>Use</th>}
                  <th>Line</th>
                  <th>Product</th>
                  <th>Qty</th>
                  {needsLines && <th>Refund qty</th>}
                  <th>Line total</th>
                </tr>
              </thead>
              <tbody>
                {sale.lines.map((line) => {
                  const selection = lineSelections.find((item) => item.sale_line_id === line.id)
                  return (
                    <tr key={line.id}>
                      {needsLines && (
                        <td>
                          <input
                            type="checkbox"
                            checked={selection?.selected ?? false}
                            onChange={(event) =>
                              onLineChange(line.id, { selected: event.target.checked })
                            }
                          />
                        </td>
                      )}
                      <td className={styles.mono}>#{line.id}</td>
                      <td>{line.product_id}</td>
                      <td>{line.quantity}</td>
                      {needsLines && (
                        <td>
                          <input
                            type="number"
                            min={1}
                            max={line.quantity}
                            disabled={!selection?.selected}
                            value={selection?.quantity ?? line.quantity}
                            onChange={(event) =>
                              onLineChange(line.id, {
                                quantity: Number(event.target.value) || 1,
                              })
                            }
                          />
                        </td>
                      )}
                      <td>
                        <MoneyText value={line.line_total} />
                      </td>
                    </tr>
                  )
                })}
              </tbody>
            </table>
          </section>

          <form
            className={`panel ${styles.form}`}
            onSubmit={(event) => {
              event.preventDefault()
              onSubmit()
            }}
          >
            <label>
              Type
              <select value={type} onChange={(event) => onTypeChange(event.target.value as RefundType)}>
                {refundTypes.map((option) => (
                  <option key={option.value} value={option.value}>
                    {option.label}
                  </option>
                ))}
              </select>
            </label>
            <label className={styles.reason}>
              Reason (required)
              <textarea
                value={reason}
                onChange={(event) => onReasonChange(event.target.value)}
                rows={3}
                minLength={3}
                maxLength={500}
                placeholder="Customer returned item — defective packaging"
              />
            </label>
            <button type="submit" className="btn" disabled={submitting}>
              {submitting ? 'Recording…' : 'Record refund'}
            </button>
            {success && <p className={styles.success}>{success}</p>}
          </form>

          <section className={`panel ${styles.history}`}>
            <h2>Refund history</h2>
            {refunds.length === 0 ? (
              <p className={styles.empty}>No refunds for this sale yet.</p>
            ) : (
              <ul>
                {refunds.map((refund) => (
                  <li key={refund.id}>
                    <strong>
                      #{refund.id} · {refund.type} · <MoneyText value={refund.amount} />
                    </strong>
                    <span>
                      {refund.operator_name ?? `User ${refund.user_id}`} — {refund.reason}
                    </span>
                  </li>
                ))}
              </ul>
            )}
          </section>
        </>
      )}
    </div>
  )
}
