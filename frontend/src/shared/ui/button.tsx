import type { ButtonHTMLAttributes } from 'react'
import { cn } from './cn'

const base =
  'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50'

const variants = {
  default: 'bg-primary text-primary-foreground shadow hover:bg-primary/90',
  destructive: 'bg-destructive text-destructive-foreground shadow-sm hover:bg-destructive/90',
  outline: 'border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground',
}

const sizes = {
  default: 'h-9 px-4 py-2',
  sm: 'h-8 rounded-md px-3 text-sm',
}

type ButtonVariant = keyof typeof variants
type ButtonSize = keyof typeof sizes

export function buttonClass(variant: ButtonVariant = 'default', size: ButtonSize = 'default', className?: string) {
  return cn(base, variants[variant], sizes[size], className)
}

type ButtonProps = ButtonHTMLAttributes<HTMLButtonElement> & {
  variant?: ButtonVariant
  size?: ButtonSize
}

export function Button({ className, variant = 'default', size = 'default', ...props }: ButtonProps) {
  return <button className={buttonClass(variant, size, className)} {...props} />
}
