import { useEffect, useState, type FormEvent } from 'react'
import { primeCsrf } from '../../../shared/api/client'
import { formatApiError, useSession } from '../../../shared/session/SessionContext'

export function useLoginForm() {
  const { login } = useSession()
  const [email, setEmail] = useState('operator@pos.test')
  const [password, setPassword] = useState('password')
  const [error, setError] = useState<string | null>(null)
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    // Warm the Sanctum cookie before submit; loginRequest retries if this fails.
    void primeCsrf().catch(() => undefined)
  }, [])

  async function onSubmit(event: FormEvent) {
    event.preventDefault()
    setError(null)
    setLoading(true)
    try {
      await login(email, password)
    } catch (err) {
      setError(formatApiError(err))
    } finally {
      setLoading(false)
    }
  }

  return {
    email,
    setEmail,
    password,
    setPassword,
    error,
    loading,
    onSubmit,
  }
}
