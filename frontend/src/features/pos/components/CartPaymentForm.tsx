import type { Sale } from '../../../shared/api/types'
import { MoneyText } from '../../../shared/ui/MoneyText'
import type { PayMethod } from '../hooks/usePosPayment'
import styles from './CartPaymentForm.module.css'

type CartPaymentFormProps = {
  sale: Sale | null
  payMethod: PayMethod
  cashReceived: string
  cashChange: number | null
  holdLabel: string
  busy: boolean
  onPayMethodChange: (method: PayMethod) => void
  onCashReceivedChange: (value: string) => void
  onHoldLabelChange: (value: string) => void
  onParkSale: () => void
  onPay: () => void
}

/** Dumb: pay method + hold + complete. */
export function CartPaymentForm({
  sale,
  payMethod,
  cashReceived,
  cashChange,
  holdLabel,
  busy,
  onPayMethodChange,
  onCashReceivedChange,
  onHoldLabelChange,
  onParkSale,
  onPay,
}: CartPaymentFormProps) {
  return (
    <>
      <div className={styles.payMethods}>
        <label>
          <input
            type="radio"
            name="pay"
            checked={payMethod === 'pix'}
            onChange={() => onPayMethodChange('pix')}
          />
          PIX
        </label>
        <label>
          <input
            type="radio"
            name="pay"
            checked={payMethod === 'cash'}
            onChange={() => onPayMethodChange('cash')}
          />
          Cash
        </label>
      </div>

      {payMethod === 'cash' ? (
        <div className="field">
          <label htmlFor="cashReceived">Cash received</label>
          <input
            id="cashReceived"
            className="price"
            value={cashReceived}
            onChange={(e) => onCashReceivedChange(e.target.value)}
            placeholder={sale?.total ?? '0.00'}
          />
          {cashChange !== null ? (
            <p className={styles.change}>
              Change: <MoneyText value={Math.max(cashChange, 0)} />
            </p>
          ) : null}
        </div>
      ) : null}

      <div className={styles.actions}>
        <div className={styles.toolRow}>
          <input
            placeholder="Hold label"
            value={holdLabel}
            onChange={(e) => onHoldLabelChange(e.target.value)}
          />
          <button type="button" className="btn btn-ghost" disabled={busy} onClick={onParkSale}>
            Hold
          </button>
        </div>
        <button
          type="button"
          className="btn btn-primary"
          disabled={busy || !sale || sale.lines.length === 0}
          onClick={onPay}
        >
          Complete · {payMethod.toUpperCase()}
        </button>
      </div>
    </>
  )
}
