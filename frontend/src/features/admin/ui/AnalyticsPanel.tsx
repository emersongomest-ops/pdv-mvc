import type { AdminAnalytics, Customer } from '../../../shared/api/types'
import styles from './UsersPanel.module.css'

type AnalyticsPanelProps = {
  analytics: AdminAnalytics | null
  campaignCustomers: Customer[]
  birthMonth: string
  region: string
  onBirthMonthChange: (value: string) => void
  onRegionChange: (value: string) => void
  onRunCampaignFilter: () => void
  loading: boolean
  campaignLoading: boolean
}

export function AnalyticsPanel({
  analytics,
  campaignCustomers,
  birthMonth,
  region,
  onBirthMonthChange,
  onRegionChange,
  onRunCampaignFilter,
  loading,
  campaignLoading,
}: AnalyticsPanelProps) {
  const recentRegistrations =
    analytics?.registrations_over_time.filter((bucket) => bucket.count > 0).slice(-14) ?? []

  return (
    <div className={styles.stack}>
      <section className={`panel ${styles.list}`}>
        <h2>Recurrence (RN-081)</h2>
        {loading || !analytics ? (
          <p className={styles.empty}>Loading…</p>
        ) : (
          <p>
            Index <strong className={styles.mono}>{analytics.recurrence.index}</strong>
            {' · '}
            {analytics.recurrence.customers_with_repeat} repeat /{' '}
            {analytics.recurrence.customers_with_purchases} with purchases (assigned stores)
          </p>
        )}
      </section>

      <section className={`panel ${styles.list}`}>
        <h2>New registrations (RN-080)</h2>
        {loading || !analytics ? (
          <p className={styles.empty}>Loading…</p>
        ) : recentRegistrations.length === 0 ? (
          <p className={styles.empty}>No registrations in the last 30 days.</p>
        ) : (
          <table>
            <thead>
              <tr>
                <th>Date</th>
                <th>Count</th>
              </tr>
            </thead>
            <tbody>
              {recentRegistrations.map((bucket) => (
                <tr key={bucket.date}>
                  <td className={styles.mono}>{bucket.date}</td>
                  <td className={styles.mono}>{bucket.count}</td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </section>

      <section className={`panel ${styles.list}`}>
        <h2>Spend per customer / store (RN-082)</h2>
        {loading || !analytics ? (
          <p className={styles.empty}>Loading…</p>
        ) : analytics.top_customers_by_spend.length === 0 ? (
          <p className={styles.empty}>No purchase stats for assigned stores.</p>
        ) : (
          <table>
            <thead>
              <tr>
                <th>Customer</th>
                <th>Lifetime</th>
                <th>Per store</th>
              </tr>
            </thead>
            <tbody>
              {analytics.top_customers_by_spend.map((row) => (
                <tr key={row.customer_id}>
                  <td>
                    {row.name}
                    <div className={styles.hint}>{row.cpf}</div>
                  </td>
                  <td className={styles.mono}>{row.lifetime_spend}</td>
                  <td>
                    <div className={styles.stores}>
                      {row.store_spend.map((store) => (
                        <span key={store.store_id} className={styles.storeChip}>
                          {store.store_code ?? store.store_id}: {store.total_spend} ({store.purchase_count}x)
                        </span>
                      ))}
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </section>

      <section className={`panel ${styles.form}`}>
        <h2>Campaign filters (RN-083 / RN-084)</h2>
        <form
          className={styles.grid}
          onSubmit={(event) => {
            event.preventDefault()
            onRunCampaignFilter()
          }}
        >
          <label>
            Birth month
            <select value={birthMonth} onChange={(e) => onBirthMonthChange(e.target.value)}>
              <option value="">Any</option>
              {Array.from({ length: 12 }, (_, index) => (
                <option key={index + 1} value={String(index + 1)}>
                  {index + 1}
                </option>
              ))}
            </select>
          </label>
          <label>
            Region (address contains)
            <input
              value={region}
              onChange={(e) => onRegionChange(e.target.value)}
              placeholder="e.g. SP, Rio, Centro"
            />
          </label>
          <button type="submit" className="btn" disabled={campaignLoading}>
            {campaignLoading ? 'Filtering…' : 'Filter customers'}
          </button>
        </form>
        {campaignCustomers.length === 0 ? (
          <p className={styles.empty}>Run a filter to list campaign targets.</p>
        ) : (
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Birth</th>
                <th>Address</th>
              </tr>
            </thead>
            <tbody>
              {campaignCustomers.map((customer) => (
                <tr key={customer.id}>
                  <td>{customer.name}</td>
                  <td className={styles.mono}>{customer.birth_date ?? '—'}</td>
                  <td>{customer.address ?? '—'}</td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </section>
    </div>
  )
}
