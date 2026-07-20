import { useAdminCustomers } from '../../../features/admin/hooks/useAdminCustomers'
import { CustomersPanel } from '../../../features/admin/ui/CustomersPanel'
import { ErrorBanner } from '../../../shared/ui/ErrorBanner'
import styles from './CatalogPage.module.css'

/** Smart page: admin customer CRUD + lifetime spend (RN-030/033). */
export function CustomersPage() {
  const data = useAdminCustomers()

  return (
    <div className={styles.page}>
      <header className={styles.header}>
        <div>
          <h1>Customers</h1>
          <p>Registry with required fields and lifetime spend (RN-030–033).</p>
        </div>
      </header>

      <ErrorBanner message={data.error} />

      <CustomersPanel
        customers={data.customers}
        search={data.search}
        onSearchChange={data.setSearch}
        onSearch={() => void data.load(data.search)}
        form={data.form}
        editingId={data.editingId}
        onFormChange={data.updateForm}
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
