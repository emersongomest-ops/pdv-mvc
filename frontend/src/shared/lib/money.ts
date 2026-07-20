/**
 * API money is decimal strings ("13.00").
 * Domain/DB (backend) stores integer cents — conversion is server-side only.
 */

export function formatMoney(value: string | number | null | undefined, prefix = 'R$ '): string {
  if (value === null || value === undefined || value === '') {
    return `${prefix}0.00`
  }
  const n = typeof value === 'number' ? value : Number(value)
  if (!Number.isFinite(n)) {
    return `${prefix}${String(value)}`
  }
  return `${prefix}${n.toFixed(2)}`
}

/** Normalize user input to two-decimal string for API payloads. */
export function toApiDecimal(value: string): string {
  const normalized = value.trim().replace(',', '.')
  const n = Number(normalized)
  if (!Number.isFinite(n) || n < 0) {
    throw new Error('Invalid money amount')
  }
  return n.toFixed(2)
}

export function moneyDiff(a: string | number, b: string | number): number {
  return Number(a) - Number(b)
}
