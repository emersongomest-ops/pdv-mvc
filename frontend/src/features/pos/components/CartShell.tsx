import type { Product, Sale } from '../../../shared/api/types'
import { CartCustomerForm } from './CartCustomerForm'
import { CartLines } from './CartLines'
import { CartPaymentForm } from './CartPaymentForm'
import type { PayMethod } from '../hooks/usePosPayment'
import type { Customer } from '../../../shared/api/types'
import styles from './CartShell.module.css'

type CartShellProps = {
  sale: Sale | null
  products: Product[]
  customer: Customer | null
  cpfInput: string
  promoCode: string
  holdLabel: string
  cashReceived: string
  payMethod: PayMethod
  cashChange: number | null
  busy: boolean
  onCpfChange: (value: string) => void
  onPromoChange: (value: string) => void
  onHoldLabelChange: (value: string) => void
  onCashReceivedChange: (value: string) => void
  onPayMethodChange: (method: PayMethod) => void
  onLookupCustomer: () => void
  onApplyPromo: () => void
  onParkSale: () => void
  onPay: () => void
}

/** Dumb shell: composes cart presentational pieces. */
export function CartShell(props: CartShellProps) {
  return (
    <aside className={`panel ${styles.cart}`}>
      <h2>Cart {props.sale ? `#${props.sale.id}` : ''}</h2>
      <CartCustomerForm
        sale={props.sale}
        customer={props.customer}
        cpfInput={props.cpfInput}
        promoCode={props.promoCode}
        busy={props.busy}
        onCpfChange={props.onCpfChange}
        onPromoChange={props.onPromoChange}
        onLookupCustomer={props.onLookupCustomer}
        onApplyPromo={props.onApplyPromo}
      />
      <CartLines sale={props.sale} products={props.products} />
      <CartPaymentForm
        sale={props.sale}
        payMethod={props.payMethod}
        cashReceived={props.cashReceived}
        cashChange={props.cashChange}
        holdLabel={props.holdLabel}
        busy={props.busy}
        onPayMethodChange={props.onPayMethodChange}
        onCashReceivedChange={props.onCashReceivedChange}
        onHoldLabelChange={props.onHoldLabelChange}
        onParkSale={props.onParkSale}
        onPay={props.onPay}
      />
    </aside>
  )
}
