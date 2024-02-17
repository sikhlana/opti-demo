<script setup lang="ts">
interface Content {
  body?: string;
  title?: string;
}

const { content } = defineProps<{
  content: Content;
}>();
</script>

<template>
  <div class="mt-6">
    <dl class="divide-gray-100 divide-y">
      <div class="px-4 py-6 sm:gap-4 sm:grid sm:grid-cols-3 sm:px-0">
        <dt class="font-medium leading-6 text-gray-900 text-sm">Title</dt>
        <dd class="leading-6 mt-1 sm:col-span-2 sm:mt-0 text-gray-700 text-sm">
          {{ content.title ?? 'N/A' }}
        </dd>
      </div>
      <div class="px-4 py-6 sm:gap-4 sm:grid sm:grid-cols-3 sm:px-0">
        <dt class="font-medium leading-6 text-gray-900 text-sm">Description</dt>
        <dd class="leading-6 mt-1 sm:col-span-2 sm:mt-0 text-gray-700 text-sm">
          {{ content.meta.description ?? 'N/A' }}
        </dd>
      </div>
      <div class="px-4 py-6 sm:gap-4 sm:grid sm:grid-cols-3 sm:px-0">
        <dt class="font-medium leading-6 text-gray-900 text-sm">Body</dt>
        <dd
          class="leading-6 mt-1 sm:col-span-2 sm:mt-0 text-gray-700 text-sm whitespace-pre-wrap"
        >
          {{ content.body ?? 'N/A' }}
        </dd>
      </div>
    </dl>
  </div>
</template>
