import type { FormEvent } from 'react'
import { ErrorBanner } from '../../../shared/ui/ErrorBanner'
import { Field, PanelForm } from '../../../shared/ui/FormPrimitives'
import styles from './LoginForm.module.css'

export type LoginStep = 'credentials' | 'mfa_setup' | 'mfa_verify'

type LoginFormProps = {
  step: LoginStep
  email: string
  password: string
  mfaCode: string
  setupSecret: string | null
  setupQrDataUri: string | null
  error: string | null
  loading: boolean
  onEmailChange: (value: string) => void
  onPasswordChange: (value: string) => void
  onMfaCodeChange: (value: string) => void
  onSubmit: (event: FormEvent) => void
  onBackToCredentials: () => void
}

/** Presentational login card (dumb). */
export function LoginForm({
  step,
  email,
  password,
  mfaCode,
  setupSecret,
  setupQrDataUri,
  error,
  loading,
  onEmailChange,
  onPasswordChange,
  onMfaCodeChange,
  onSubmit,
  onBackToCredentials,
}: LoginFormProps) {
  const isMfa = step === 'mfa_setup' || step === 'mfa_verify'

  return (
    <div className={styles.page}>
      <PanelForm className={styles.card} onSubmit={onSubmit}>
        <p className={styles.brand}>PDV</p>
        <h1>{isMfa ? 'Authenticator code' : 'Operational login'}</h1>
        <p className={styles.hint}>
          {step === 'mfa_setup'
            ? 'Scan the QR with your authenticator app, then enter the 6-digit code.'
            : step === 'mfa_verify'
              ? 'Enter the 6-digit code from your authenticator app.'
              : 'Session cookie via Sanctum — store and shift next.'}
        </p>

        <ErrorBanner message={error} />

        {step === 'credentials' && (
          <>
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
          </>
        )}

        {step === 'mfa_setup' && setupQrDataUri && (
          <div className={styles.mfaSetup}>
            <img className={styles.qr} src={setupQrDataUri} alt="Authenticator QR code" width={220} height={220} />
            {setupSecret && (
              <p className={styles.secret}>
                Manual key: <code>{setupSecret}</code>
              </p>
            )}
          </div>
        )}

        {isMfa && (
          <Field id="mfa-code" label="Authentication code">
            <input
              id="mfa-code"
              type="text"
              inputMode="numeric"
              autoComplete="one-time-code"
              pattern="[0-9 ]*"
              maxLength={8}
              value={mfaCode}
              onChange={(e) => onMfaCodeChange(e.target.value)}
              required
              autoFocus
            />
          </Field>
        )}

        <button className="btn btn-primary" type="submit" disabled={loading}>
          {loading
            ? 'Please wait…'
            : step === 'credentials'
              ? 'Sign in'
              : step === 'mfa_setup'
                ? 'Confirm and continue'
                : 'Verify and continue'}
        </button>

        {isMfa && (
          <button className="btn" type="button" disabled={loading} onClick={onBackToCredentials}>
            Back
          </button>
        )}
      </PanelForm>
    </div>
  )
}
