import { useCallback, useEffect, useState } from 'react'
import {
  getAdminShiftReport,
  listAdminShifts,
  listStores,
  reopenAdminShift,
} from '../../../shared/api/client'
import type { AdminShiftSummary, ShiftClosingReport, Store } from '../../../shared/api/types'
import { formatApiError } from '../../../shared/session/SessionContext'

export function useAdminShifts() {
  const [stores, setStores] = useState<Store[]>([])
  const [storeIdInput, setStoreIdInput] = useState('')
  const [shifts, setShifts] = useState<AdminShiftSummary[]>([])
  const [report, setReport] = useState<ShiftClosingReport | null>(null)
  const [selectedShiftId, setSelectedShiftId] = useState<number | null>(null)
  const [loading, setLoading] = useState(false)
  const [reopeningId, setReopeningId] = useState<number | null>(null)
  const [error, setError] = useState<string | null>(null)
  const [success, setSuccess] = useState<string | null>(null)

  useEffect(() => {
    listStores()
      .then((response) => {
        setStores(response.data.stores)
        if (response.data.stores.length === 1) {
          setStoreIdInput(String(response.data.stores[0].id))
        }
      })
      .catch((err) => setError(formatApiError(err)))
  }, [])

  const loadShifts = useCallback(async () => {
    const storeId = Number(storeIdInput)
    if (!Number.isFinite(storeId) || storeId < 1) {
      setError('Select an assigned store.')
      return
    }
    setLoading(true)
    setError(null)
    setSuccess(null)
    setReport(null)
    setSelectedShiftId(null)
    try {
      const response = await listAdminShifts(storeId)
      setShifts(response.data.shifts)
    } catch (err) {
      setShifts([])
      setError(formatApiError(err))
    } finally {
      setLoading(false)
    }
  }, [storeIdInput])

  const loadReport = useCallback(async (shiftId: number) => {
    setLoading(true)
    setError(null)
    setSelectedShiftId(shiftId)
    try {
      const response = await getAdminShiftReport(shiftId)
      setReport(response.data.report)
    } catch (err) {
      setReport(null)
      setError(formatApiError(err))
    } finally {
      setLoading(false)
    }
  }, [])

  const reopenShift = useCallback(
    async (shiftId: number) => {
      setReopeningId(shiftId)
      setError(null)
      setSuccess(null)
      try {
        const response = await reopenAdminShift(shiftId)
        setSuccess(response.data.message)
        await loadShifts()
        if (selectedShiftId === shiftId) {
          setReport(null)
          setSelectedShiftId(null)
        }
      } catch (err) {
        setError(formatApiError(err))
      } finally {
        setReopeningId(null)
      }
    },
    [loadShifts, selectedShiftId],
  )

  return {
    stores,
    storeIdInput,
    setStoreIdInput,
    shifts,
    report,
    selectedShiftId,
    loading,
    reopeningId,
    error,
    success,
    loadShifts,
    loadReport,
    reopenShift,
  }
}
