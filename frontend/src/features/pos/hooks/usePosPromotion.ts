import { useCallback, useState } from 'react'
import { applyPromotionToSale } from '../../../shared/api/client'
import {
  usePosActivity,
  usePosSaleState,
} from '../context/PosWorkspaceState'
import { usePosSale } from './usePosSale'

/** SRP: apply promotion code to current sale. */
export function usePosPromotion() {
  const { sale } = usePosSaleState()
  const { busy, setError, setMessage, runBusy, clearFeedback } = usePosActivity()
  const { setSale } = usePosSale()
  const [promoCode, setPromoCode] = useState('')

  const applyPromo = useCallback(async () => {
    if (!sale) {
      setError('Carrinho vazio — adicione itens antes do cupom.')
      return
    }
    if (!promoCode.trim()) {
      setError('Informe o código da promoção.')
      return
    }
    clearFeedback()
    await runBusy(async () => {
      const code = promoCode.trim()
      const updated = await applyPromotionToSale(sale.id, code)
      setSale(updated.data.sale)
      setMessage(`Promoção ${code.toUpperCase()} aplicada.`)
      setPromoCode('')
    })
  }, [sale, promoCode, clearFeedback, runBusy, setError, setSale, setMessage])

  return { promoCode, setPromoCode, busy, applyPromo }
}
