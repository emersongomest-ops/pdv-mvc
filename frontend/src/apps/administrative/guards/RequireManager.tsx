import type { ReactNode } from 'react'
import { Navigate } from 'react-router-dom'
import { useSession } from '../../../shared/session/SessionContext'

export function RequireManager({ children }: { children: ReactNode }) {
  const { user, authStatus } = useSession()

  if (authStatus === 'loading') {
    return <p className="auth-boot">Checking session…</p>
  }

  if (authStatus === 'anonymous' || !user) {
    return <Navigate to="/login" replace />
  }

  if (user.role !== 'manager') {
    return <Navigate to="/store" replace />
  }

  return children
}
