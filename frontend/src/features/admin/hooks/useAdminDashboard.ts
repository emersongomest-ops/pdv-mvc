import { useCallback, useEffect, useState } from 'react'
import { getAdminDashboard, listAdminNotifications } from '../../../shared/api/client'
import type { AdminDashboardMetrics, AdminNotification } from '../../../shared/api/types'
import { formatApiError, useSession } from '../../../shared/session/SessionContext'
import {
  useAdminRealtimeNotifications,
  type RealtimeNotificationPayload,
} from './useAdminRealtimeNotifications'

function toAdminNotification(payload: RealtimeNotificationPayload): AdminNotification {
  return {
    id: payload.id ?? crypto.randomUUID(),
    kind: payload.kind ?? 'sale.completed',
    data: {
      kind: payload.kind,
      sale_id: payload.sale_id,
      store_id: payload.store_id,
      operator_id: payload.operator_id,
      total: payload.total,
      message: payload.message,
    },
    read_at: null,
    created_at: new Date().toISOString(),
  }
}

export function useAdminDashboard() {
  const { user } = useSession()
  const [message, setMessage] = useState<string | null>(null)
  const [metrics, setMetrics] = useState<AdminDashboardMetrics | null>(null)
  const [notifications, setNotifications] = useState<AdminNotification[]>([])
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    Promise.all([getAdminDashboard(), listAdminNotifications()])
      .then(([dash, notif]) => {
        setMessage(dash.data.message)
        setMetrics(dash.data.metrics)
        setNotifications(notif.data.notifications)
      })
      .catch((err) => setError(formatApiError(err)))
  }, [])

  const handleRealtimeNotification = useCallback((payload: RealtimeNotificationPayload) => {
    const next = toAdminNotification(payload)
    setNotifications((current) => {
      if (current.some((item) => item.id === next.id)) {
        return current
      }
      return [next, ...current]
    })
  }, [])

  useAdminRealtimeNotifications(
    user?.role === 'manager' ? user.id : undefined,
    handleRealtimeNotification,
  )

  return {
    message,
    productCount: metrics?.products_total ?? null,
    activeCount: metrics?.products_active ?? null,
    inactive: metrics?.products_inactive ?? null,
    customersTotal: metrics?.customers_total ?? null,
    salesCompleted: metrics?.sales_completed ?? null,
    openShifts: metrics?.open_shifts ?? null,
    notifications,
    error,
  }
}
