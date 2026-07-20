import { Link } from 'react-router-dom'
import type { AdminNotification } from '../../../shared/api/types'
import { ErrorBanner } from '../../../shared/ui/ErrorBanner'
import styles from './DashboardView.module.css'

type DashboardViewProps = {
  message: string | null
  productCount: number | null
  activeCount: number | null
  inactive: number | null
  customersTotal: number | null
  salesCompleted: number | null
  openShifts: number | null
  notifications: AdminNotification[]
  error: string | null
}

function formatActivity(notification: AdminNotification) {
  const data = notification.data
  if (data.kind === 'sale.completed' && data.sale_id !== undefined) {
    return {
      title: `Sale #${data.sale_id} completed`,
      subtitle: data.total ? `Total R$ ${data.total}` : 'Sale completed',
      status: notification.read_at ? 'read' : 'new',
      href: `/admin/refunds?sale_id=${data.sale_id}`,
    }
  }

  return {
    title: data.message ?? 'Notification',
    subtitle: notification.kind ?? 'admin',
    status: notification.read_at ? 'read' : 'new',
    href: null as string | null,
  }
}

/** Presentational admin dashboard (dumb). */
export function DashboardView({
  message,
  productCount,
  activeCount,
  inactive,
  customersTotal,
  salesCompleted,
  openShifts,
  notifications,
  error,
}: DashboardViewProps) {
  const activities =
    notifications.length > 0
      ? notifications.map(formatActivity)
      : [
          {
            title: 'No notifications yet',
            subtitle: 'Completed sales notify store managers',
            status: 'idle',
            href: null as string | null,
          },
        ]
  return (
    <div className={styles.page}>
      <header className={styles.header}>
        <h1>Dashboard</h1>
        <p>Manager overview — store ops at a glance.</p>
      </header>

      <ErrorBanner message={error} />

      <section className={styles.kpis} aria-label="Key metrics">
        <article className={`panel ${styles.kpi}`}>
          <span>Access</span>
          <strong>Admin</strong>
          <em>{message ?? 'Loading…'}</em>
        </article>
        <article className={`panel ${styles.kpi}`}>
          <span>Products</span>
          <strong>{productCount ?? '—'}</strong>
          <em>{activeCount !== null ? `${activeCount} active` : 'Loading…'}</em>
        </article>
        <article className={`panel ${styles.kpi}`}>
          <span>Inactive</span>
          <strong>{inactive ?? '—'}</strong>
          <em>Catalog</em>
        </article>
        <article className={`panel ${styles.kpi}`}>
          <span>Customers</span>
          <strong>{customersTotal ?? '—'}</strong>
          <em>Your stores</em>
        </article>
        <article className={`panel ${styles.kpi}`}>
          <span>Sales completed</span>
          <strong>{salesCompleted ?? '—'}</strong>
          <em>Your stores</em>
        </article>
        <article className={`panel ${styles.kpi}`}>
          <span>Open shifts</span>
          <strong>{openShifts ?? '—'}</strong>
          <em>Your stores</em>
        </article>
      </section>

      <section className={styles.lower}>
        <div className={`panel ${styles.panel}`}>
          <h2>Quick links</h2>
          <div className={styles.links}>
            <Link to="/admin/analytics">
              Analytics & campaigns <span>→</span>
            </Link>
            <Link to="/admin/catalog">
              Catalog <span>→</span>
            </Link>
            <Link to="/admin/sales">
              Sales filters <span>→</span>
            </Link>
            <Link to="/admin/shifts">
              Shift reports <span>→</span>
            </Link>
            <Link to="/admin/users">
              Users <span>→</span>
            </Link>
            <Link to="/admin/inventory">
              Inventory adjustments <span>→</span>
            </Link>
            <Link to="/admin/promotions">
              Promotions <span>→</span>
            </Link>
            <Link to="/admin/refunds">
              Refunds <span>→</span>
            </Link>
            <Link to="/admin/audit-log">
              Audit log <span>→</span>
            </Link>
          </div>
        </div>

        <div className={`panel ${styles.panel}`}>
          <h2>Recent activity</h2>
          <ul className={styles.activity}>
            {activities.map((item, index) => (
              <li key={`${item.title}-${index}`}>
                <div>
                  <strong>
                    {item.href ? <Link to={item.href}>{item.title}</Link> : item.title}
                  </strong>
                  <span>{item.subtitle}</span>
                </div>
                <em>{item.status}</em>
              </li>
            ))}
          </ul>
        </div>
      </section>
    </div>
  )
}
