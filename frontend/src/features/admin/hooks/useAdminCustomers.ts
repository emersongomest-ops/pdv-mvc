import { useCallback, useEffect, useState } from 'react'
import {
  createAdminCustomer,
  listAdminCustomers,
  updateAdminCustomer,
} from '../../../shared/api/client'
import type { Customer, CustomerPayload } from '../../../shared/api/types'
import { formatApiError } from '../../../shared/session/SessionContext'

export type CustomerFormState = CustomerPayload

const PAGE_SIZE = 40

const emptyForm = (): CustomerFormState => ({
  name: '',
  email: '',
  cpf: '',
  phone: '',
  birth_date: '',
  address: '',
})

function formFromCustomer(customer: Customer): CustomerFormState {
  return {
    name: customer.name,
    email: customer.email,
    cpf: customer.cpf,
    phone: customer.phone,
    birth_date: customer.birth_date ?? '',
    address: customer.address ?? '',
  }
}

export function useAdminCustomers() {
  const [customers, setCustomers] = useState<Customer[]>([])
  const [search, setSearch] = useState('')
  const [form, setForm] = useState<CustomerFormState>(emptyForm)
  const [editingId, setEditingId] = useState<number | null>(null)
  const [loading, setLoading] = useState(false)
  const [loadingMore, setLoadingMore] = useState(false)
  const [nextCursor, setNextCursor] = useState<string | null>(null)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [success, setSuccess] = useState<string | null>(null)

  const loadPage = useCallback(
    async (opts: { term?: string; cursor?: string | null; append: boolean }) => {
      const response = await listAdminCustomers({
        search: opts.term?.trim() || undefined,
        cursor: opts.cursor || undefined,
        per_page: PAGE_SIZE,
      })
      setCustomers((current) =>
        opts.append ? [...current, ...response.data.customers] : response.data.customers,
      )
      setNextCursor(response.meta?.next_cursor ?? null)
    },
    [],
  )

  const load = useCallback(
    async (term?: string) => {
      setLoading(true)
      setError(null)
      try {
        await loadPage({ term, append: false })
      } catch (err) {
        setCustomers([])
        setNextCursor(null)
        setError(formatApiError(err))
      } finally {
        setLoading(false)
      }
    },
    [loadPage],
  )

  useEffect(() => {
    void load()
  }, [load])

  const loadMore = useCallback(async () => {
    if (!nextCursor || loadingMore) return
    setLoadingMore(true)
    setError(null)
    try {
      await loadPage({ term: search, cursor: nextCursor, append: true })
    } catch (err) {
      setError(formatApiError(err))
    } finally {
      setLoadingMore(false)
    }
  }, [nextCursor, loadingMore, loadPage, search])

  const updateForm = <K extends keyof CustomerFormState>(key: K, value: CustomerFormState[K]) => {
    setForm((current) => ({ ...current, [key]: value }))
  }

  const startCreate = () => {
    setEditingId(null)
    setForm(emptyForm())
    setSuccess(null)
  }

  const startEdit = (customer: Customer) => {
    setEditingId(customer.id)
    setForm(formFromCustomer(customer))
    setSuccess(null)
  }

  const save = async () => {
    setSaving(true)
    setError(null)
    setSuccess(null)
    try {
      if (editingId === null) {
        await createAdminCustomer(form)
        setSuccess('Customer created.')
      } else {
        await updateAdminCustomer(editingId, form)
        setSuccess('Customer updated.')
      }
      setForm(emptyForm())
      setEditingId(null)
      await load(search)
    } catch (err) {
      setError(formatApiError(err))
    } finally {
      setSaving(false)
    }
  }

  return {
    customers,
    search,
    setSearch,
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
    startCreate,
    startEdit,
    save,
  }
}
