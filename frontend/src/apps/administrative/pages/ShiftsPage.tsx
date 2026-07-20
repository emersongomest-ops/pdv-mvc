import { useAdminShifts } from '../../../features/admin/hooks/useAdminShifts'
import { ShiftsPanel } from '../../../features/admin/ui/ShiftsPanel'
import { ErrorBanner } from '../../../shared/ui/ErrorBanner'
import styles from './CatalogPage.module.css'

/** Smart page: admin shift list + closing report (RN-063). */
export function ShiftsPage() {
  const data = useAdminShifts()

  return (
    <div className={styles.page}>
      <header className={styles.header}>
        <div>
          <h1>Shifts</h1>
          <p>
            Closing reports per store — sales totals, payment mix, cash variance. Managers may reopen
            closed shifts (RN-003/004/063).
          </p>
        </div>
      </header>

      <ErrorBanner message={data.error} />

      <ShiftsPanel
        stores={data.stores}
        storeIdInput={data.storeIdInput}
        onStoreIdChange={data.setStoreIdInput}
        onLoadShifts={() => void data.loadShifts()}
        shifts={data.shifts}
        report={data.report}
        selectedShiftId={data.selectedShiftId}
        onSelectShift={(id) => void data.loadReport(id)}
        onReopenShift={(id) => void data.reopenShift(id)}
        loading={data.loading}
        reopeningId={data.reopeningId}
        success={data.success}
      />
    </div>
  )
}
