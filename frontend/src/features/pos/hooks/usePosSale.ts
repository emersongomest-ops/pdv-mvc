import { useCallback } from 'react'
import { addSaleLine, createSale } from '../../../shared/api/client'
import type { Product, Sale } from '../../../shared/api/types'
import {
  usePosActivity,
  usePosSaleState,
} from '../context/PosWorkspaceState'

/** SRP: current sale cart mutations. First item = one RTT (create + line). */
export function usePosSale() {
  const { sale, setSale } = usePosSaleState()
  const { runBusy, clearFeedback, setMessage } = usePosActivity()

  const ensureSale = useCallback(async (): Promise<Sale> => {
    if (sale) return sale
    const idempotencyKey = crypto.randomUUID()
    const created = await createSale(undefined, idempotencyKey)
    setSale(created.data.sale)
    return created.data.sale
  }, [sale, setSale])

  const addProduct = useCallback(
    async (product: Product) => {
      clearFeedback()
      const idempotencyKey = crypto.randomUUID()
      await runBusy(async () => {
        if (!sale) {
          const created = await createSale(
            { product_id: product.id, quantity: 1 },
            idempotencyKey,
          )
          setSale(created.data.sale)
          return
        }
        const updated = await addSaleLine(sale.id, product.id, 1, idempotencyKey)
        setSale(updated.data.sale)
      })
    },
    [clearFeedback, runBusy, sale, setSale],
  )

  const resetSale = useCallback(() => {
    setSale(null)
  }, [setSale])

  return { sale, setSale, ensureSale, addProduct, resetSale, setMessage }
}
