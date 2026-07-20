import { useCallback, useEffect, useState } from 'react'
import { holdSale, listHeldSales, resumeSale } from '../../../shared/api/client'
import type { Sale } from '../../../shared/api/types'
import { formatApiError } from '../../../shared/session/SessionContext'
import {
  usePosActivity,
  usePosHeldState,
  usePosSaleState,
} from '../context/PosWorkspaceState'

type UsePosHeldOptions = {
  /** Only one caller should load held list on mount (CatalogContainer). */
  loadOnMount?: boolean
}

/** SRP: park / resume held carts (heldSales shared via workspace). */
export function usePosHeld(shiftOpen: boolean, options: UsePosHeldOptions = {}) {
  const { loadOnMount = false } = options
  const { sale, setSale } = usePosSaleState()
  const { heldSales, setHeldSales } = usePosHeldState()
  const { busy, setError, setMessage, runBusy, clearFeedback } = usePosActivity()
  const [holdLabel, setHoldLabel] = useState('')

  const refreshHeld = useCallback(async () => {
    const res = await listHeldSales()
    setHeldSales(res.data.sales)
  }, [setHeldSales])

  useEffect(() => {
    if (!loadOnMount || !shiftOpen) return
    refreshHeld().catch((err) => setError(formatApiError(err)))
  }, [loadOnMount, shiftOpen, refreshHeld, setError])

  const parkSale = useCallback(async () => {
    if (!sale || sale.lines.length === 0) {
      setError('Nada para estacionar.')
      return
    }
    clearFeedback()
    await runBusy(async () => {
      await holdSale(sale.id, holdLabel.trim() || undefined)
      setMessage(`Venda #${sale.id} em hold.`)
      setSale(null)
      setHoldLabel('')
      await refreshHeld()
    })
  }, [
    sale,
    holdLabel,
    clearFeedback,
    runBusy,
    setError,
    setMessage,
    setSale,
    refreshHeld,
  ])

  const resumeHeld = useCallback(
    async (held: Sale) => {
      clearFeedback()
      await runBusy(async () => {
        const updated = await resumeSale(held.id)
        setSale(updated.data.sale)
        setMessage(`Venda #${held.id} retomada.`)
        await refreshHeld()
      })
    },
    [clearFeedback, runBusy, setSale, setMessage, refreshHeld],
  )

  return {
    heldSales,
    holdLabel,
    setHoldLabel,
    busy,
    parkSale,
    resumeHeld,
  }
}
