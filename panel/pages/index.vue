<script setup lang="ts">
import type { FormSubmitEvent } from '#ui/types';

interface State {
  force: boolean;
  url: string;
}

const loading = ref<boolean>(false);

const state = ref<State>({
  url: '',
  force: false,
});

async function submit(e: FormSubmitEvent<State>): Promise<void> {
  loading.value = true;

  try {
    const { data, error } = await useFetch(
      '/scrape',
      withDefaultFetchOptions({
        method: 'post',
        body: e.data,
        watch: false,
      }),
    );

    if (handleApiError(error, undefined, false)) {
      return;
    }

    await navigateTo(`/contents/${data.value?.id}`);
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <UContainer>
    <UPage>
      <ULandingHero
        description="Easily scrape contents of news articles from web-based sources!"
        icon="i-ph-robot"
        title="Content Scraper"
      >
        <template #links>
          <UForm
            class="flex flex-col max-w-3xl space-y-2.5 w-full"
            :state="state"
            @submit="submit"
          >
            <div class="flex space-x-2.5 w-full">
              <UInput
                v-model="state.url"
                class="flex-grow"
                :disabled="loading"
                placeholder="Enter a URL..."
                size="xl"
              />
              <UButton
                color="primary"
                label="Let's Go!"
                :loading="loading"
                size="xl"
                type="submit"
              />
            </div>
            <div>
              <UCheckbox
                v-model="state.force"
                label="Scrape again even if there's an older version."
              />
            </div>
          </UForm>
        </template>
      </ULandingHero>
    </UPage>
  </UContainer>
</template>
