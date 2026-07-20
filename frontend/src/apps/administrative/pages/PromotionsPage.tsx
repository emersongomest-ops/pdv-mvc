import { useAdminPromotions } from '../../../features/admin/hooks/useAdminPromotions'
import { PromotionsPanel } from '../../../features/admin/ui/PromotionsPanel'
import { ErrorBanner } from '../../../shared/ui/ErrorBanner'
import styles from './CatalogPage.module.css'

/** Smart page: manager promotions unique/accumulable (RN-044/046). */
export function PromotionsPage() {
  const data = useAdminPromotions()

  return (
    <div className={styles.page}>
      <header className={styles.header}>
        <div>
          <h1>Promotions</h1>
          <p>Create and assign coupons — unique or accumulable (RN-044/046).</p>
        </div>
      </header>

      <ErrorBanner message={data.error} />

      <PromotionsPanel
        promotions={data.promotions}
        customers={data.customers}
        form={data.form}
        editingId={data.editingId}
        onFormChange={data.updateForm}
        onToggleCustomer={data.toggleCustomer}
        onStartCreate={data.startCreate}
        onStartEdit={data.startEdit}
        onSave={() => void data.save()}
        loading={data.loading}
        loadingMore={data.loadingMore}
        nextCursor={data.nextCursor}
        onLoadMore={() => void data.loadMore()}
        saving={data.saving}
        success={data.success}
      />
    </div>
  )
}
