import { useAdminProducts } from '../../../features/catalog/hooks/useAdminProducts'
import { ProductTable } from '../../../features/catalog/ui/ProductTable'
import { ErrorBanner } from '../../../shared/ui/ErrorBanner'
import styles from './CatalogPage.module.css'

/** Smart page: loads admin catalog → dumb table. */
export function CatalogPage() {
  const { products, error, loading, loadingMore, nextCursor, loadMore } = useAdminProducts()

  return (
    <div className={styles.page}>
      <header className={styles.header}>
        <div>
          <h1>Catalog</h1>
          <p>Admin products — base price (RN-040). API decimal · DB cents.</p>
        </div>
      </header>

      <ErrorBanner message={error} />

      <div className={`panel ${styles.tableWrap}`}>
        <ProductTable
          products={products}
          loading={loading}
          loadingMore={loadingMore}
          nextCursor={nextCursor}
          onLoadMore={() => void loadMore()}
        />
      </div>
    </div>
  )
}
