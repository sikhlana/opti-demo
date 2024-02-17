import type { IFetchError } from 'ofetch';

export function isFetchError(obj: any): obj is IFetchError {
  return obj?.statusCode;
}
