import { useCallback, useEffect, useState } from 'react'
import {
  adjustAdminInventory,
  listAdminInventory,
  listAdminProducts,
  listStores,
} from '../../../shared/api/client'
import type { AdminProduct, Store, StoreInventoryRow } from '../../../shared/api/types'
import { formatApiError } from '../../../shared/session/SessionContext'

export type InventoryAdjustForm = {
  product_id: number | ''
  quantity: string
  reason: string
}

const emptyAdjust = (): InventoryAdjustForm => ({
  product_id: '',
  quantity: '0',
  reason: '',
})

export function useAdminInventory() {
  const [stores, setStores] = useState<Store[]>([])
  const [products, setProducts] = useState<AdminProduct[]>([])
  const [storeId, setStoreId] = useState<number | null>(null)
  const [rows, setRows] = useState<StoreInventoryRow[]>([])
  const [form, setForm] = useState<InventoryAdjustForm>(emptyAdjust)
  const [loading, setLoading] = useState(false)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [success, setSuccess] = useState<string | null>(null)

  const loadMeta = useCallback(async () => {
    setError(null)
    try {
      const [storeRes, productRes] = await Promise.all([listStores(), listAdminProducts()])
      setStores(storeRes.data.stores)
      setProducts(productRes.data.products)
      setStoreId((current) => current ?? storeRes.data.stores[0]?.id ?? null)
    } catch (err) {
      setError(formatApiError(err))
    }
  }, [])

  const loadInventory = useCallback(async (id: number) => {
    setLoading(true)
    setError(null)
    try {
      const response = await listAdminInventory(id)
      setRows(response.data.inventory)
    } catch (err) {
      setRows([])
      setError(formatApiError(err))
    } finally {
      setLoading(false)
    }
  }, [])

  useEffect(() => {
    void loadMeta()
  }, [loadMeta])

  useEffect(() => {
    if (storeId !== null) {
      void loadInventory(storeId)
    }
  }, [storeId, loadInventory])

  const updateForm = <K extends keyof InventoryAdjustForm>(key: K, value: InventoryAdjustForm[K]) => {
    setForm((current) => ({ ...current, [key]: value }))
  }

  const save = async () => {
    if (storeId === null || form.product_id === '') {
      setError('Select a store and product.')
      return
    }
    setSaving(true)
    setError(null)
    setSuccess(null)
    try {
      await adjustAdminInventory({
        store_id: storeId,
        product_id: form.product_id,
        quantity: Number(form.quantity),
        reason: form.reason.trim(),
      })
      setSuccess('Inventory adjusted (audited).')
      setForm(emptyAdjust())
      await loadInventory(storeId)
    } catch (err) {
      setError(formatApiError(err))
    } finally {
      setSaving(false)
    }
  }

  return {
    stores,
    products,
    storeId,
    setStoreId,
    rows,
    form,
    loading,
    saving,
    error,
    success,
    updateForm,
    save,
  }
}
