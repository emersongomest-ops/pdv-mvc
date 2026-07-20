import { useCallback, useEffect, useState } from 'react'
import {
  createAdminPromotion,
  listAdminCustomers,
  listAdminPromotions,
  updateAdminPromotion,
} from '../../../shared/api/client'
import type {
  Customer,
  DiscountType,
  Promotion,
  PromotionPayload,
  StackingMode,
} from '../../../shared/api/types'
import { formatApiError } from '../../../shared/session/SessionContext'

export type PromotionFormState = PromotionPayload

const PAGE_SIZE = 40

const emptyForm = (): PromotionFormState => ({
  code: '',
  name: '',
  discount_type: 'percent',
  discount_value: '10',
  stacking_mode: 'unique',
  applies_to_all_customers: true,
  is_active: true,
  starts_at: '',
  ends_at: '',
  customer_ids: [],
})

function formFromPromotion(promotion: Promotion): PromotionFormState {
  return {
    code: promotion.code,
    name: promotion.name,
    discount_type: promotion.discount_type,
    discount_value: promotion.discount_value,
    stacking_mode: promotion.stacking_mode,
    applies_to_all_customers: promotion.applies_to_all_customers,
    is_active: promotion.is_active,
    starts_at: promotion.starts_at?.slice(0, 10) ?? '',
    ends_at: promotion.ends_at?.slice(0, 10) ?? '',
    customer_ids: [...promotion.customer_ids],
  }
}

function toPayload(form: PromotionFormState): PromotionPayload {
  return {
    ...form,
    starts_at: (form.starts_at ?? '').trim() || null,
    ends_at: (form.ends_at ?? '').trim() || null,
    customer_ids: form.applies_to_all_customers ? [] : form.customer_ids,
  }
}

export function useAdminPromotions() {
  const [promotions, setPromotions] = useState<Promotion[]>([])
  const [customers, setCustomers] = useState<Customer[]>([])
  const [form, setForm] = useState<PromotionFormState>(emptyForm)
  const [editingId, setEditingId] = useState<number | null>(null)
  const [loading, setLoading] = useState(false)
  const [loadingMore, setLoadingMore] = useState(false)
  const [nextCursor, setNextCursor] = useState<string | null>(null)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [success, setSuccess] = useState<string | null>(null)

  const loadPromoPage = useCallback(async (opts: { cursor?: string | null; append: boolean }) => {
    const response = await listAdminPromotions({
      cursor: opts.cursor || undefined,
      per_page: PAGE_SIZE,
    })
    setPromotions((current) =>
      opts.append ? [...current, ...response.data.promotions] : response.data.promotions,
    )
    setNextCursor(response.meta?.next_cursor ?? null)
  }, [])

  const load = useCallback(async () => {
    setLoading(true)
    setError(null)
    try {
      const [, customerRes] = await Promise.all([
        loadPromoPage({ append: false }),
        listAdminCustomers(),
      ])
      setCustomers(customerRes.data.customers)
    } catch (err) {
      setPromotions([])
      setNextCursor(null)
      setError(formatApiError(err))
    } finally {
      setLoading(false)
    }
  }, [loadPromoPage])

  useEffect(() => {
    void load()
  }, [load])

  const loadMore = useCallback(async () => {
    if (!nextCursor || loadingMore) return
    setLoadingMore(true)
    setError(null)
    try {
      await loadPromoPage({ cursor: nextCursor, append: true })
    } catch (err) {
      setError(formatApiError(err))
    } finally {
      setLoadingMore(false)
    }
  }, [nextCursor, loadingMore, loadPromoPage])

  const updateForm = <K extends keyof PromotionFormState>(key: K, value: PromotionFormState[K]) => {
    setForm((current) => ({ ...current, [key]: value }))
  }

  const toggleCustomer = (customerId: number) => {
    setForm((current) => {
      const has = current.customer_ids.includes(customerId)
      return {
        ...current,
        customer_ids: has
          ? current.customer_ids.filter((id) => id !== customerId)
          : [...current.customer_ids, customerId],
      }
    })
  }

  const startCreate = () => {
    setEditingId(null)
    setForm(emptyForm())
    setSuccess(null)
  }

  const startEdit = (promotion: Promotion) => {
    setEditingId(promotion.id)
    setForm(formFromPromotion(promotion))
    setSuccess(null)
  }

  const save = async () => {
    setSaving(true)
    setError(null)
    setSuccess(null)
    try {
      const payload = toPayload(form)
      if (editingId === null) {
        await createAdminPromotion(payload)
        setSuccess('Promotion created.')
      } else {
        await updateAdminPromotion(editingId, payload)
        setSuccess('Promotion updated.')
      }
      setForm(emptyForm())
      setEditingId(null)
      await load()
    } catch (err) {
      setError(formatApiError(err))
    } finally {
      setSaving(false)
    }
  }

  return {
    promotions,
    customers,
    form,
    editingId,
    loading,
    loadingMore,
    nextCursor,
    saving,
    error,
    success,
    load,
    loadMore,
    updateForm,
    toggleCustomer,
    startCreate,
    startEdit,
    save,
    setDiscountType: (value: DiscountType) => updateForm('discount_type', value),
    setStackingMode: (value: StackingMode) => updateForm('stacking_mode', value),
  }
}
