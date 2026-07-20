import type { FormEvent } from 'react'
import { ErrorBanner } from '../../../shared/ui/ErrorBanner'
import { Field, PanelForm } from '../../../shared/ui/FormPrimitives'
import styles from './LoginForm.module.css'

type LoginFormProps = {
  email: string
  password: string
  error: string | null
  loading: boolean
  onEmailChange: (value: string) => void
  onPasswordChange: (value: string) => void
  onSubmit: (event: FormEvent) => void
}

/** Presentational login card (dumb). */
export function LoginForm({
  email,
  password,
  error,
  loading,
  onEmailChange,
  onPasswordChange,
  onSubmit,
}: LoginFormProps) {
  return (
    <div className={styles.page}>
      <PanelForm className={styles.card} onSubmit={onSubmit}>
        <p className={styles.brand}>PDV</p>
        <h1>Operational login</h1>
        <p className={styles.hint}>Session cookie via Sanctum — store and shift next.</p>

        <ErrorBanner message={error} />

        <Field id="email" label="Email">
          <input
            id="email"
            type="email"
            autoComplete="username"
            value={email}
            onChange={(e) => onEmailChange(e.target.value)}
            required
          />
        </Field>

        <Field id="password" label="Password">
          <input
            id="password"
            type="password"
            autoComplete="current-password"
            value={password}
            onChange={(e) => onPasswordChange(e.target.value)}
            required
          />
        </Field>

        <button className="btn btn-primary" type="submit" disabled={loading}>
          {loading ? 'Signing in…' : 'Sign in'}
        </button>
      </PanelForm>
    </div>
  )
}
