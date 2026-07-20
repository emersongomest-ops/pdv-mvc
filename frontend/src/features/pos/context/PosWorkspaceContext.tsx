import {
  useCallback,
  useMemo,
  useState,
  type ReactNode,
} from 'react'
import type { Customer, Product, Sale } from '../../../shared/api/types'
import { formatApiError } from '../../../shared/session/SessionContext'
import {
  ActivityContext,
  CatalogContext,
  CustomerContext,
  HeldContext,
  SaleContext,
} from './PosWorkspaceState'

export function PosWorkspaceProvider({ children }: { children: ReactNode }) {
  const [sale, setSale] = useState<Sale | null>(null)
  const [products, setProducts] = useState<Product[]>([])
  const [heldSales, setHeldSales] = useState<Sale[]>([])
  const [customer, setCustomer] = useState<Customer | null>(null)
  const [busy, setBusy] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [message, setMessage] = useState<string | null>(null)

  const clearFeedback = useCallback(() => {
    setError(null)
    setMessage(null)
  }, [])

  const runBusy = useCallback(async <T,>(fn: () => Promise<T>) => {
    setBusy(true)
    setError(null)
    try {
      return await fn()
    } catch (err) {
      setError(formatApiError(err))
      return undefined
    } finally {
      setBusy(false)
    }
  }, [])

  const saleValue = useMemo(() => ({ sale, setSale }), [sale])
  const catalogValue = useMemo(() => ({ products, setProducts }), [products])
  const heldValue = useMemo(() => ({ heldSales, setHeldSales }), [heldSales])
  const customerValue = useMemo(() => ({ customer, setCustomer }), [customer])
  const activityValue = useMemo(
    () => ({
      busy,
      error,
      message,
      setError,
      setMessage,
      clearFeedback,
      runBusy,
    }),
    [
      busy,
      error,
      message,
      clearFeedback,
      runBusy,
    ],
  )

  return (
    <SaleContext.Provider value={saleValue}>
      <CatalogContext.Provider value={catalogValue}>
        <HeldContext.Provider value={heldValue}>
          <CustomerContext.Provider value={customerValue}>
            <ActivityContext.Provider value={activityValue}>
              {children}
            </ActivityContext.Provider>
          </CustomerContext.Provider>
        </HeldContext.Provider>
      </CatalogContext.Provider>
    </SaleContext.Provider>
  )
}
