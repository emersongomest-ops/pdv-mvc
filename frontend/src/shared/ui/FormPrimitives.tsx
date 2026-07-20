import type { FormEvent, ReactNode } from 'react'

type FieldProps = {
  id: string
  label: string
  children: ReactNode
}

export function Field({ id, label, children }: FieldProps) {
  return (
    <div className="field">
      <label htmlFor={id}>{label}</label>
      {children}
    </div>
  )
}

type PanelFormProps = {
  className?: string
  onSubmit: (event: FormEvent) => void
  children: ReactNode
}

export function PanelForm({ className = '', onSubmit, children }: PanelFormProps) {
  return (
    <form className={`panel ${className}`} onSubmit={onSubmit}>
      {children}
    </form>
  )
}
