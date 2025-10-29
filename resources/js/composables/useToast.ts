import { eventBus } from '@/lib/eventBus';

export type ToastStyle = 'success' | 'danger' | 'warning' | 'info';

export function useToast() {
  const show = (message: string, style: ToastStyle = 'success') => {
    eventBus.emit('toast:show', { message, style });
  };

  return { show };
}
