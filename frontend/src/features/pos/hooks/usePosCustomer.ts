import { useCallback, useState } from 'react'
import { attachCustomerToSale, findCustomerByCpf } from '../../../shared/api/client'
import {
  usePosActivity,
  usePosCustomerState,
} from '../context/PosWorkspaceState'
import { usePosSale } from './usePosSale'

/** SRP: attach customer by CPF (customer shared via workspace). */
export function usePosCustomer() {
  const { customer, setCustomer } = usePosCustomerState()
  const { busy, setError, setMessage, runBusy, clearFeedback } = usePosActivity()
  const { ensureSale, setSale } = usePosSale()
  const [cpfInput, setCpfInput] = useState('')

  const lookupCustomer = useCallback(async () => {
    if (!cpfInput.trim()) {
      setError('Informe o CPF.')
      return
    }
    clearFeedback()
    await runBusy(async () => {
      const found = await findCustomerByCpf(cpfInput.trim())
      setCustomer(found.data.customer)
      const current = await ensureSale()
      const updated = await attachCustomerToSale(current.id, found.data.customer.id)
      setSale(updated.data.sale)
      setMessage(`Cliente ${found.data.customer.name} vinculado.`)
    })
  }, [
    cpfInput,
    clearFeedback,
    runBusy,
    setError,
    ensureSale,
    setSale,
    setMessage,
    setCustomer,
  ])

  const clearCustomer = useCallback(() => {
    setCustomer(null)
    setCpfInput('')
  }, [setCustomer])

  return {
    customer,
    cpfInput,
    setCpfInput,
    busy,
    lookupCustomer,
    clearCustomer,
  }
}
