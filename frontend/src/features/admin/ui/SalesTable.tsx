import { Link } from 'react-router-dom'
import type { AdminSaleSummary } from '../../../shared/api/types'
import { MoneyText } from '../../../shared/ui/MoneyText'
import styles from './SalesTable.module.css'

type SalesTableProps = {
  sales: AdminSaleSummary[]
  loading: boolean
}

function formatWhen(value: string | null): string {
  if (!value) return '—'
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value
  return date.toLocaleString()
}

/** Presentational admin sales table (dumb). */
export function SalesTable({ sales, loading }: SalesTableProps) {
  if (loading) {
    return <p className={styles.empty}>Loading sales…</p>
  }

  if (sales.length === 0) {
    return <p className={styles.empty}>No sales match the current filters.</p>
  }

  return (
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Completed</th>
          <th>Store</th>
          <th>Operator</th>
          <th>Customer</th>
          <th>Payments</th>
          <th>Total</th>
          <th />
        </tr>
      </thead>
      <tbody>
        {sales.map((sale) => (
          <tr key={sale.id}>
            <td className={styles.id}>#{sale.id}</td>
            <td>{formatWhen(sale.completed_at)}</td>
            <td>{sale.store_code ?? sale.store_id}</td>
            <td>{sale.operator_name ?? sale.operator_id}</td>
            <td>{sale.customer_name ?? (sale.customer_id ? `#${sale.customer_id}` : 'Walk-in')}</td>
            <td>{sale.payment_methods.join(', ') || '—'}</td>
            <td>
              <MoneyText value={sale.total} />
            </td>
            <td>
              <Link to={`/admin/refunds?sale_id=${sale.id}`}>Refund</Link>
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  )
}
