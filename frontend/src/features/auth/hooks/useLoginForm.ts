import { useEffect, useState, type FormEvent } from 'react'
import {
  beginMfaSetupRequest,
  confirmMfaSetupRequest,
  primeCsrf,
  verifyMfaChallengeRequest,
} from '../../../shared/api/client'
import type { User } from '../../../shared/api/types'
import { formatApiError, useSession } from '../../../shared/session/SessionContext'
import type { LoginStep } from '../ui/LoginForm'

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

  useEffect(() => {
    void primeCsrf().catch(() => undefined)
  }, [])

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
        const outcome = await login(email, password)
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
    onSubmit,
    onBackToCredentials: resetMfaUi,
  }
}
