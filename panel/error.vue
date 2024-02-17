<script setup lang="ts">
import type { NuxtError } from '#app';

defineProps<{
  error: NuxtError;
}>();

useHead({
  title: 'Error',
});
</script>

<template>
  <UHeader />

  <UContainer>
    <UMain>
      <UPage>
        <UPageError :error="'toJSON' in error ? error.toJSON() : error" />
      </UPage>
    </UMain>
  </UContainer>
</template>
