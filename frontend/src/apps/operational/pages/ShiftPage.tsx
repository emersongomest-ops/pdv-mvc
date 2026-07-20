import { useEffect, useState, type FormEvent } from 'react'
import { Navigate } from 'react-router-dom'
import { getCurrentShift, openShift } from '../../../shared/api/client'
import { formatApiError, useSession } from '../../../shared/session/SessionContext'
import styles from './ShiftPage.module.css'

export function ShiftPage() {
  const { user, store, shiftOpen, setShiftOpen, clearStore } = useSession()
  const [openingCash, setOpeningCash] = useState('100.00')
  const [error, setError] = useState<string | null>(null)
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)

  useEffect(() => {
    if (!user || !store) return
    getCurrentShift()
      .then((res) => {
        if (res.data.shift) {
          setShiftOpen(true)
        }
      })
      .catch((err) => setError(formatApiError(err)))
      .finally(() => setLoading(false))
  }, [user, store, setShiftOpen])

  if (!user) {
    return <Navigate to="/login" replace />
  }

  if (!store) {
    return <Navigate to="/store" replace />
  }

  if (shiftOpen) {
    return <Navigate to="/pos" replace />
  }

  async function onSubmit(event: FormEvent) {
    event.preventDefault()
    setSaving(true)
    setError(null)
    try {
      await openShift(Number(openingCash))
      setShiftOpen(true)
    } catch (err) {
      setError(formatApiError(err))
    } finally {
      setSaving(false)
    }
  }

  return (
    <div className={styles.page}>
      <form className={`panel ${styles.card}`} onSubmit={onSubmit}>
        <p className={styles.brand}>PDV</p>
        <h1>Open cash shift</h1>
        <p className={styles.meta}>
          Store <strong>{store.name}</strong> ({store.code})
        </p>

        {error ? <div className="error-banner">{error}</div> : null}
        {loading ? <p className={styles.meta}>Checking current shift…</p> : null}

        <div className="field">
          <label htmlFor="opening">Opening cash</label>
          <input
            id="opening"
            className="price"
            value={openingCash}
            onChange={(e) => setOpeningCash(e.target.value)}
            required
          />
        </div>

        <div className={styles.actions}>
          <button type="button" className="btn btn-ghost" onClick={clearStore} disabled={saving}>
            Change store
          </button>
          <button className="btn btn-primary" type="submit" disabled={saving || loading}>
            {saving ? 'Opening…' : 'Open shift'}
          </button>
        </div>
      </form>
    </div>
  )
}
