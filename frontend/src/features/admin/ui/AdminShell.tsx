import { NavLink, Outlet } from 'react-router-dom'
import { useSession } from '../../../shared/session/SessionContext'
import styles from './AdminShell.module.css'

const links = [
  { to: '/admin', end: true, label: 'Dashboard' },
  { to: '/admin/analytics', label: 'Analytics' },
  { to: '/admin/catalog', label: 'Catalog' },
  { to: '/admin/sales', label: 'Sales' },
  { to: '/admin/shifts', label: 'Shifts' },
  { to: '/admin/users', label: 'Users' },
  { to: '/admin/customers', label: 'Customers' },
  { to: '/admin/promotions', label: 'Promotions' },
  { to: '/admin/inventory', label: 'Inventory' },
  { to: '/admin/refunds', label: 'Refunds' },
  { to: '/admin/audit-log', label: 'Audit log' },
]

export function AdminShell() {
  const { user, logout } = useSession()

  return (
    <div className={styles.shell}>
      <aside className={styles.sidebar}>
        <p className={styles.brand}>PDV Admin</p>
        <nav className={styles.nav} aria-label="Administrative">
          {links.map((link) => (
            <NavLink
              key={link.to}
              to={link.to}
              end={link.end}
              className={({ isActive }) =>
                isActive ? `${styles.link} ${styles.active}` : styles.link
              }
            >
              {link.label}
            </NavLink>
          ))}
        </nav>
        <div className={styles.footer}>
          <p className={styles.user}>
            {user?.name} · {user?.role}
          </p>
          <NavLink to="/store">Open POS</NavLink>
          <button type="button" className="btn btn-ghost" onClick={() => void logout()}>
            Sign out
          </button>
        </div>
      </aside>
      <main className={styles.main}>
        <Outlet />
      </main>
    </div>
  )
}
