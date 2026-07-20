import type { Product, Sale } from '../../../shared/api/types'
import { MoneyText } from '../../../shared/ui/MoneyText'
import styles from './CatalogPanel.module.css'

type CatalogPanelProps = {
  products: Product[]
  search: string
  heldSales: Sale[]
  busy: boolean
  hasMore: boolean
  loadingMore: boolean
  onSearchChange: (value: string) => void
  onAddProduct: (product: Product) => void
  onResumeHeld: (sale: Sale) => void
  onLoadMore: () => void
}

/** Dumb: product list + held carts. */
export function CatalogPanel({
  products,
  search,
  heldSales,
  busy,
  hasMore,
  loadingMore,
  onSearchChange,
  onAddProduct,
  onResumeHeld,
  onLoadMore,
}: CatalogPanelProps) {
  return (
    <section className={`panel ${styles.catalog}`}>
      <div className={styles.catalogHead}>
        <h2>Products</h2>
        <input
          placeholder="Search name or SKU"
          value={search}
          onChange={(e) => onSearchChange(e.target.value)}
        />
      </div>
      <div className={styles.productList}>
        {products.map((product) => (
          <button
            key={product.id}
            type="button"
            className={styles.product}
            disabled={busy}
            onClick={() => onAddProduct(product)}
          >
            <span>
              <strong>{product.name}</strong>
              <small>{product.sku}</small>
            </span>
            <span className="price">
              <MoneyText value={product.base_price} />
            </span>
          </button>
        ))}
      </div>
      {hasMore ? (
        <button
          type="button"
          className="btn btn-ghost"
          disabled={busy || loadingMore}
          onClick={onLoadMore}
        >
          {loadingMore ? 'Loading…' : 'Load more'}
        </button>
      ) : null}

      {heldSales.length > 0 ? (
        <div className={styles.held}>
          <h3>Held carts</h3>
          <ul>
            {heldSales.map((held) => (
              <li key={held.id}>
                <span>
                  #{held.id}
                  {held.hold_label ? ` · ${held.hold_label}` : ''} ·{' '}
                  <MoneyText value={held.total} />
                </span>
                <button
                  type="button"
                  className="btn btn-ghost"
                  disabled={busy}
                  onClick={() => onResumeHeld(held)}
                >
                  Resume
                </button>
              </li>
            ))}
          </ul>
        </div>
      ) : null}
    </section>
  )
}
