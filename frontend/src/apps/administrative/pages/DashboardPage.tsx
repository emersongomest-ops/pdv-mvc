import { useAdminDashboard } from '../../../features/admin/hooks/useAdminDashboard'
import { DashboardView } from '../../../features/admin/ui/DashboardView'

/** Smart page: dashboard data → dumb view. */
export function DashboardPage() {
  const data = useAdminDashboard()
  return <DashboardView {...data} />
}
