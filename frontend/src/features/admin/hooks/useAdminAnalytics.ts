import { useCallback, useEffect, useState } from 'react'
import { getAdminAnalytics, listCampaignCustomers } from '../../../shared/api/client'
import type { AdminAnalytics, Customer } from '../../../shared/api/types'
import { formatApiError } from '../../../shared/session/SessionContext'

export function useAdminAnalytics() {
  const [analytics, setAnalytics] = useState<AdminAnalytics | null>(null)
  const [campaignCustomers, setCampaignCustomers] = useState<Customer[]>([])
  const [birthMonth, setBirthMonth] = useState('')
  const [region, setRegion] = useState('')
  const [loading, setLoading] = useState(false)
  const [campaignLoading, setCampaignLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)

  const loadAnalytics = useCallback(async () => {
    setLoading(true)
    setError(null)
    try {
      const response = await getAdminAnalytics({ registration_days: 30, top_customers: 15 })
      setAnalytics(response.data)
    } catch (err) {
      setAnalytics(null)
      setError(formatApiError(err))
    } finally {
      setLoading(false)
    }
  }, [])

  const loadCampaigns = useCallback(async () => {
    setCampaignLoading(true)
    setError(null)
    try {
      const response = await listCampaignCustomers({
        birth_month: birthMonth ? Number(birthMonth) : undefined,
        region: region.trim() || undefined,
      })
      setCampaignCustomers(response.data.customers)
    } catch (err) {
      setCampaignCustomers([])
      setError(formatApiError(err))
    } finally {
      setCampaignLoading(false)
    }
  }, [birthMonth, region])

  useEffect(() => {
    void loadAnalytics()
  }, [loadAnalytics])

  return {
    analytics,
    campaignCustomers,
    birthMonth,
    setBirthMonth,
    region,
    setRegion,
    loading,
    campaignLoading,
    error,
    loadAnalytics,
    loadCampaigns,
  }
}
