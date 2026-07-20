import type { AdminProduct } from '../../../shared/api/types'
import { MoneyText } from '../../../shared/ui/MoneyText'
import styles from './ProductTable.module.css'

type ProductTableProps = {
  products: AdminProduct[]
  loading: boolean
  loadingMore?: boolean
  nextCursor?: string | null
  onLoadMore?: () => void
}

/** Presentational catalog table (dumb). */
export function ProductTable({
  products,
  loading,
  loadingMore = false,
  nextCursor = null,
  onLoadMore,
}: ProductTableProps) {
  if (loading) {
    return <p className={styles.empty}>Loading products…</p>
  }

  if (products.length === 0) {
    return <p className={styles.empty}>No products yet.</p>
  }

  return (
    <div>
      <table>
        <thead>
          <tr>
            <th>SKU</th>
            <th>Name</th>
            <th>Category</th>
            <th>Base price</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          {products.map((product) => (
            <tr key={product.id}>
              <td className={styles.sku}>{product.sku}</td>
              <td>{product.name}</td>
              <td>{product.category_name ?? '—'}</td>
              <td>
                <MoneyText value={product.base_price} />
              </td>
              <td>
                <span
                  className={`${styles.badge} ${product.is_active ? styles.badgeOn : styles.badgeOff}`}
                >
                  {product.is_active ? 'Active' : 'Inactive'}
                </span>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
      {nextCursor && onLoadMore ? (
        <div className={styles.footer}>
          <button type="button" className={styles.loadMore} disabled={loadingMore} onClick={onLoadMore}>
            {loadingMore ? 'Loading…' : 'Load more'}
          </button>
        </div>
      ) : null}
    </div>
  )
}
