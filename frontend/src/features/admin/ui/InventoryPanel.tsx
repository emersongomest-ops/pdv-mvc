import type { AdminProduct, Store, StoreInventoryRow } from '../../../shared/api/types'
import type { InventoryAdjustForm } from '../hooks/useAdminInventory'
import styles from './UsersPanel.module.css'

type InventoryPanelProps = {
  stores: Store[]
  products: AdminProduct[]
  storeId: number | null
  onStoreChange: (storeId: number) => void
  rows: StoreInventoryRow[]
  form: InventoryAdjustForm
  onFormChange: <K extends keyof InventoryAdjustForm>(key: K, value: InventoryAdjustForm[K]) => void
  onSave: () => void
  loading: boolean
  saving: boolean
  success: string | null
}

export function InventoryPanel({
  stores,
  products,
  storeId,
  onStoreChange,
  rows,
  form,
  onFormChange,
  onSave,
  loading,
  saving,
  success,
}: InventoryPanelProps) {
  return (
    <div className={styles.stack}>
      <div className={`panel ${styles.toolbar}`}>
        <label>
          Store
          <select
            value={storeId ?? ''}
            onChange={(e) => onStoreChange(Number(e.target.value))}
            disabled={stores.length === 0}
          >
            {stores.length === 0 ? <option value="">No stores</option> : null}
            {stores.map((store) => (
              <option key={store.id} value={store.id}>
                {store.code} — {store.name}
              </option>
            ))}
          </select>
        </label>
      </div>

      <section className={`panel ${styles.list}`}>
        <h2>Stock levels</h2>
        {loading ? <p className={styles.empty}>Loading…</p> : null}
        {!loading && rows.length === 0 ? (
          <p className={styles.empty}>No inventory rows for this store.</p>
        ) : null}
        {!loading && rows.length > 0 ? (
          <table>
            <thead>
              <tr>
                <th>SKU</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Track</th>
              </tr>
            </thead>
            <tbody>
              {rows.map((row) => (
                <tr key={`${row.store_id}-${row.product_id}`}>
                  <td className={styles.mono}>{row.product_sku ?? '—'}</td>
                  <td>{row.product_name ?? `#${row.product_id}`}</td>
                  <td className={styles.mono}>{row.quantity}</td>
                  <td>{row.track_stock ? 'Yes' : 'No'}</td>
                </tr>
              ))}
            </tbody>
          </table>
        ) : null}
      </section>

      <section className={`panel ${styles.form}`}>
        <h2>Adjust stock</h2>
        <p className={styles.hint}>Sets absolute quantity. Reason required (RN-023 / RN-070).</p>
        {success ? <p className={styles.success}>{success}</p> : null}
        <div className={styles.grid}>
          <label>
            Product
            <select
              value={form.product_id === '' ? '' : String(form.product_id)}
              onChange={(e) =>
                onFormChange('product_id', e.target.value === '' ? '' : Number(e.target.value))
              }
            >
              <option value="">Select…</option>
              {products.map((product) => (
                <option key={product.id} value={product.id}>
                  {product.sku} — {product.name}
                </option>
              ))}
            </select>
          </label>
          <label>
            Quantity
            <input
              type="number"
              min={0}
              value={form.quantity}
              onChange={(e) => onFormChange('quantity', e.target.value)}
            />
          </label>
          <label>
            Reason
            <input
              value={form.reason}
              onChange={(e) => onFormChange('reason', e.target.value)}
              placeholder="e.g. cycle count"
            />
          </label>
        </div>
        <button type="button" className="btn" disabled={saving || storeId === null} onClick={onSave}>
          {saving ? 'Saving…' : 'Apply adjustment'}
        </button>
      </section>
    </div>
  )
}
