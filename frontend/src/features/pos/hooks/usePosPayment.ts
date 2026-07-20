import { useCallback, useMemo, useState } from 'react'
import { completeSale } from '../../../shared/api/client'
import { formatMoney, moneyDiff, toApiDecimal } from '../../../shared/lib/money'
import {
  usePosActivity,
  usePosSaleState,
} from '../context/PosWorkspaceState'
import { usePosSale } from './usePosSale'

export type PayMethod = 'pix' | 'cash'

/** SRP: payment method + complete sale. */
export function usePosPayment(onPaid?: () => void) {
  const { sale } = usePosSaleState()
  const { busy, setError, setMessage, runBusy, clearFeedback } = usePosActivity()
  const { setSale } = usePosSale()
  const [payMethod, setPayMethod] = useState<PayMethod>('pix')
  const [cashReceived, setCashReceived] = useState('')

  const cashChange = useMemo(() => {
    if (payMethod !== 'cash' || !sale || !cashReceived) return null
    const change = moneyDiff(cashReceived, sale.total)
    return Number.isFinite(change) ? change : null
  }, [payMethod, sale, cashReceived])

  const pay = useCallback(async () => {
    if (!sale || sale.lines.length === 0) {
      setError('Cart is empty.')
      return
    }

    if (payMethod === 'cash' && Number(cashReceived) < Number(sale.total)) {
      setError('PAY_CASH_INSUFFICIENT: valor recebido menor que o total.')
      return
    }

    const amount = toApiDecimal(String(sale.total))
    const payments =
      payMethod === 'cash'
        ? [
            {
              method: 'cash',
              amount,
              cash_received: toApiDecimal(cashReceived || String(sale.total)),
            },
          ]
        : [{ method: 'pix', amount }]

    clearFeedback()
    await runBusy(async () => {
      const completed = await completeSale(sale.id, payments)
      const change =
        payMethod === 'cash' && cashReceived
          ? ` · troco ${formatMoney(moneyDiff(cashReceived, sale.total))}`
          : ''
      setMessage(
        `Sale #${completed.data.sale.id} completed — ${payMethod.toUpperCase()} ${formatMoney(completed.data.sale.total)}${change}`,
      )
      setSale(null)
      setCashReceived('')
      setPayMethod('pix')
      onPaid?.()
    })
  }, [
    sale,
    payMethod,
    cashReceived,
    clearFeedback,
    runBusy,
    setError,
    setMessage,
    setSale,
    onPaid,
  ])

  return {
    payMethod,
    setPayMethod,
    cashReceived,
    setCashReceived,
    cashChange,
    busy,
    pay,
  }
}
