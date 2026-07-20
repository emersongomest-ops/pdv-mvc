import { useEffect, useState, type FormEvent } from 'react'
import {
  beginMfaSetupRequest,
  confirmMfaSetupRequest,
  primeCsrf,
  verifyMfaChallengeRequest,
  ApiClientError,
} from '../../../shared/api/client'
import type { User } from '../../../shared/api/types'
import { formatApiError, useSession } from '../../../shared/session/SessionContext'
import type { LoginStep } from '../ui/LoginForm'

const FALLBACK_SITE_KEY = import.meta.env.VITE_TURNSTILE_SITE_KEY as string | undefined

export function useLoginForm() {
  const { login, establishSession } = useSession()
  const [step, setStep] = useState<LoginStep>('credentials')
  const [email, setEmail] = useState('operator@pos.test')
  const [password, setPassword] = useState('password')
  const [mfaCode, setMfaCode] = useState('')
  const [setupSecret, setSetupSecret] = useState<string | null>(null)
  const [setupQrDataUri, setSetupQrDataUri] = useState<string | null>(null)
  const [recoveryCodes, setRecoveryCodes] = useState<string[] | null>(null)
  const [pendingUser, setPendingUser] = useState<User | null>(null)
  const [error, setError] = useState<string | null>(null)
  const [loading, setLoading] = useState(false)
  const [captchaRequired, setCaptchaRequired] = useState(false)
  const [turnstileSiteKey, setTurnstileSiteKey] = useState<string | null>(
    FALLBACK_SITE_KEY && FALLBACK_SITE_KEY.length > 0 ? FALLBACK_SITE_KEY : null,
  )
  const [turnstileToken, setTurnstileToken] = useState<string | null>(null)

  useEffect(() => {
    void primeCsrf().catch(() => undefined)
  }, [])

  function applyCaptchaFromError(err: unknown) {
    if (!(err instanceof ApiClientError)) return
    const ctx = err.error.context
    if (ctx?.captcha_required === true) {
      setCaptchaRequired(true)
      if (ctx.turnstile_site_key) {
        setTurnstileSiteKey(ctx.turnstile_site_key)
      }
    }
    if (
      err.error.code === 'AUTH_CAPTCHA_REQUIRED' ||
      err.error.code === 'AUTH_CAPTCHA_INVALID'
    ) {
      setCaptchaRequired(true)
      setTurnstileToken(null)
    }
  }

  function resetMfaUi() {
    setStep('credentials')
    setMfaCode('')
    setSetupSecret(null)
    setSetupQrDataUri(null)
    setRecoveryCodes(null)
    setPendingUser(null)
  }

  async function onSubmit(event: FormEvent) {
    event.preventDefault()
    setError(null)
    setLoading(true)
    try {
      if (step === 'credentials') {
        const outcome = await login(email, password, turnstileToken)
        setCaptchaRequired(false)
        setTurnstileToken(null)
        if (outcome.status === 'authenticated') {
          return
        }
        if (outcome.status === 'mfa_setup_required') {
          const setup = await beginMfaSetupRequest()
          setSetupSecret(setup.data.secret)
          setSetupQrDataUri(setup.data.qr_data_uri)
          setStep('mfa_setup')
          return
        }
        setStep('mfa_verify')
        return
      }

      if (step === 'mfa_setup') {
        const response = await confirmMfaSetupRequest(mfaCode)
        setRecoveryCodes(response.data.recovery_codes)
        setPendingUser(response.data.user)
        setMfaCode('')
        setStep('mfa_recovery')
        return
      }

      if (step === 'mfa_recovery') {
        if (pendingUser) {
          establishSession(pendingUser)
        }
        return
      }

      const response = await verifyMfaChallengeRequest(mfaCode)
      establishSession(response.data.user)
    } catch (err) {
      applyCaptchaFromError(err)
      setError(formatApiError(err))
    } finally {
      setLoading(false)
    }
  }

  return {
    step,
    email,
    setEmail,
    password,
    setPassword,
    mfaCode,
    setMfaCode,
    setupSecret,
    setupQrDataUri,
    recoveryCodes,
    error,
    loading,
    captchaRequired,
    turnstileSiteKey,
    setTurnstileToken,
    onSubmit,
    onBackToCredentials: resetMfaUi,
  }
}
