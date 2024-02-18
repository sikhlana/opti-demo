// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  app: {
    head: {
      title: 'Content Scraper',
    },
  },
  colorMode: {
    fallback: 'light',
    preference: 'light',
  },
  components: [{ path: './components', prefix: 'A', pathPrefix: true }],
  devtools: { enabled: true },
  extends: ['@nuxt/ui-pro'],
  modules: [
    '@nuxt/image',
    '@nuxt/ui',
    '@nuxtjs/eslint-module',
    '@vueuse/nuxt',
    'nuxt-headlessui',
    'nuxt-icon',
  ],
  runtimeConfig: {
    api: {
      baseUrl: undefined,
    },
    public: {
      api: {
        baseUrl: process.env.API_BASE_URL,
      },
    },
  },
  ssr: false,
  ui: {
    icons: ['ph'],
  },
  vite: {
    build: {
      cssCodeSplit: true,
      minify: 'esbuild',
      rollupOptions: {
        treeshake: 'recommended',
      },
      sourcemap: true,
    },
  },
  vue: {
    propsDestructure: true,
  },
});
