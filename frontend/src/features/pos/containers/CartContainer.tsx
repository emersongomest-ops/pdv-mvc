import { CartShell } from '../components/CartShell'
import {
  usePosActivity,
  usePosCatalogState,
  usePosSaleState,
} from '../context/PosWorkspaceState'
import { usePosCustomer } from '../hooks/usePosCustomer'
import { usePosHeld } from '../hooks/usePosHeld'
import { usePosPayment } from '../hooks/usePosPayment'
import { usePosPromotion } from '../hooks/usePosPromotion'

/** Smart: cart hooks → CartShell. */
export function CartContainer({ shiftOpen }: { shiftOpen: boolean }) {
  const { sale } = usePosSaleState()
  const { products } = usePosCatalogState()
  const { busy } = usePosActivity()
  const customer = usePosCustomer()
  const promo = usePosPromotion()
  const held = usePosHeld(shiftOpen, { loadOnMount: false })
  const payment = usePosPayment(() => customer.clearCustomer())

  async function handlePark() {
    await held.parkSale()
    customer.clearCustomer()
  }

  return (
    <CartShell
      sale={sale}
      products={products}
      customer={customer.customer}
      cpfInput={customer.cpfInput}
      promoCode={promo.promoCode}
      holdLabel={held.holdLabel}
      cashReceived={payment.cashReceived}
      payMethod={payment.payMethod}
      cashChange={payment.cashChange}
      busy={busy}
      onCpfChange={customer.setCpfInput}
      onPromoChange={promo.setPromoCode}
      onHoldLabelChange={held.setHoldLabel}
      onCashReceivedChange={payment.setCashReceived}
      onPayMethodChange={payment.setPayMethod}
      onLookupCustomer={() => void customer.lookupCustomer()}
      onApplyPromo={() => void promo.applyPromo()}
      onParkSale={() => void handlePark()}
      onPay={() => void payment.pay()}
    />
  )
}
