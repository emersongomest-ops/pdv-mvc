import { useCallback, useEffect, useState } from 'react'
import { listAdminProducts } from '../../../shared/api/client'
import type { AdminProduct } from '../../../shared/api/types'
import { formatApiError } from '../../../shared/session/SessionContext'

const PAGE_SIZE = 40

export function useAdminProducts() {
  const [products, setProducts] = useState<AdminProduct[]>([])
  const [error, setError] = useState<string | null>(null)
  const [loading, setLoading] = useState(true)
  const [loadingMore, setLoadingMore] = useState(false)
  const [nextCursor, setNextCursor] = useState<string | null>(null)

  const loadPage = useCallback(async (opts: { cursor?: string | null; append: boolean }) => {
    const response = await listAdminProducts({
      cursor: opts.cursor || undefined,
      per_page: PAGE_SIZE,
    })
    setProducts((current) =>
      opts.append ? [...current, ...response.data.products] : response.data.products,
    )
    setNextCursor(response.meta?.next_cursor ?? null)
  }, [])

  useEffect(() => {
    setLoading(true)
    loadPage({ append: false })
      .catch((err) => setError(formatApiError(err)))
      .finally(() => setLoading(false))
  }, [loadPage])

  const loadMore = useCallback(async () => {
    if (!nextCursor || loadingMore) return
    setLoadingMore(true)
    setError(null)
    try {
      await loadPage({ cursor: nextCursor, append: true })
    } catch (err) {
      setError(formatApiError(err))
    } finally {
      setLoadingMore(false)
    }
  }, [nextCursor, loadingMore, loadPage])

  return { products, error, loading, loadingMore, nextCursor, loadMore }
}
