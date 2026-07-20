import { usePosCustomerState } from '../context/PosWorkspaceState'
import { usePosCatalog } from '../hooks/usePosCatalog'
import { usePosHeld } from '../hooks/usePosHeld'
import { CatalogPanel } from '../components/CatalogPanel'

/** Smart: wires catalog + held resume → CatalogPanel. */
export function CatalogContainer({ shiftOpen }: { shiftOpen: boolean }) {
  const { setCustomer } = usePosCustomerState()
  const catalog = usePosCatalog(shiftOpen)
  const held = usePosHeld(shiftOpen, { loadOnMount: true })

  async function handleResume(sale: Parameters<typeof held.resumeHeld>[0]) {
    await held.resumeHeld(sale)
    setCustomer(null)
  }

  return (
    <CatalogPanel
      products={catalog.products}
      search={catalog.search}
      heldSales={held.heldSales}
      busy={catalog.busy}
      hasMore={catalog.nextCursor !== null}
      loadingMore={catalog.loadingMore}
      onSearchChange={catalog.setSearch}
      onAddProduct={(p) => void catalog.addProduct(p)}
      onResumeHeld={(s) => void handleResume(s)}
      onLoadMore={() => void catalog.loadMore()}
    />
  )
}
