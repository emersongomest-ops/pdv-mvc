import type { Customer } from '../../../shared/api/types'
import type { CustomerFormState } from '../hooks/useAdminCustomers'
import styles from './UsersPanel.module.css'

type CustomersPanelProps = {
  customers: Customer[]
  search: string
  onSearchChange: (value: string) => void
  onSearch: () => void
  form: CustomerFormState
  editingId: number | null
  onFormChange: <K extends keyof CustomerFormState>(key: K, value: CustomerFormState[K]) => void
  onStartCreate: () => void
  onStartEdit: (customer: Customer) => void
  onSave: () => void
  loading: boolean
  loadingMore?: boolean
  nextCursor?: string | null
  onLoadMore?: () => void
  saving: boolean
  success: string | null
}

export function CustomersPanel({
  customers,
  search,
  onSearchChange,
  onSearch,
  form,
  editingId,
  onFormChange,
  onStartCreate,
  onStartEdit,
  onSave,
  loading,
  loadingMore = false,
  nextCursor = null,
  onLoadMore,
  saving,
  success,
}: CustomersPanelProps) {
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
            placeholder="Name, email or CPF"
          />
        </label>
        <button type="submit" className="btn" disabled={loading}>
          {loading ? 'Loading…' : 'Search'}
        </button>
        <button type="button" className="btn btn-ghost" onClick={onStartCreate}>
          New customer
        </button>
      </form>

      <section className={`panel ${styles.list}`}>
        <h2>Customers</h2>
        {customers.length === 0 ? (
          <p className={styles.empty}>No customers found.</p>
        ) : (
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>CPF</th>
                <th>Email</th>
                <th>Lifetime spend</th>
                <th />
              </tr>
            </thead>
            <tbody>
              {customers.map((customer) => (
                <tr key={customer.id}>
                  <td className={styles.mono}>#{customer.id}</td>
                  <td>{customer.name}</td>
                  <td className={styles.mono}>{customer.cpf}</td>
                  <td>{customer.email}</td>
                  <td className={styles.mono}>{customer.lifetime_spend}</td>
                  <td>
                    <button type="button" className="btn btn-ghost" onClick={() => onStartEdit(customer)}>
                      Edit
                    </button>
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
        <h2>{editingId === null ? 'Create customer' : `Edit customer #${editingId}`}</h2>
        {success ? <p className={styles.success}>{success}</p> : null}
        <div className={styles.grid}>
          <label>
            Name
            <input value={form.name} onChange={(e) => onFormChange('name', e.target.value)} required />
          </label>
          <label>
            Email
            <input
              type="email"
              value={form.email}
              onChange={(e) => onFormChange('email', e.target.value)}
              required
            />
          </label>
          <label>
            CPF
            <input value={form.cpf} onChange={(e) => onFormChange('cpf', e.target.value)} required />
          </label>
          <label>
            Phone
            <input value={form.phone} onChange={(e) => onFormChange('phone', e.target.value)} required />
          </label>
          <label>
            Birth date
            <input
              type="date"
              value={form.birth_date}
              onChange={(e) => onFormChange('birth_date', e.target.value)}
              required
            />
          </label>
          <label>
            Address
            <input
              value={form.address}
              onChange={(e) => onFormChange('address', e.target.value)}
              required
            />
          </label>
        </div>
        <button type="button" className="btn" disabled={saving} onClick={onSave}>
          {saving ? 'Saving…' : editingId === null ? 'Create' : 'Save'}
        </button>
      </section>
    </div>
  )
}
