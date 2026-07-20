import { Navigate, Route, Routes } from 'react-router-dom'
import { RequireManager } from './apps/administrative/guards/RequireManager'
import { AnalyticsPage } from './apps/administrative/pages/AnalyticsPage'
import { AuditLogPage } from './apps/administrative/pages/AuditLogPage'
import { CatalogPage } from './apps/administrative/pages/CatalogPage'
import { CustomersPage } from './apps/administrative/pages/CustomersPage'
import { DashboardPage } from './apps/administrative/pages/DashboardPage'
import { InventoryPage } from './apps/administrative/pages/InventoryPage'
import { PromotionsPage } from './apps/administrative/pages/PromotionsPage'
import { RefundsPage } from './apps/administrative/pages/RefundsPage'
import { SalesPage } from './apps/administrative/pages/SalesPage'
import { ShiftsPage } from './apps/administrative/pages/ShiftsPage'
import { UsersPage } from './apps/administrative/pages/UsersPage'
import { RequireAuth } from './apps/operational/guards/RequireAuth'
import { LoginPage } from './apps/operational/pages/LoginPage'
import { PosPage } from './apps/operational/pages/PosPage'
import { ShiftPage } from './apps/operational/pages/ShiftPage'
import { StoreSelectPage } from './apps/operational/pages/StoreSelectPage'
import { AdminShell } from './features/admin/ui/AdminShell'
import { SessionProvider, useSession } from './shared/session/SessionContext'

function HomeRedirect() {
  const { user, store, shiftOpen, authStatus } = useSession()

  if (authStatus === 'loading') {
    return <p className="auth-boot">Checking session…</p>
  }
  if (authStatus === 'anonymous' || !user) {
    return <Navigate to="/login" replace />
  }
  if (user.role === 'manager') {
    return <Navigate to="/admin" replace />
  }
  if (!store) {
    return <Navigate to="/store" replace />
  }
  if (!shiftOpen) {
    return <Navigate to="/shift" replace />
  }
  return <Navigate to="/pos" replace />
}

export default function App() {
  return (
    <SessionProvider>
      <div className="app-shell">
        <Routes>
          <Route path="/" element={<HomeRedirect />} />
          <Route path="/login" element={<LoginPage />} />
          <Route
            path="/store"
            element={
              <RequireAuth>
                <StoreSelectPage />
              </RequireAuth>
            }
          />
          <Route
            path="/shift"
            element={
              <RequireAuth>
                <ShiftPage />
              </RequireAuth>
            }
          />
          <Route
            path="/pos"
            element={
              <RequireAuth>
                <PosPage />
              </RequireAuth>
            }
          />

          <Route
            path="/admin"
            element={
              <RequireAuth>
                <RequireManager>
                  <AdminShell />
                </RequireManager>
              </RequireAuth>
            }
          >
            <Route index element={<DashboardPage />} />
            <Route path="analytics" element={<AnalyticsPage />} />
            <Route path="catalog" element={<CatalogPage />} />
            <Route path="sales" element={<SalesPage />} />
            <Route path="shifts" element={<ShiftsPage />} />
            <Route path="users" element={<UsersPage />} />
            <Route path="customers" element={<CustomersPage />} />
            <Route path="promotions" element={<PromotionsPage />} />
            <Route path="inventory" element={<InventoryPage />} />
            <Route path="refunds" element={<RefundsPage />} />
            <Route path="audit-log" element={<AuditLogPage />} />
          </Route>

          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </div>
    </SessionProvider>
  )
}
