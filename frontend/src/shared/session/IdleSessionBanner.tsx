import styles from './IdleSessionBanner.module.css'

type IdleSessionBannerProps = {
  visible: boolean
  logoutMinutes: number
  onStay: () => void
  onLogout: () => void
}

/** Warning strip when the idle timer is about to force logout. */
export function IdleSessionBanner({
  visible,
  logoutMinutes,
  onStay,
  onLogout,
}: IdleSessionBannerProps) {
  if (!visible) return null

  return (
    <div className={styles.banner} role="alertdialog" aria-labelledby="idle-session-title">
      <div className={styles.panel}>
        <p id="idle-session-title" className={styles.title}>
          Session idle
        </p>
        <p className={styles.body}>
          No activity detected. You will be signed out after {logoutMinutes} minutes of idle time
          (shared POS lock). Move the mouse or press a key to stay signed in.
        </p>
        <div className={styles.actions}>
          <button type="button" className="btn btn-primary" onClick={onStay}>
            Stay signed in
          </button>
          <button type="button" className="btn" onClick={onLogout}>
            Sign out now
          </button>
        </div>
      </div>
    </div>
  )
}
