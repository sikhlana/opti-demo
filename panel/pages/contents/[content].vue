<script setup lang="ts">
import { format } from 'date-fns/format';
import { titleCase } from 'scule';

const {
  params: { content },
} = useRoute();

const { data, error, refresh } = await useFetch(
  `/contents/${content}`,
  withDefaultFetchOptions(),
);

handleApiError(error);
watch(error, () => handleApiError(error));

function refreshIfInProgress(): void {
  if (['completed', 'failed'].includes(data.value?.state)) {
    return;
  }

  refresh().then(() => setTimeout(() => refreshIfInProgress(), 500));
}

refreshIfInProgress();

const badgeColor = computed<string>(() => {
  switch (data.value?.state) {
    default:
      return 'yellow';
    case 'processing':
      return 'cyan';
    case 'completed':
      return 'emerald';
    case 'failed':
      return 'red';
  }
});
</script>

<template>
  <UContainer>
    <UPage>
      <UPageHeader
        :description="data.canonical_url ?? data.url"
        title="Scrape Details"
      >
        <template #links>
          <UBadge :color="badgeColor" :label="titleCase(data.state ?? 'N/A')" />
        </template>
      </UPageHeader>

      <UPageBody>
        <div
          v-if="data.state === 'failed'"
          class="flex flex-col items-center justify-center p-6"
        >
          <p class="font-bold text-xl">Unable to scrape resource</p>
          <small class="font-light pt-3 text-xs">{{ data.error }}</small>
        </div>
        <template v-else-if="data.state === 'completed'">
          <template v-if="data.parent">
            <div class="px-4 sm:px-0">
              <h3 class="font-semibold leading-7 text-base text-gray-900">
                Cached Content
              </h3>
              <p class="leading-6 max-w-2xl mt-1 text-gray-500 text-sm">
                This resource has been scraped on
                {{ format(new Date(data.parent.updated_at), 'PPpp') }}
              </p>
            </div>
            <AContent :content="data.parent" />
          </template>
          <AContent v-else :content="data" />
        </template>
        <div v-else class="flex flex-col items-center justify-center p-6">
          <p class="font-bold text-xl">
            Content scraping currently in-progress
          </p>
          <small class="font-light pt-3 text-xs"
            >The page will be automatically updated once it's ready.</small
          >
        </div>
      </UPageBody>

      <div class="flex flex-col items-center pb-9">
        <UButton label="Go back home" size="md" to="/" />
      </div>
    </UPage>
  </UContainer>
</template>
