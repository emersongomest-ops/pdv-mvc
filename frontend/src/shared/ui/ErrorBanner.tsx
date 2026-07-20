type ErrorBannerProps = {
  message: string | null
}

/** Presentational error banner (dumb). */
export function ErrorBanner({ message }: ErrorBannerProps) {
  if (!message) return null
  return <div className="error-banner">{message}</div>
}
