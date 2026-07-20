import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

declare global {
  interface Window {
    Pusher: typeof Pusher
  }
}

let echoInstance: Echo<'reverb'> | null = null

export function isRealtimeEnabled(): boolean {
  return Boolean(import.meta.env.VITE_REVERB_APP_KEY)
}

export function getEcho(): Echo<'reverb'> | null {
  const key = import.meta.env.VITE_REVERB_APP_KEY
  if (!key) {
    return null
  }

  if (echoInstance === null) {
    window.Pusher = Pusher

    const scheme = import.meta.env.VITE_REVERB_SCHEME ?? 'http'
    const port = Number(import.meta.env.VITE_REVERB_PORT ?? 8080)

    echoInstance = new Echo({
      broadcaster: 'reverb',
      key,
      wsHost: import.meta.env.VITE_REVERB_HOST ?? 'localhost',
      wsPort: port,
      wssPort: port,
      forceTLS: scheme === 'https',
      enabledTransports: ['ws', 'wss'],
      authEndpoint: '/broadcasting/auth',
    })
  }

  return echoInstance
}

export function disconnectEcho(): void {
  if (echoInstance !== null) {
    echoInstance.disconnect()
    echoInstance = null
  }
}
