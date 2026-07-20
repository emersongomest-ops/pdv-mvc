import { useCallback, useMemo } from 'react'
import { useNavigate } from 'react-router-dom'
import { IdleSessionBanner } from './IdleSessionBanner'
import { useSession } from './SessionContext'
import { readIdleSessionConfig, useIdleSession } from './useIdleSession'

/** Mounts idle warning + forced logout when authenticated (ASVS V3 / TM-06). */
export function IdleSessionGuard() {
  const { authStatus, logout } = useSession()
  const navigate = useNavigate()
  const config = useMemo(() => readIdleSessionConfig(), [])

  const onTimeout = useCallback(() => {
    void logout().then(() => navigate('/login', { replace: true }))
  }, [logout, navigate])

  const { warning } = useIdleSession({
    enabled: authStatus === 'authenticated',
    config,
    onTimeout,
  })

  if (authStatus !== 'authenticated' || config.logoutMinutes <= 0) {
    return null
  }

  return (
    <IdleSessionBanner
      visible={warning}
      logoutMinutes={config.logoutMinutes}
      onStay={() => {
        // Activity listeners already reset timers; synthetic click also counts as activity.
        window.dispatchEvent(new Event('mousedown'))
      }}
      onLogout={() => void logout().then(() => navigate('/login', { replace: true }))}
    />
  )
}
