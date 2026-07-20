import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from 'react'
import {
  ApiClientError,
  fetchCurrentUser,
  loginRequest,
  logoutRequest,
  setSessionInvalidHandler,
} from '../api/client'
import type { Store, User } from '../api/types'

export type AuthStatus = 'loading' | 'authenticated' | 'anonymous'

type SessionState = {
  user: User | null
  store: Store | null
  shiftOpen: boolean
  authStatus: AuthStatus
  login: (
    email: string,
    password: string,
    turnstileToken?: string | null,
  ) => Promise<
    | { status: 'authenticated'; user: User }
    | { status: 'mfa_required'; user: User }
    | { status: 'mfa_setup_required'; user: User }
  >
  establishSession: (user: User) => void
  setStore: (store: Store) => void
  clearStore: () => void
  setShiftOpen: (open: boolean) => void
  logoutLocal: () => void
  logout: () => Promise<void>
}

const SessionContext = createContext<SessionState | null>(null)

const STORAGE_KEY = 'pdv.session'

type Persisted = {
  user: User
  store: Store | null
  shiftOpen: boolean
}

function loadPersisted(): Persisted | null {
  try {
    const raw = sessionStorage.getItem(STORAGE_KEY)
    return raw ? (JSON.parse(raw) as Persisted) : null
  } catch {
    return null
  }
}

function savePersisted(value: Persisted | null) {
  if (value === null) {
    sessionStorage.removeItem(STORAGE_KEY)
    return
  }
  sessionStorage.setItem(STORAGE_KEY, JSON.stringify(value))
}

function hasSessionHint(): boolean {
  if (loadPersisted()) {
    return true
  }
  return document.cookie.length > 0
}

export function SessionProvider({ children }: { children: ReactNode }) {
  const persisted = loadPersisted()
  const [user, setUser] = useState<User | null>(null)
  const [store, setStoreState] = useState<Store | null>(persisted?.store ?? null)
  const [shiftOpen, setShiftOpenState] = useState(persisted?.shiftOpen ?? false)
  const [authStatus, setAuthStatus] = useState<AuthStatus>('loading')

  const persist = useCallback((next: Persisted | null) => {
    savePersisted(next)
  }, [])

  const clearSessionState = useCallback(() => {
    setUser(null)
    setStoreState(null)
    setShiftOpenState(false)
    persist(null)
    setAuthStatus('anonymous')
  }, [persist])

  useEffect(() => {
    setSessionInvalidHandler(() => {
      clearSessionState()
      if (!window.location.pathname.startsWith('/login')) {
        window.location.assign('/login')
      }
    })
    return () => setSessionInvalidHandler(null)
  }, [clearSessionState])

  useEffect(() => {
    let cancelled = false

    async function validateBoot() {
      if (!hasSessionHint()) {
        if (!cancelled) {
          clearSessionState()
        }
        return
      }

      try {
        const response = await fetchCurrentUser()
        if (cancelled) {
          return
        }
        const nextUser = response.data.user
        const local = loadPersisted()
        const keepLocal =
          local && local.user.id === nextUser.id
            ? { store: local.store, shiftOpen: local.shiftOpen }
            : { store: null as Store | null, shiftOpen: false }

        setUser(nextUser)
        setStoreState(keepLocal.store)
        setShiftOpenState(keepLocal.shiftOpen)
        persist({
          user: nextUser,
          store: keepLocal.store,
          shiftOpen: keepLocal.shiftOpen,
        })
        setAuthStatus('authenticated')
      } catch {
        if (!cancelled) {
          clearSessionState()
        }
      }
    }

    void validateBoot()
    return () => {
      cancelled = true
    }
  }, [clearSessionState, persist])

  const login = useCallback(
    async (email: string, password: string, turnstileToken?: string | null) => {
      const response = await loginRequest(email, password, turnstileToken)
      const nextUser = response.data.user

      if (response.data.mfa_required) {
        return response.data.mfa_setup_required
          ? ({ status: 'mfa_setup_required' as const, user: nextUser })
          : ({ status: 'mfa_required' as const, user: nextUser })
      }

      setUser(nextUser)
      setStoreState(null)
      setShiftOpenState(false)
      persist({ user: nextUser, store: null, shiftOpen: false })
      setAuthStatus('authenticated')
      return { status: 'authenticated' as const, user: nextUser }
    },
    [persist],
  )

  const establishSession = useCallback(
    (nextUser: User) => {
      setUser(nextUser)
      setStoreState(null)
      setShiftOpenState(false)
      persist({ user: nextUser, store: null, shiftOpen: false })
      setAuthStatus('authenticated')
    },
    [persist],
  )

  const setStore = useCallback(
    (next: Store) => {
      setStoreState(next)
      setShiftOpenState(false)
      if (user) {
        persist({ user, store: next, shiftOpen: false })
      }
    },
    [persist, user],
  )

  const clearStore = useCallback(() => {
    setStoreState(null)
    setShiftOpenState(false)
    if (user) {
      persist({ user, store: null, shiftOpen: false })
    }
  }, [persist, user])

  const setShiftOpen = useCallback(
    (open: boolean) => {
      setShiftOpenState(open)
      if (user) {
        persist({ user, store, shiftOpen: open })
      }
    },
    [persist, store, user],
  )

  const logoutLocal = useCallback(() => {
    clearSessionState()
  }, [clearSessionState])

  const logout = useCallback(async () => {
    try {
      await logoutRequest()
    } catch {
      // Server session may already be dead; still clear local.
    } finally {
      clearSessionState()
    }
  }, [clearSessionState])

  const value = useMemo(
    () => ({
      user,
      store,
      shiftOpen,
      authStatus,
      login,
      establishSession,
      setStore,
      clearStore,
      setShiftOpen,
      logoutLocal,
      logout,
    }),
    [
      user,
      store,
      shiftOpen,
      authStatus,
      login,
      establishSession,
      setStore,
      clearStore,
      setShiftOpen,
      logoutLocal,
      logout,
    ],
  )

  return <SessionContext.Provider value={value}>{children}</SessionContext.Provider>
}

export function useSession() {
  const ctx = useContext(SessionContext)
  if (!ctx) {
    throw new Error('useSession must be used within SessionProvider')
  }
  return ctx
}

export function formatApiError(error: unknown): string {
  if (error instanceof ApiClientError) {
    return error.error.message
  }
  if (error instanceof Error) {
    return error.message
  }
  return 'Unexpected error'
}
