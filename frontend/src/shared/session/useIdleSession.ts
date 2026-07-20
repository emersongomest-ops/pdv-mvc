import { useEffect, useRef, useState } from 'react'

const ACTIVITY_EVENTS = ['mousedown', 'keydown', 'touchstart', 'scroll', 'mousemove'] as const

export type IdleSessionConfig = {
  /** Minutes of idle before warning. 0 = disabled. */
  warningMinutes: number
  /** Minutes of idle before logout. Must be > warningMinutes when enabled. */
  logoutMinutes: number
}

export function readIdleSessionConfig(): IdleSessionConfig {
  const warningMinutes = Number(import.meta.env.VITE_IDLE_WARNING_MINUTES ?? 14)
  const logoutMinutes = Number(import.meta.env.VITE_IDLE_LOGOUT_MINUTES ?? 15)

  if (!Number.isFinite(logoutMinutes) || logoutMinutes <= 0) {
    return { warningMinutes: 0, logoutMinutes: 0 }
  }

  const warn = Number.isFinite(warningMinutes) && warningMinutes > 0 ? warningMinutes : logoutMinutes * 0.9

  return {
    warningMinutes: Math.min(warn, logoutMinutes - 0.05),
    logoutMinutes,
  }
}

type UseIdleSessionOptions = {
  enabled: boolean
  config: IdleSessionConfig
  onTimeout: () => void
}

/**
 * Client-side idle guard for shared POS terminals (ASVS V3).
 * Server SESSION_LIFETIME remains authoritative for absolute cookie lifetime.
 */
export function useIdleSession({ enabled, config, onTimeout }: UseIdleSessionOptions) {
  const [warning, setWarning] = useState(false)
  const warningTimer = useRef<ReturnType<typeof setTimeout> | null>(null)
  const logoutTimer = useRef<ReturnType<typeof setTimeout> | null>(null)
  const onTimeoutRef = useRef(onTimeout)
  onTimeoutRef.current = onTimeout

  useEffect(() => {
    if (!enabled || config.logoutMinutes <= 0) {
      setWarning(false)
      return
    }

    const warningMs = Math.max(0, config.warningMinutes * 60_000)
    const logoutMs = config.logoutMinutes * 60_000

    function clearTimers() {
      if (warningTimer.current) clearTimeout(warningTimer.current)
      if (logoutTimer.current) clearTimeout(logoutTimer.current)
      warningTimer.current = null
      logoutTimer.current = null
    }

    function arm() {
      clearTimers()
      setWarning(false)

      if (warningMs > 0 && warningMs < logoutMs) {
        warningTimer.current = setTimeout(() => setWarning(true), warningMs)
      }

      logoutTimer.current = setTimeout(() => {
        setWarning(false)
        onTimeoutRef.current()
      }, logoutMs)
    }

    arm()

    for (const event of ACTIVITY_EVENTS) {
      window.addEventListener(event, arm, { passive: true })
    }

    return () => {
      clearTimers()
      for (const event of ACTIVITY_EVENTS) {
        window.removeEventListener(event, arm)
      }
    }
  }, [enabled, config.warningMinutes, config.logoutMinutes])

  return { warning }
}
