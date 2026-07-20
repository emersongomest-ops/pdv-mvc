import {
  createContext,
  useContext,
  type Context,
  type Dispatch,
  type SetStateAction,
} from 'react'
import type { Customer, Product, Sale } from '../../../shared/api/types'

export type SaleState = {
  sale: Sale | null
  setSale: Dispatch<SetStateAction<Sale | null>>
}

export type CatalogState = {
  products: Product[]
  setProducts: Dispatch<SetStateAction<Product[]>>
}

export type HeldState = {
  heldSales: Sale[]
  setHeldSales: Dispatch<SetStateAction<Sale[]>>
}

export type CustomerState = {
  customer: Customer | null
  setCustomer: Dispatch<SetStateAction<Customer | null>>
}

export type ActivityState = {
  busy: boolean
  error: string | null
  message: string | null
  setError: Dispatch<SetStateAction<string | null>>
  setMessage: Dispatch<SetStateAction<string | null>>
  clearFeedback: () => void
  runBusy: <T>(fn: () => Promise<T>) => Promise<T | undefined>
}

export const SaleContext = createContext<SaleState | null>(null)
export const CatalogContext = createContext<CatalogState | null>(null)
export const HeldContext = createContext<HeldState | null>(null)
export const CustomerContext = createContext<CustomerState | null>(null)
export const ActivityContext = createContext<ActivityState | null>(null)

function useRequiredContext<T>(context: Context<T | null>, name: string): T {
  const value = useContext(context)
  if (!value) {
    throw new Error(`${name} must be used within PosWorkspaceProvider`)
  }
  return value
}

export function usePosSaleState() {
  return useRequiredContext(SaleContext, 'usePosSaleState')
}

export function usePosCatalogState() {
  return useRequiredContext(CatalogContext, 'usePosCatalogState')
}

export function usePosHeldState() {
  return useRequiredContext(HeldContext, 'usePosHeldState')
}

export function usePosCustomerState() {
  return useRequiredContext(CustomerContext, 'usePosCustomerState')
}

export function usePosActivity() {
  return useRequiredContext(ActivityContext, 'usePosActivity')
}
