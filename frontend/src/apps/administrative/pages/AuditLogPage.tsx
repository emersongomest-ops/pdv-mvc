import { useAdminAuditLogs } from '../../../features/admin/hooks/useAdminAuditLogs'
import { AuditLogFilters } from '../../../features/admin/ui/AuditLogFilters'
import { AuditLogTable } from '../../../features/admin/ui/AuditLogTable'
import { ErrorBanner } from '../../../shared/ui/ErrorBanner'
import styles from './CatalogPage.module.css'

/** Smart page: immutable audit trail with filters (RN-070). */
export function AuditLogPage() {
  const data = useAdminAuditLogs()

  return (
    <div className={styles.page}>
      <header className={styles.header}>
        <div>
          <h1>Audit log</h1>
          <p>
            Sensitive actions: price changes, stock adjusts, refunds/returns, promotion management
            (RN-070).
          </p>
        </div>
      </header>

      <ErrorBanner message={data.error} />

      <AuditLogFilters
        draft={data.draft}
        stores={data.stores}
        onChange={data.setDraft}
        onApply={data.applyFilters}
        onClear={data.clearFilters}
      />

      <div className={`panel ${styles.tableWrap}`}>
        <AuditLogTable
          entries={data.entries}
          loading={data.loading}
          loadingMore={data.loadingMore}
          nextCursor={data.nextCursor}
          onLoadMore={data.loadMore}
        />
      </div>
    </div>
  )
}
