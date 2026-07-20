import { useCallback, useEffect, useState } from 'react'
import { useSearchParams } from 'react-router-dom'
import {
  createRefund,
  getAdminSale,
  listRefundsForSale,
} from '../../../shared/api/client'
import type { CreateRefundPayload, Refund, RefundType, Sale } from '../../../shared/api/types'
import { formatApiError } from '../../../shared/session/SessionContext'

export type LineSelection = {
  sale_line_id: number
  quantity: number
  selected: boolean
  maxQuantity: number
}

export function useAdminRefunds() {
  const [searchParams, setSearchParams] = useSearchParams()
  const initialSaleId = searchParams.get('sale_id') ?? ''

  const [saleIdInput, setSaleIdInput] = useState(initialSaleId)
  const [sale, setSale] = useState<Sale | null>(null)
  const [refunds, setRefunds] = useState<Refund[]>([])
  const [lineSelections, setLineSelections] = useState<LineSelection[]>([])
  const [type, setType] = useState<RefundType>('partial_return')
  const [reason, setReason] = useState('')
  const [loading, setLoading] = useState(false)
  const [submitting, setSubmitting] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [success, setSuccess] = useState<string | null>(null)

  const loadSale = useCallback(async (saleId: number) => {
    setLoading(true)
    setError(null)
    setSuccess(null)
    try {
      const [saleResponse, refundsResponse] = await Promise.all([
        getAdminSale(saleId),
        listRefundsForSale(saleId),
      ])
      const nextSale = saleResponse.data.sale
      setSale(nextSale)
      setRefunds(refundsResponse.data.refunds)
      setLineSelections(
        nextSale.lines.map((line) => ({
          sale_line_id: line.id,
          quantity: line.quantity,
          selected: false,
          maxQuantity: line.quantity,
        })),
      )
      setSearchParams({ sale_id: String(saleId) })
    } catch (err) {
      setSale(null)
      setRefunds([])
      setLineSelections([])
      setError(formatApiError(err))
    } finally {
      setLoading(false)
    }
  }, [setSearchParams])

  useEffect(() => {
    const fromQuery = Number(initialSaleId)
    if (initialSaleId && Number.isFinite(fromQuery) && fromQuery > 0) {
      void loadSale(fromQuery)
    }
  }, [initialSaleId, loadSale])

  const lookup = useCallback(() => {
    const saleId = Number(saleIdInput)
    if (!Number.isFinite(saleId) || saleId < 1) {
      setError('Enter a valid sale ID.')
      return
    }
    void loadSale(saleId)
  }, [loadSale, saleIdInput])

  const updateLine = useCallback((saleLineId: number, patch: Partial<LineSelection>) => {
    setLineSelections((current) =>
      current.map((line) =>
        line.sale_line_id === saleLineId ? { ...line, ...patch } : line,
      ),
    )
  }, [])

  const needsLines = type === 'partial_refund' || type === 'partial_return'

  const submit = useCallback(async () => {
    if (!sale) return
    if (reason.trim().length < 3) {
      setError('Reason must be at least 3 characters.')
      return
    }

    const payload: CreateRefundPayload = {
      type,
      reason: reason.trim(),
    }

    if (needsLines) {
      const lines = lineSelections
        .filter((line) => line.selected)
        .map((line) => ({
          sale_line_id: line.sale_line_id,
          quantity: line.quantity,
        }))
      if (lines.length === 0) {
        setError('Select at least one sale line for partial refund/return.')
        return
      }
      payload.lines = lines
    }

    setSubmitting(true)
    setError(null)
    setSuccess(null)
    const idempotencyKey = crypto.randomUUID()
    try {
      const response = await createRefund(sale.id, payload, idempotencyKey)
      setSuccess(response.data.message)
      setReason('')
      await loadSale(sale.id)
    } catch (err) {
      setError(formatApiError(err))
    } finally {
      setSubmitting(false)
    }
  }, [lineSelections, loadSale, needsLines, reason, sale, type])

  return {
    saleIdInput,
    setSaleIdInput,
    sale,
    refunds,
    lineSelections,
    updateLine,
    type,
    setType,
    reason,
    setReason,
    needsLines,
    loading,
    submitting,
    error,
    success,
    lookup,
    submit,
  }
}
