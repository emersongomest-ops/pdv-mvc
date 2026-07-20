import type { FormEvent } from 'react'
import { ErrorBanner } from '../../../shared/ui/ErrorBanner'
import { Field, PanelForm } from '../../../shared/ui/FormPrimitives'
import styles from './LoginForm.module.css'
import { TurnstileWidget } from './TurnstileWidget'

export type LoginStep = 'credentials' | 'mfa_setup' | 'mfa_verify' | 'mfa_recovery'

type LoginFormProps = {
  step: LoginStep
  email: string
  password: string
  mfaCode: string
  setupSecret: string | null
  setupQrDataUri: string | null
  recoveryCodes: string[] | null
  error: string | null
  loading: boolean
  captchaRequired: boolean
  turnstileSiteKey: string | null
  onEmailChange: (value: string) => void
  onPasswordChange: (value: string) => void
  onMfaCodeChange: (value: string) => void
  onTurnstileToken: (token: string | null) => void
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
  recoveryCodes,
  error,
  loading,
  captchaRequired,
  turnstileSiteKey,
  onEmailChange,
  onPasswordChange,
  onMfaCodeChange,
  onTurnstileToken,
  onSubmit,
  onBackToCredentials,
}: LoginFormProps) {
  const isMfaCode = step === 'mfa_setup' || step === 'mfa_verify'
  const isRecovery = step === 'mfa_recovery'
  const showCaptcha = step === 'credentials' && captchaRequired && Boolean(turnstileSiteKey)

  return (
    <div className={styles.page}>
      <PanelForm className={styles.card} onSubmit={onSubmit}>
        <p className={styles.brand}>PDV</p>
        <h1>
          {isRecovery
            ? 'Save recovery codes'
            : isMfaCode
              ? 'Authenticator code'
              : 'Operational login'}
        </h1>
        <p className={styles.hint}>
          {step === 'mfa_setup'
            ? 'Scan the QR with your authenticator app, then enter the 6-digit code.'
            : step === 'mfa_verify'
              ? 'Enter the 6-digit TOTP or a one-time recovery code.'
              : step === 'mfa_recovery'
                ? 'Store these codes offline. Each works once if you lose the authenticator.'
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

            {showCaptcha && turnstileSiteKey && (
              <div className={styles.captcha}>
                <TurnstileWidget siteKey={turnstileSiteKey} onToken={onTurnstileToken} />
              </div>
            )}

            {captchaRequired && !turnstileSiteKey && (
              <p className={styles.hint} role="alert">
                CAPTCHA required but site key is not configured (TURNSTILE_SITE_KEY /
                VITE_TURNSTILE_SITE_KEY).
              </p>
            )}
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

        {isRecovery && recoveryCodes && recoveryCodes.length > 0 && (
          <ul className={styles.recoveryList}>
            {recoveryCodes.map((code) => (
              <li key={code}>
                <code>{code}</code>
              </li>
            ))}
          </ul>
        )}

        {isMfaCode && (
          <Field id="mfa-code" label={step === 'mfa_verify' ? 'Code or recovery' : 'Authentication code'}>
            <input
              id="mfa-code"
              type="text"
              inputMode={step === 'mfa_setup' ? 'numeric' : 'text'}
              autoComplete="one-time-code"
              maxLength={16}
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
                : step === 'mfa_recovery'
                  ? 'I saved these codes'
                  : 'Verify and continue'}
        </button>

        {(isMfaCode || isRecovery) && (
          <button className="btn" type="button" disabled={loading} onClick={onBackToCredentials}>
            Back
          </button>
        )}
      </PanelForm>
    </div>
  )
}
