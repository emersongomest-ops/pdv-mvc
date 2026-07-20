import { useEffect } from 'react'
import { getEcho, isRealtimeEnabled } from '../../../shared/realtime/echo'

export type RealtimeNotificationPayload = {
  id?: string
  kind?: string
  sale_id?: number
  store_id?: number
  operator_id?: number
  total?: string
  message?: string
}

export function useAdminRealtimeNotifications(
  userId: number | undefined,
  onNotification: (payload: RealtimeNotificationPayload) => void,
): void {
  useEffect(() => {
    if (!userId || !isRealtimeEnabled()) {
      return
    }

    const echo = getEcho()
    if (!echo) {
      return
    }

    const channelName = `App.Models.User.${userId}`
    const channel = echo.private(channelName)

    channel.notification((notification: RealtimeNotificationPayload) => {
      onNotification(notification)
    })

    return () => {
      echo.leave(channelName)
    }
  }, [onNotification, userId])
}
