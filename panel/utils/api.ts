import defu from 'defu';
import each from 'lodash/each';
import omit from 'lodash/omit';
import type { ValidationErrors } from 'nuxt-laravel-precognition/dist/runtime/types/core';
import type { MaybeRef, Ref } from 'vue';

import type { UseFetchOptions } from '#app';
import type { Form, FormError } from '#ui/types';
export function withDefaultFetchOptions<ResT>(
  options: UseFetchOptions<ResT> = {},
): UseFetchOptions<ResT> {
  const runtimeConfig = useRuntimeConfig();

  return defu({}, omit(options, ['noProgress']), {
    baseURL: runtimeConfig.public.api.baseUrl,
    headers: {
      Accept: 'application/json',
      'X-Timezone': Intl.DateTimeFormat().resolvedOptions().timeZone,
    },
  } as UseFetchOptions<ResT>);
}

export function handleValidationError<Schema>(
  e: ValidationErrors,
  form: Ref<Form<Schema> | undefined>,
): void {
  const errors: FormError[] = [];

  each(e, (e, path) => {
    errors.push({
      path,
      message: e.join(' '),
    });
  });

  form.value?.setErrors(errors);
}

export function handleApiError<Schema>(
  e: MaybeRef,
  form?: Ref<Form<Schema> | undefined>,
  throwError = true,
): boolean {
  e = toRaw(unref(e));

  if (!e) {
    return false;
  }

  const toast = useToast();

  if (isFetchError(e)) {
    // if (e.statusCode === 422) {
    //   if (e.data.message) {
    //     toast.add({
    //       title: e.data.message,
    //       timeout: 3000,
    //       color: 'red',
    //     });
    //   }
    //
    //   if (e.data.errors && form) {
    //     handleValidationError(e.data.errors, form);
    //   }
    //
    //   return true;
    // }

    e = createError({
      statusCode: e.statusCode,
      fatal: true,
      statusMessage: e.statusMessage,
      message:
        (e.data.message === 'Server Error' ? undefined : e.data.message) ??
        'Sorry, something went terribly wrong.',
      cause: e,
    });
  }

  if (!isNuxtError(e)) {
    e = createError(e);
  }

  if (throwError) {
    throw e;
  }

  toast.add({
    title: e.message,
    timeout: 3000,
    color: 'red',
  });

  return true;
}
