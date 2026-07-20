import { useCallback, useEffect, useState } from 'react'
import { listOperationalProducts } from '../../../shared/api/client'
import { formatApiError } from '../../../shared/session/SessionContext'
import {
  usePosActivity,
  usePosCatalogState,
} from '../context/PosWorkspaceState'
import { usePosSale } from './usePosSale'

const PAGE_SIZE = 40

/** SRP: operational catalog pages + debounced search. */
export function usePosCatalog(shiftOpen: boolean) {
  const { products, setProducts } = usePosCatalogState()
  const { setError, busy } = usePosActivity()
  const { addProduct } = usePosSale()
  const [search, setSearch] = useState('')
  const [nextCursor, setNextCursor] = useState<string | null>(null)
  const [loadingMore, setLoadingMore] = useState(false)

  const loadPage = useCallback(
    async (opts: { search: string; cursor?: string | null; append: boolean }) => {
      const response = await listOperationalProducts({
        search: opts.search.trim() || undefined,
        cursor: opts.cursor || undefined,
        per_page: PAGE_SIZE,
      })
      setProducts((current) =>
        opts.append ? [...current, ...response.data.products] : response.data.products,
      )
      setNextCursor(response.meta.next_cursor)
    },
    [setProducts],
  )

  useEffect(() => {
    if (!shiftOpen) return

    const handle = window.setTimeout(() => {
      loadPage({ search, append: false })
        .catch((err) => setError(formatApiError(err)))
    }, 200)

    return () => window.clearTimeout(handle)
  }, [shiftOpen, search, loadPage, setError])

  const loadMore = useCallback(async () => {
    if (!nextCursor || loadingMore) return
    setLoadingMore(true)
    setError(null)
    try {
      await loadPage({ search, cursor: nextCursor, append: true })
    } catch (err) {
      setError(formatApiError(err))
    } finally {
      setLoadingMore(false)
    }
  }, [nextCursor, loadingMore, loadPage, search, setError])

  return {
    products,
    search,
    setSearch,
    busy,
    addProduct,
    nextCursor,
    loadingMore,
    loadMore,
  }
}
