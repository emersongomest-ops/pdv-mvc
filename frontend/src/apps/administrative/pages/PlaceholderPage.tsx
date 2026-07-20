import styles from './PlaceholderPage.module.css'

export function PlaceholderPage({ title, hint }: { title: string; hint: string }) {
  return (
    <div className={styles.page}>
      <h1>{title}</h1>
      <p>{hint}</p>
      <div className={`panel ${styles.panel}`}>Content area — next slice.</div>
    </div>
  )
}
