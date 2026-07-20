import type { AdminUser, Store } from '../../../shared/api/types'
import type { UserFormState } from '../hooks/useAdminUsers'
import styles from './UsersPanel.module.css'

type UsersPanelProps = {
  users: AdminUser[]
  stores: Store[]
  search: string
  onSearchChange: (value: string) => void
  onSearch: () => void
  form: UserFormState
  editingUserId: number | null
  onFormChange: <K extends keyof UserFormState>(key: K, value: UserFormState[K]) => void
  onToggleStore: (storeId: number) => void
  onStartCreate: () => void
  onStartEdit: (user: AdminUser) => void
  onSave: () => void
  onResetMfa: (user: AdminUser) => void
  currentUserId: number | null
  loading: boolean
  loadingMore?: boolean
  nextCursor?: string | null
  onLoadMore?: () => void
  saving: boolean
  success: string | null
}

/** Presentational admin users panel (dumb). */
export function UsersPanel({
  users,
  stores,
  search,
  onSearchChange,
  onSearch,
  form,
  editingUserId,
  onFormChange,
  onToggleStore,
  onStartCreate,
  onStartEdit,
  onSave,
  onResetMfa,
  currentUserId,
  loading,
  loadingMore = false,
  nextCursor = null,
  onLoadMore,
  saving,
  success,
}: UsersPanelProps) {
  const isEditing = editingUserId !== null

  return (
    <div className={styles.stack}>
      <form
        className={`panel ${styles.toolbar}`}
        onSubmit={(event) => {
          event.preventDefault()
          onSearch()
        }}
      >
        <label>
          Search
          <input
            type="search"
            value={search}
            onChange={(event) => onSearchChange(event.target.value)}
            placeholder="Name or email"
          />
        </label>
        <button type="submit" className="btn" disabled={loading}>
          {loading ? 'Loading…' : 'Search'}
        </button>
        <button type="button" className="btn btn-ghost" onClick={onStartCreate}>
          New user
        </button>
      </form>

      <section className={`panel ${styles.list}`}>
        <h2>Users</h2>
        {users.length === 0 ? (
          <p className={styles.empty}>No users found.</p>
        ) : (
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>MFA</th>
                <th>Stores</th>
                <th />
              </tr>
            </thead>
            <tbody>
              {users.map((user) => (
                <tr key={user.id}>
                  <td className={styles.mono}>#{user.id}</td>
                  <td>{user.name}</td>
                  <td>{user.email}</td>
                  <td>{user.role}</td>
                  <td>
                    <span className={`${styles.badge} ${user.is_active ? styles.active : styles.inactive}`}>
                      {user.is_active ? 'Active' : 'Inactive'}
                    </span>
                  </td>
                  <td>
                    {user.role === 'manager' ? (user.mfa_enabled ? 'On' : 'Off') : '—'}
                  </td>
                  <td>
                    <div className={styles.stores}>
                      {user.stores.length === 0 ? (
                        <span className={styles.empty}>—</span>
                      ) : (
                        user.stores.map((store) => (
                          <span key={store.id} className={styles.storeChip}>
                            {store.code}
                          </span>
                        ))
                      )}
                    </div>
                  </td>
                  <td>
                    <button
                      type="button"
                      className="btn btn-ghost"
                      onClick={() => onStartEdit(user)}
                    >
                      Edit
                    </button>
                    {user.role === 'manager' && user.id !== currentUserId ? (
                      <button
                        type="button"
                        className="btn btn-ghost"
                        disabled={saving}
                        onClick={() => onResetMfa(user)}
                      >
                        Reset MFA
                      </button>
                    ) : null}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
        {nextCursor && onLoadMore ? (
          <div className={styles.footer}>
            <button type="button" className="btn" disabled={loadingMore} onClick={onLoadMore}>
              {loadingMore ? 'Loading…' : 'Load more'}
            </button>
          </div>
        ) : null}
      </section>

      <section className={`panel ${styles.form}`}>
        <h2>{isEditing ? `Edit user #${editingUserId}` : 'Create user'}</h2>
        {success && <p className={styles.success}>{success}</p>}
        <p className={styles.hint}>
          {isEditing
            ? 'Password optional on edit. Managers cannot deactivate or demote themselves.'
            : 'Password required. Assign at least one store.'}
        </p>

        <div className={styles.grid}>
          <label>
            Name
            <input
              value={form.name}
              onChange={(event) => onFormChange('name', event.target.value)}
              required
            />
          </label>
          <label>
            Email
            <input
              type="email"
              value={form.email}
              onChange={(event) => onFormChange('email', event.target.value)}
              required
            />
          </label>
          <label>
            Role
            <select
              value={form.role}
              onChange={(event) =>
                onFormChange('role', event.target.value as UserFormState['role'])
              }
            >
              <option value="operator">Operator</option>
              <option value="manager">Manager</option>
            </select>
          </label>
          <label>
            Status
            <select
              value={form.is_active ? 'active' : 'inactive'}
              onChange={(event) => onFormChange('is_active', event.target.value === 'active')}
            >
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </label>
          <label>
            Password {isEditing ? '(optional)' : ''}
            <input
              type="password"
              autoComplete="new-password"
              value={form.password}
              onChange={(event) => onFormChange('password', event.target.value)}
              required={!isEditing}
              minLength={isEditing ? undefined : 8}
            />
          </label>
          <label>
            Confirm password
            <input
              type="password"
              autoComplete="new-password"
              value={form.password_confirmation}
              onChange={(event) => onFormChange('password_confirmation', event.target.value)}
              required={!isEditing || form.password.length > 0}
            />
          </label>
        </div>

        <div className={styles.checkList}>
          Stores
          <div className={styles.checkRow}>
            {stores.length === 0 ? (
              <span className={styles.empty}>No stores available.</span>
            ) : (
              stores.map((store) => (
                <label key={store.id}>
                  <input
                    type="checkbox"
                    checked={form.store_ids.includes(store.id)}
                    onChange={() => onToggleStore(store.id)}
                  />
                  {store.name} ({store.code})
                </label>
              ))
            )}
          </div>
        </div>

        <div className={styles.actions}>
          <button type="button" className="btn" disabled={saving} onClick={onSave}>
            {saving ? 'Saving…' : isEditing ? 'Save changes' : 'Create user'}
          </button>
          {isEditing && (
            <button type="button" className="btn btn-ghost" onClick={onStartCreate}>
              Cancel edit
            </button>
          )}
        </div>
      </section>
    </div>
  )
}
