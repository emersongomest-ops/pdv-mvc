import type { Customer, Sale } from '../../../shared/api/types'
import { MoneyText } from '../../../shared/ui/MoneyText'
import styles from './CartCustomerForm.module.css'

type CartCustomerFormProps = {
  sale: Sale | null
  customer: Customer | null
  cpfInput: string
  promoCode: string
  busy: boolean
  onCpfChange: (value: string) => void
  onPromoChange: (value: string) => void
  onLookupCustomer: () => void
  onApplyPromo: () => void
}

/** Dumb: CPF attach + promo code. */
export function CartCustomerForm({
  sale,
  customer,
  cpfInput,
  promoCode,
  busy,
  onCpfChange,
  onPromoChange,
  onLookupCustomer,
  onApplyPromo,
}: CartCustomerFormProps) {
  return (
    <div className={styles.tools}>
      <div className={styles.toolRow}>
        <input
          placeholder="CPF cliente"
          value={cpfInput}
          onChange={(e) => onCpfChange(e.target.value)}
        />
        <button type="button" className="btn btn-ghost" disabled={busy} onClick={onLookupCustomer}>
          Attach
        </button>
      </div>
      {customer ? (
        <p className={styles.tag}>
          {customer.name} · {customer.cpf}
        </p>
      ) : sale?.customer_id ? (
        <p className={styles.tag}>Customer #{sale.customer_id}</p>
      ) : null}

      <div className={styles.toolRow}>
        <input
          placeholder="Cupom / promo"
          value={promoCode}
          onChange={(e) => onPromoChange(e.target.value)}
        />
        <button type="button" className="btn btn-ghost" disabled={busy} onClick={onApplyPromo}>
          Apply
        </button>
      </div>
      {(sale?.promotions?.length ?? 0) > 0 ? (
        <ul className={styles.promoList}>
          {sale!.promotions!.map((promo) => (
            <li key={promo.promotion_id}>
              {promo.code} (−
              <MoneyText value={promo.discount_amount} />)
            </li>
          ))}
        </ul>
      ) : null}
    </div>
  )
}
