import { useCallback, useEffect, useState } from 'react'
import { listAdminAuditLogs, listStores } from '../../../shared/api/client'
import type { AdminAuditFilters, AuditLogEntry, Store } from '../../../shared/api/types'
import { formatApiError } from '../../../shared/session/SessionContext'

export function useAdminAuditLogs() {
  const [entries, setEntries] = useState<AuditLogEntry[]>([])
  const [stores, setStores] = useState<Store[]>([])
  const [filters, setFilters] = useState<AdminAuditFilters>({ per_page: 50 })
  const [draft, setDraft] = useState<AdminAuditFilters>({})
  const [nextCursor, setNextCursor] = useState<string | null>(null)
  const [loading, setLoading] = useState(true)
  const [loadingMore, setLoadingMore] = useState(false)
  const [error, setError] = useState<string | null>(null)

  const load = useCallback(async (next: AdminAuditFilters, append = false) => {
    if (append) {
      setLoadingMore(true)
    } else {
      setLoading(true)
    }
    setError(null)
    try {
      const response = await listAdminAuditLogs(next)
      setEntries((current) =>
        append ? [...current, ...response.data.audit_logs] : response.data.audit_logs,
      )
      setNextCursor(response.meta.next_cursor)
    } catch (err) {
      if (!append) {
        setEntries([])
      }
      setError(formatApiError(err))
    } finally {
      setLoading(false)
      setLoadingMore(false)
    }
  }, [])

  useEffect(() => {
    listStores()
      .then((response) => setStores(response.data.stores))
      .catch((err) => setError(formatApiError(err)))
  }, [])

  useEffect(() => {
    void load(filters)
  }, [filters, load])

  const applyFilters = useCallback(() => {
    const next: AdminAuditFilters = { per_page: 50 }
    if (draft.from) next.from = draft.from
    if (draft.to) next.to = draft.to
    if (draft.action) next.action = draft.action
    if (draft.actor_id !== undefined && !Number.isNaN(draft.actor_id)) {
      next.actor_id = draft.actor_id
    }
    if (draft.store_id !== undefined && !Number.isNaN(draft.store_id)) {
      next.store_id = draft.store_id
    }
    if (draft.subject_type) next.subject_type = draft.subject_type
    if (draft.subject_id !== undefined && !Number.isNaN(draft.subject_id)) {
      next.subject_id = draft.subject_id
    }
    setFilters(next)
  }, [draft])

  const clearFilters = useCallback(() => {
    setDraft({})
    setFilters({ per_page: 50 })
  }, [])

  const loadMore = useCallback(() => {
    if (!nextCursor) return
    void load({ ...filters, cursor: nextCursor }, true)
  }, [filters, load, nextCursor])

  return {
    entries,
    stores,
    draft,
    setDraft,
    loading,
    loadingMore,
    error,
    nextCursor,
    applyFilters,
    clearFilters,
    loadMore,
  }
}
