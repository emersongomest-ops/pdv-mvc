import { useAdminRefunds } from '../../../features/admin/hooks/useAdminRefunds'
import { RefundsPanel } from '../../../features/admin/ui/RefundsPanel'
import { ErrorBanner } from '../../../shared/ui/ErrorBanner'
import styles from './CatalogPage.module.css'

/** Smart page: admin refunds / returns by sale (RN-016–019a). */
export function RefundsPage() {
  const data = useAdminRefunds()

  return (
    <div className={styles.page}>
      <header className={styles.header}>
        <div>
          <h1>Refunds</h1>
          <p>
            Manager refunds and returns with audit reason. Only sales from your assigned stores
            (RN-016–019a / RN-064).
          </p>
        </div>
      </header>

      <ErrorBanner message={data.error} />

      <RefundsPanel
        saleIdInput={data.saleIdInput}
        onSaleIdChange={data.setSaleIdInput}
        onLookup={data.lookup}
        sale={data.sale}
        refunds={data.refunds}
        lineSelections={data.lineSelections}
        onLineChange={data.updateLine}
        type={data.type}
        onTypeChange={data.setType}
        reason={data.reason}
        onReasonChange={data.setReason}
        needsLines={data.needsLines}
        loading={data.loading}
        submitting={data.submitting}
        success={data.success}
        onSubmit={() => void data.submit()}
      />
    </div>
  )
}
