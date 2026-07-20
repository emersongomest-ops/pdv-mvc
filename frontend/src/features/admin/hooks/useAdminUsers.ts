import { useCallback, useEffect, useState } from 'react'
import {
  createAdminUser,
  listAdminUsers,
  listStores,
  updateAdminUser,
} from '../../../shared/api/client'
import type {
  AdminUser,
  CreateAdminUserPayload,
  Store,
  UpdateAdminUserPayload,
  UserRole,
} from '../../../shared/api/types'
import { formatApiError } from '../../../shared/session/SessionContext'

export type UserFormState = {
  name: string
  email: string
  role: UserRole
  is_active: boolean
  store_ids: number[]
  password: string
  password_confirmation: string
}

const PAGE_SIZE = 40

const emptyForm = (): UserFormState => ({
  name: '',
  email: '',
  role: 'operator',
  is_active: true,
  store_ids: [],
  password: '',
  password_confirmation: '',
})

function formFromUser(user: AdminUser): UserFormState {
  return {
    name: user.name,
    email: user.email,
    role: user.role,
    is_active: user.is_active,
    store_ids: user.stores.map((store) => store.id),
    password: '',
    password_confirmation: '',
  }
}

export function useAdminUsers() {
  const [users, setUsers] = useState<AdminUser[]>([])
  const [stores, setStores] = useState<Store[]>([])
  const [search, setSearch] = useState('')
  const [form, setForm] = useState<UserFormState>(emptyForm)
  const [editingUserId, setEditingUserId] = useState<number | null>(null)
  const [loading, setLoading] = useState(false)
  const [loadingMore, setLoadingMore] = useState(false)
  const [nextCursor, setNextCursor] = useState<string | null>(null)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [success, setSuccess] = useState<string | null>(null)

  const loadPage = useCallback(async (opts: { term?: string; cursor?: string | null; append: boolean }) => {
    const response = await listAdminUsers({
      search: opts.term?.trim() || undefined,
      cursor: opts.cursor || undefined,
      per_page: PAGE_SIZE,
    })
    setUsers((current) =>
      opts.append ? [...current, ...response.data.users] : response.data.users,
    )
    setNextCursor(response.meta?.next_cursor ?? null)
  }, [])

  const loadUsers = useCallback(
    async (term?: string) => {
      setLoading(true)
      setError(null)
      try {
        await loadPage({ term, append: false })
      } catch (err) {
        setUsers([])
        setNextCursor(null)
        setError(formatApiError(err))
      } finally {
        setLoading(false)
      }
    },
    [loadPage],
  )

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

  const loadStores = useCallback(async () => {
    try {
      const response = await listStores()
      setStores(response.data.stores)
    } catch (err) {
      setError(formatApiError(err))
    }
  }, [])

  useEffect(() => {
    void loadUsers()
    void loadStores()
  }, [loadUsers, loadStores])

  const startCreate = useCallback(() => {
    setEditingUserId(null)
    setForm(emptyForm())
    setSuccess(null)
    setError(null)
  }, [])

  const startEdit = useCallback((user: AdminUser) => {
    setEditingUserId(user.id)
    setForm(formFromUser(user))
    setSuccess(null)
    setError(null)
  }, [])

  const updateForm = useCallback(<K extends keyof UserFormState>(key: K, value: UserFormState[K]) => {
    setForm((current) => ({ ...current, [key]: value }))
  }, [])

  const toggleStore = useCallback((storeId: number) => {
    setForm((current) => {
      const has = current.store_ids.includes(storeId)
      return {
        ...current,
        store_ids: has
          ? current.store_ids.filter((id) => id !== storeId)
          : [...current.store_ids, storeId],
      }
    })
  }, [])

  const save = useCallback(async () => {
    setSaving(true)
    setError(null)
    setSuccess(null)
    try {
      if (form.store_ids.length < 1) {
        setError('Select at least one store.')
        return
      }

      if (editingUserId === null) {
        if (!form.password || form.password.length < 8) {
          setError('Password must be at least 8 characters.')
          return
        }
        if (form.password !== form.password_confirmation) {
          setError('Password confirmation does not match.')
          return
        }
        const payload: CreateAdminUserPayload = {
          name: form.name.trim(),
          email: form.email.trim(),
          password: form.password,
          password_confirmation: form.password_confirmation,
          role: form.role,
          is_active: form.is_active,
          store_ids: form.store_ids,
        }
        const response = await createAdminUser(payload)
        setSuccess(response.data.message)
      } else {
        const payload: UpdateAdminUserPayload = {
          name: form.name.trim(),
          email: form.email.trim(),
          role: form.role,
          is_active: form.is_active,
          store_ids: form.store_ids,
        }
        if (form.password) {
          if (form.password.length < 8) {
            setError('Password must be at least 8 characters.')
            return
          }
          if (form.password !== form.password_confirmation) {
            setError('Password confirmation does not match.')
            return
          }
          payload.password = form.password
          payload.password_confirmation = form.password_confirmation
        }
        const response = await updateAdminUser(editingUserId, payload)
        setSuccess(response.data.message)
      }

      await loadUsers(search)
      startCreate()
    } catch (err) {
      setError(formatApiError(err))
    } finally {
      setSaving(false)
    }
  }, [editingUserId, form, loadUsers, search, startCreate])

  return {
    users,
    stores,
    search,
    setSearch,
    form,
    editingUserId,
    loading,
    loadingMore,
    nextCursor,
    saving,
    error,
    success,
    loadUsers,
    loadMore,
    startCreate,
    startEdit,
    updateForm,
    toggleStore,
    save,
  }
}
