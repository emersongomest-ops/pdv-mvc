import { useCallback, useEffect, useState } from 'react'
import { listAdminSales, listStores } from '../../../shared/api/client'
import type { AdminSaleSummary, AdminSalesFilters, Store } from '../../../shared/api/types'
import { formatApiError } from '../../../shared/session/SessionContext'

export function useAdminSales() {
  const [sales, setSales] = useState<AdminSaleSummary[]>([])
  const [stores, setStores] = useState<Store[]>([])
  const [filters, setFilters] = useState<AdminSalesFilters>({})
  const [draft, setDraft] = useState<AdminSalesFilters>({})
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  const load = useCallback((next: AdminSalesFilters) => {
    setLoading(true)
    setError(null)
    listAdminSales(next)
      .then((response) => setSales(response.data.sales))
      .catch((err) => setError(formatApiError(err)))
      .finally(() => setLoading(false))
  }, [])

  useEffect(() => {
    listStores()
      .then((response) => setStores(response.data.stores))
      .catch((err) => setError(formatApiError(err)))
  }, [])

  useEffect(() => {
    load(filters)
  }, [filters, load])

  const applyFilters = useCallback(() => {
    const next: AdminSalesFilters = {}
    if (draft.from) next.from = draft.from
    if (draft.to) next.to = draft.to
    if (draft.store_id !== undefined && !Number.isNaN(draft.store_id)) {
      next.store_id = draft.store_id
    }
    if (draft.operator_id !== undefined && !Number.isNaN(draft.operator_id)) {
      next.operator_id = draft.operator_id
    }
    if (draft.customer_id !== undefined && !Number.isNaN(draft.customer_id)) {
      next.customer_id = draft.customer_id
    }
    if (draft.payment_method) next.payment_method = draft.payment_method
    setFilters(next)
  }, [draft])

  const clearFilters = useCallback(() => {
    setDraft({})
    setFilters({})
  }, [])

  return {
    sales,
    stores,
    draft,
    setDraft,
    loading,
    error,
    applyFilters,
    clearFilters,
  }
}
