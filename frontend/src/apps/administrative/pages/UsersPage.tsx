import { useAdminUsers } from '../../../features/admin/hooks/useAdminUsers'
import { UsersPanel } from '../../../features/admin/ui/UsersPanel'
import { useSession } from '../../../shared/session/SessionContext'
import { ErrorBanner } from '../../../shared/ui/ErrorBanner'
import styles from './CatalogPage.module.css'

/** Smart page: admin user CRUD with store assignment (RN-062) + MFA reset (RN-074). */
export function UsersPage() {
  const data = useAdminUsers()
  const { user } = useSession()

  return (
    <div className={styles.page}>
      <header className={styles.header}>
        <div>
          <h1>Users</h1>
          <p>Manage operators and managers, roles, status, store access, and MFA reset (RN-062 / RN-074).</p>
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
        onResetMfa={(target) => void data.resetMfa(target)}
        currentUserId={user?.id ?? null}
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
