import { useAdminUsers } from '../../../features/admin/hooks/useAdminUsers'
import { UsersPanel } from '../../../features/admin/ui/UsersPanel'
import { ErrorBanner } from '../../../shared/ui/ErrorBanner'
import styles from './CatalogPage.module.css'

/** Smart page: admin user CRUD with store assignment (RN-062). */
export function UsersPage() {
  const data = useAdminUsers()

  return (
    <div className={styles.page}>
      <header className={styles.header}>
        <div>
          <h1>Users</h1>
          <p>Manage operators and managers, roles, status, and store access (RN-062).</p>
        </div>
      </header>

      <ErrorBanner message={data.error} />

      <UsersPanel
        users={data.users}
        stores={data.stores}
        search={data.search}
        onSearchChange={data.setSearch}
        onSearch={() => void data.loadUsers(data.search)}
        form={data.form}
        editingUserId={data.editingUserId}
        onFormChange={data.updateForm}
        onToggleStore={data.toggleStore}
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
