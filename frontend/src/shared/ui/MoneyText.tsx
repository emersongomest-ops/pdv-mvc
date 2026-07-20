import { formatMoney } from '../lib/money'

type MoneyTextProps = {
  value: string | number | null | undefined
  prefix?: string
  className?: string
}

/** Presentational money display (dumb). */
export function MoneyText({ value, prefix = 'R$ ', className = 'price' }: MoneyTextProps) {
  return <span className={className}>{formatMoney(value, prefix)}</span>
}
