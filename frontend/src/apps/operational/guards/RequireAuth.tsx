import type { ReactNode } from 'react'
import { Navigate } from 'react-router-dom'
import { useSession } from '../../../shared/session/SessionContext'

/** Blocks internal routes until server session is confirmed. */
export function RequireAuth({ children }: { children: ReactNode }) {
  const { authStatus } = useSession()

  if (authStatus === 'loading') {
    return <p className="auth-boot">Checking session…</p>
  }

  if (authStatus === 'anonymous') {
    return <Navigate to="/login" replace />
  }

  return children
}
