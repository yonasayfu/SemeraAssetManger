import { ref } from 'vue';

export interface AsyncOptions {
  onSuccess?: () => void;
  onError?: (e: unknown) => void;
  finally?: () => void;
}

export function useAsyncAction<T extends any[]>(
  fn: (...args: T) => Promise<void> | void,
  opts: AsyncOptions = {},
) {
  const loading = ref(false);
  const error = ref<unknown | null>(null);

  const run = async (...args: T) => {
    loading.value = true;
    error.value = null;
    try {
      await fn(...args);
      opts.onSuccess?.();
    } catch (e) {
      error.value = e;
      opts.onError?.(e);
      // Optional: console log for now; integrate with a toast system if present.
      // eslint-disable-next-line no-console
      console.error('[async]', e);
    } finally {
      loading.value = false;
      opts.finally?.();
    }
  };

  return { run, loading, error };
}
