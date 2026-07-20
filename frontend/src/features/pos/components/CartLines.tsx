import type { Product, Sale } from '../../../shared/api/types'
import { MoneyText } from '../../../shared/ui/MoneyText'
import styles from './CartLines.module.css'

type CartLinesProps = {
  sale: Sale | null
  products: Product[]
}

/** Dumb: cart line items + totals. */
export function CartLines({ sale, products }: CartLinesProps) {
  return (
    <>
      <ul className={styles.lines}>
        {(sale?.lines ?? []).map((line) => {
          const product = products.find((p) => p.id === line.product_id)
          return (
            <li key={line.id}>
              <span>
                {product?.name ?? `Product #${line.product_id}`} × {line.quantity}
              </span>
              <span className="price">
                <MoneyText value={line.line_total} />
              </span>
            </li>
          )
        })}
      </ul>

      <div className={styles.totals}>
        <div>
          <span>Subtotal</span>
          <MoneyText value={sale?.subtotal ?? '0.00'} />
        </div>
        <div>
          <span>Discount</span>
          <MoneyText value={sale?.discount_total ?? '0.00'} />
        </div>
        <div className={styles.total}>
          <span>Total</span>
          <MoneyText value={sale?.total ?? '0.00'} />
        </div>
      </div>
    </>
  )
}
