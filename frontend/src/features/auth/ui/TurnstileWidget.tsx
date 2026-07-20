import { useEffect, useRef } from 'react'

const SCRIPT_ID = 'cf-turnstile-api'
const SCRIPT_SRC = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit'

type TurnstileApi = {
  render: (
    container: HTMLElement,
    options: {
      sitekey: string
      callback: (token: string) => void
      'error-callback'?: () => void
      'expired-callback'?: () => void
      theme?: 'light' | 'dark' | 'auto'
    },
  ) => string
  remove: (widgetId: string) => void
  reset: (widgetId?: string) => void
}

declare global {
  interface Window {
    turnstile?: TurnstileApi
  }
}

function loadTurnstileScript(): Promise<TurnstileApi> {
  if (window.turnstile) {
    return Promise.resolve(window.turnstile)
  }

  return new Promise((resolve, reject) => {
    const existing = document.getElementById(SCRIPT_ID) as HTMLScriptElement | null
    if (existing) {
      existing.addEventListener('load', () => {
        if (window.turnstile) resolve(window.turnstile)
        else reject(new Error('Turnstile failed to load'))
      })
      existing.addEventListener('error', () => reject(new Error('Turnstile script error')))
      return
    }

    const script = document.createElement('script')
    script.id = SCRIPT_ID
    script.src = SCRIPT_SRC
    script.async = true
    script.onload = () => {
      if (window.turnstile) resolve(window.turnstile)
      else reject(new Error('Turnstile failed to load'))
    }
    script.onerror = () => reject(new Error('Turnstile script error'))
    document.head.appendChild(script)
  })
}

type TurnstileWidgetProps = {
  siteKey: string
  onToken: (token: string | null) => void
}

/** Explicit Cloudflare Turnstile widget (shown after login failure threshold). */
export function TurnstileWidget({ siteKey, onToken }: TurnstileWidgetProps) {
  const containerRef = useRef<HTMLDivElement>(null)
  const widgetIdRef = useRef<string | null>(null)

  useEffect(() => {
    let cancelled = false
    onToken(null)

    void loadTurnstileScript()
      .then((api) => {
        if (cancelled || !containerRef.current) return
        if (widgetIdRef.current) {
          api.remove(widgetIdRef.current)
          widgetIdRef.current = null
        }
        containerRef.current.innerHTML = ''
        widgetIdRef.current = api.render(containerRef.current, {
          sitekey: siteKey,
          callback: (token) => onToken(token),
          'error-callback': () => onToken(null),
          'expired-callback': () => onToken(null),
          theme: 'auto',
        })
      })
      .catch(() => {
        if (!cancelled) onToken(null)
      })

    return () => {
      cancelled = true
      if (widgetIdRef.current && window.turnstile) {
        window.turnstile.remove(widgetIdRef.current)
        widgetIdRef.current = null
      }
    }
  }, [siteKey, onToken])

  return <div ref={containerRef} data-testid="turnstile-widget" />
}
