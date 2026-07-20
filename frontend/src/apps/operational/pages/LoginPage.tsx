import { Navigate } from 'react-router-dom'
import { useLoginForm } from '../../../features/auth/hooks/useLoginForm'
import { LoginForm } from '../../../features/auth/ui/LoginForm'
import { useSession } from '../../../shared/session/SessionContext'

/** Smart page: session gate + login hook → dumb form. */
export function LoginPage() {
  const { user, authStatus } = useSession()
  const form = useLoginForm()

  if (authStatus === 'loading') {
    return <p className="auth-boot">Checking session…</p>
  }

  if (authStatus === 'authenticated' && user) {
    return <Navigate to={user.role === 'manager' ? '/admin' : '/store'} replace />
  }

  return (
    <LoginForm
      email={form.email}
      password={form.password}
      error={form.error}
      loading={form.loading}
      onEmailChange={form.setEmail}
      onPasswordChange={form.setPassword}
      onSubmit={form.onSubmit}
    />
  )
}
