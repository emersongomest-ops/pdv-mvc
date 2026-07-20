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
    const created = await createSale()
    setSale(created.data.sale)
    return created.data.sale
  }, [sale, setSale])

  const addProduct = useCallback(
    async (product: Product) => {
      clearFeedback()
      await runBusy(async () => {
        if (!sale) {
          const created = await createSale({ product_id: product.id, quantity: 1 })
          setSale(created.data.sale)
          return
        }
        const updated = await addSaleLine(sale.id, product.id, 1)
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
