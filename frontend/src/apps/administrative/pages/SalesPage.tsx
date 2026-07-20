import { useState } from 'react'
import { reconcileAdminPayments } from '../../../shared/api/client'
import { useAdminSales } from '../../../features/admin/hooks/useAdminSales'
import { SalesFilters } from '../../../features/admin/ui/SalesFilters'
import { SalesTable } from '../../../features/admin/ui/SalesTable'
import { formatApiError } from '../../../shared/session/SessionContext'
import { ErrorBanner } from '../../../shared/ui/ErrorBanner'
import styles from './CatalogPage.module.css'

/** Smart page: admin sales filters + table (RN-061). */
export function SalesPage() {
  const { sales, stores, draft, setDraft, loading, error, applyFilters, clearFilters } =
    useAdminSales()
  const [reconciling, setReconciling] = useState(false)
  const [reconcileError, setReconcileError] = useState<string | null>(null)
  const [reconcileMsg, setReconcileMsg] = useState<string | null>(null)

  async function handleReconcile() {
    setReconciling(true)
    setReconcileError(null)
    setReconcileMsg(null)
    try {
      const { data } = await reconcileAdminPayments()
      setReconcileMsg(
        `Payments updated: confirmed ${data.settlements_confirmed}, failed ${data.settlements_failed}, pending ${data.still_pending}, webhook retries ${data.webhook_retries_succeeded}.`,
      )
      applyFilters()
    } catch (err) {
      setReconcileError(formatApiError(err))
    } finally {
      setReconciling(false)
    }
  }

  return (
    <div className={styles.page}>
      <header className={styles.header}>
        <div>
          <h1>Sales</h1>
          <p>
            Filter completed sales by period, assigned store, operator, customer, payment
            (RN-061/064).
          </p>
        </div>
        <button
          type="button"
          className="btn"
          onClick={() => void handleReconcile()}
          disabled={reconciling}
        >
          {reconciling ? 'Updating…' : 'Refresh payments'}
        </button>
      </header>

      <ErrorBanner message={reconcileError ?? error} />
      {reconcileMsg ? <p className={styles.hint}>{reconcileMsg}</p> : null}

      <SalesFilters
        draft={draft}
        stores={stores}
        onChange={setDraft}
        onApply={applyFilters}
        onClear={clearFilters}
      />

      <div className={`panel ${styles.tableWrap}`}>
        <SalesTable sales={sales} loading={loading} />
      </div>
    </div>
  )
}
