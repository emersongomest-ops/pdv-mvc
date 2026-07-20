import { useAdminInventory } from '../../../features/admin/hooks/useAdminInventory'
import { InventoryPanel } from '../../../features/admin/ui/InventoryPanel'
import { ErrorBanner } from '../../../shared/ui/ErrorBanner'
import styles from './CatalogPage.module.css'

/** Smart page: store stock list + audited absolute adjust (RN-023). */
export function InventoryPage() {
  const data = useAdminInventory()

  return (
    <div className={styles.page}>
      <header className={styles.header}>
        <div>
          <h1>Inventory</h1>
          <p>Per-store stock levels and audited adjustments (RN-023 / RN-064).</p>
        </div>
      </header>

      <ErrorBanner message={data.error} />

      <InventoryPanel
        stores={data.stores}
        products={data.products}
        storeId={data.storeId}
        onStoreChange={data.setStoreId}
        rows={data.rows}
        form={data.form}
        onFormChange={data.updateForm}
        onSave={() => void data.save()}
        loading={data.loading}
        saving={data.saving}
        success={data.success}
      />
    </div>
  )
}
