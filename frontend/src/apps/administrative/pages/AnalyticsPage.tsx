import { useAdminAnalytics } from '../../../features/admin/hooks/useAdminAnalytics'
import { AnalyticsPanel } from '../../../features/admin/ui/AnalyticsPanel'
import { ErrorBanner } from '../../../shared/ui/ErrorBanner'
import styles from './CatalogPage.module.css'

/** Smart page: analytics + campaign filters (RN-080–084). */
export function AnalyticsPage() {
  const data = useAdminAnalytics()

  return (
    <div className={styles.page}>
      <header className={styles.header}>
        <div>
          <h1>Analytics</h1>
          <p>
            Registrations over time, recurrence index, spend by store, birthday and regional
            campaign filters (RN-080–084).
          </p>
        </div>
      </header>

      <ErrorBanner message={data.error} />

      <AnalyticsPanel
        analytics={data.analytics}
        campaignCustomers={data.campaignCustomers}
        birthMonth={data.birthMonth}
        region={data.region}
        onBirthMonthChange={data.setBirthMonth}
        onRegionChange={data.setRegion}
        onRunCampaignFilter={() => void data.loadCampaigns()}
        loading={data.loading}
        campaignLoading={data.campaignLoading}
      />
    </div>
  )
}
