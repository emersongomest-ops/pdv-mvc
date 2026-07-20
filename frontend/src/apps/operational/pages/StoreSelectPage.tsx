import { useEffect, useState } from 'react'
import { Navigate } from 'react-router-dom'
import { listStores, selectStore } from '../../../shared/api/client'
import type { Store } from '../../../shared/api/types'
import { formatApiError, useSession } from '../../../shared/session/SessionContext'
import styles from './StoreSelectPage.module.css'

export function StoreSelectPage() {
  const { user, store, setStore, logout } = useSession()
  const [stores, setStores] = useState<Store[]>([])
  const [error, setError] = useState<string | null>(null)
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)

  useEffect(() => {
    if (!user) return
    listStores()
      .then((res) => setStores(res.data.stores))
      .catch((err) => setError(formatApiError(err)))
      .finally(() => setLoading(false))
  }, [user])

  if (!user) {
    return <Navigate to="/login" replace />
  }

  if (store) {
    return <Navigate to="/shift" replace />
  }

  async function choose(next: Store) {
    setSaving(true)
    setError(null)
    try {
      await selectStore(next.id)
      setStore(next)
    } catch (err) {
      setError(formatApiError(err))
    } finally {
      setSaving(false)
    }
  }

  return (
    <div className={styles.page}>
      <header className={styles.header}>
        <div>
          <p className={styles.brand}>PDV</p>
          <h1>Select store</h1>
          <p className={styles.meta}>{user.name} · {user.role}</p>
        </div>
        <button type="button" className="btn btn-ghost" onClick={() => void logout()}>
          Sign out
        </button>
      </header>

      {error ? <div className="error-banner">{error}</div> : null}
      {loading ? <p className={styles.meta}>Loading stores…</p> : null}

      <div className={styles.grid}>
        {stores.map((item) => (
          <button
            key={item.id}
            type="button"
            className={`panel ${styles.store}`}
            disabled={saving}
            onClick={() => void choose(item)}
          >
            <span className={styles.code}>{item.code}</span>
            <strong>{item.name}</strong>
          </button>
        ))}
      </div>
    </div>
  )
}
