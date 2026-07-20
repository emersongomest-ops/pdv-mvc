import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { closeShift, reconcileOperationalPayments } from '../../../shared/api/client'
import { formatApiError, useSession } from '../../../shared/session/SessionContext'
import { ErrorBanner } from '../../../shared/ui/ErrorBanner'
import { CartContainer } from '../containers/CartContainer'
import { CatalogContainer } from '../containers/CatalogContainer'
import { usePosActivity } from '../context/PosWorkspaceState'
import styles from './PosLayout.module.css'

type PosLayoutProps = {
  storeName: string
  userName: string
  shiftOpen: boolean
  onLogout: () => void
}

/** Dumb chrome + slots for smart containers; close-shift is POS shell concern. */
export function PosLayout({ storeName, userName, shiftOpen, onLogout }: PosLayoutProps) {
  const { error, message } = usePosActivity()
  const { setShiftOpen } = useSession()
  const navigate = useNavigate()
  const [closingCash, setClosingCash] = useState('')
  const [closing, setClosing] = useState(false)
  const [closeError, setCloseError] = useState<string | null>(null)
  const [reconciling, setReconciling] = useState(false)
  const [reconcileMsg, setReconcileMsg] = useState<string | null>(null)

  async function handleCloseShift() {
    setClosing(true)
    setCloseError(null)
    try {
      const amount = closingCash.trim() === '' ? undefined : Number(closingCash)
      if (amount !== undefined && (Number.isNaN(amount) || amount < 0)) {
        setCloseError('Closing cash must be a non-negative number.')
        return
      }
      await closeShift(amount)
      setShiftOpen(false)
      navigate('/shift', { replace: true })
    } catch (err) {
      setCloseError(formatApiError(err))
    } finally {
      setClosing(false)
    }
  }

  async function handleReconcilePayments() {
    setReconciling(true)
    setReconcileMsg(null)
    setCloseError(null)
    try {
      const { data } = await reconcileOperationalPayments()
      setReconcileMsg(
        `Payments updated: confirmed ${data.settlements_confirmed}, failed ${data.settlements_failed}, pending ${data.still_pending}, webhook retries ${data.webhook_retries_succeeded}.`,
      )
    } catch (err) {
      setCloseError(formatApiError(err))
    } finally {
      setReconciling(false)
    }
  }

  return (
    <div className={styles.page}>
      <header className={styles.topbar}>
        <div>
          <p className={styles.brand}>PDV</p>
          <h1>Checkout</h1>
          <p className={styles.meta}>
            {storeName} · {userName}
          </p>
        </div>
        <div className={styles.actions}>
          <button
            type="button"
            className="btn btn-ghost"
            onClick={() => void handleReconcilePayments()}
            disabled={reconciling || closing}
          >
            {reconciling ? 'Updating payments…' : 'Refresh payments'}
          </button>
          <label className={styles.closeField}>
            Closing cash
            <input
              type="number"
              min={0}
              step="0.01"
              placeholder="optional"
              value={closingCash}
              onChange={(event) => setClosingCash(event.target.value)}
              disabled={closing}
            />
          </label>
          <button
            type="button"
            className="btn"
            onClick={() => void handleCloseShift()}
            disabled={closing}
          >
            {closing ? 'Closing…' : 'Close shift'}
          </button>
          <button type="button" className="btn btn-ghost" onClick={onLogout} disabled={closing}>
            Sign out
          </button>
        </div>
      </header>

      <ErrorBanner message={error ?? closeError} />
      {message ? <div className={styles.success}>{message}</div> : null}
      {reconcileMsg ? <div className={styles.success}>{reconcileMsg}</div> : null}

      <div className={styles.layout}>
        <CatalogContainer shiftOpen={shiftOpen} />
        <CartContainer shiftOpen={shiftOpen} />
      </div>
    </div>
  )
}
