import { Fragment, useState } from 'react'
import type { AuditLogEntry } from '../../../shared/api/types'
import styles from './AuditLogTable.module.css'

type AuditLogTableProps = {
  entries: AuditLogEntry[]
  loading: boolean
  loadingMore: boolean
  nextCursor: string | null
  onLoadMore: () => void
}

function formatWhen(value: string | null): string {
  if (!value) return '—'
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value
  return date.toLocaleString()
}

function summarize(entry: AuditLogEntry): string {
  const reason =
    typeof entry.metadata?.reason === 'string'
      ? entry.metadata.reason
      : typeof entry.new_values?.reason === 'string'
        ? entry.new_values.reason
        : null

  if (entry.action === 'catalog.product.price_changed') {
    return `${entry.old_values?.base_price ?? '—'} → ${entry.new_values?.base_price ?? '—'}`
  }
  if (entry.action === 'inventory.stock_adjusted') {
    return `qty ${entry.old_values?.quantity ?? '—'} → ${entry.new_values?.quantity ?? '—'}${
      reason ? ` · ${reason}` : ''
    }`
  }
  if (entry.action === 'refund.created' || entry.action === 'return.created') {
    return `${entry.new_values?.amount ?? '—'} · ${reason ?? entry.new_values?.type ?? ''}`
  }
  if (entry.action.startsWith('promotion.')) {
    return String(entry.new_values?.code ?? entry.subject_id)
  }
  return reason ?? '—'
}

/** Presentational audit log table (dumb). */
export function AuditLogTable({
  entries,
  loading,
  loadingMore,
  nextCursor,
  onLoadMore,
}: AuditLogTableProps) {
  const [openId, setOpenId] = useState<number | null>(null)

  if (loading) {
    return <p className={styles.empty}>Loading audit log…</p>
  }

  if (entries.length === 0) {
    return <p className={styles.empty}>No audit entries match the current filters.</p>
  }

  return (
    <div className={styles.wrap}>
      <table>
        <thead>
          <tr>
            <th>When</th>
            <th>Action</th>
            <th>Actor</th>
            <th>Store</th>
            <th>Subject</th>
            <th>Summary</th>
            <th />
          </tr>
        </thead>
        <tbody>
          {entries.map((entry) => (
            <Fragment key={entry.id}>
              <tr>
                <td>{formatWhen(entry.occurred_at)}</td>
                <td className={styles.mono}>{entry.action}</td>
                <td>{entry.actor?.name ?? '—'}</td>
                <td>{entry.store ? entry.store.code : 'Global'}</td>
                <td className={styles.mono}>
                  {entry.subject_type}#{entry.subject_id}
                </td>
                <td>{summarize(entry)}</td>
                <td>
                  <button
                    type="button"
                    className="btn btn-ghost"
                    onClick={() => setOpenId(openId === entry.id ? null : entry.id)}
                  >
                    {openId === entry.id ? 'Hide' : 'Details'}
                  </button>
                </td>
              </tr>
              {openId === entry.id && (
                <tr className={styles.detailsRow}>
                  <td colSpan={7}>
                    <pre className={styles.details}>
                      {JSON.stringify(
                        {
                          old_values: entry.old_values,
                          new_values: entry.new_values,
                          metadata: entry.metadata,
                        },
                        null,
                        2,
                      )}
                    </pre>
                  </td>
                </tr>
              )}
            </Fragment>
          ))}
        </tbody>
      </table>

      {nextCursor && (
        <div className={styles.more}>
          <button type="button" className="btn" disabled={loadingMore} onClick={onLoadMore}>
            {loadingMore ? 'Loading…' : 'Load more'}
          </button>
        </div>
      )}
    </div>
  )
}
