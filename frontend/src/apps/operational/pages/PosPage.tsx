import { Navigate } from 'react-router-dom'
import { PosLayout } from '../../../features/pos/components/PosLayout'
import { PosWorkspaceProvider } from '../../../features/pos/context/PosWorkspaceContext'
import { useSession } from '../../../shared/session/SessionContext'

/** Gate + provider; layout owns smart containers. */
export function PosPage() {
  const { user, store, shiftOpen, logout } = useSession()

  if (!user) {
    return <Navigate to="/login" replace />
  }
  if (!store) {
    return <Navigate to="/store" replace />
  }
  if (!shiftOpen) {
    return <Navigate to="/shift" replace />
  }

  return (
    <PosWorkspaceProvider>
      <PosLayout
        storeName={store.name}
        userName={user.name}
        shiftOpen={shiftOpen}
        onLogout={() => void logout()}
      />
    </PosWorkspaceProvider>
  )
}
