import { createResolver, defineNuxtModule, addImportsDir, hasNuxtModule, addComponentsDir } from '@nuxt/kit'
import { defu } from 'defu'

export default defineNuxtModule({
  meta: {
    name: 'ui-pro',
    configKey: 'uiPro'
  },
  defaults: {
    license: ''
  },
  setup (_, nuxt) {
    const resolver = createResolver(import.meta.url)
    /**
     * Add Content components & utils only if Nuxt Content is present
     */
    if (hasNuxtModule('@nuxt/content')) {
      // Add auto-imported utils
      addImportsDir(resolver.resolve('runtime/utils'))

      addComponentsDir({
        path: resolver.resolve('runtime/components/content'),
        global: true,
        prefix: '',
        pathPrefix: false
      })

      addComponentsDir({
        path: resolver.resolve('runtime/components/docs'),
        prefix: 'U',
        pathPrefix: false
      })

      // @ts-ignore
      nuxt.options.content = defu(nuxt.options.content, {
        highlight: {
          theme: {
            light: 'material-theme-lighter',
            default: 'material-theme',
            dark: 'material-theme-palenight'
          },
          preload: ['json', 'js', 'ts', 'html', 'css', 'vue', 'diff', 'shell', 'markdown', 'yaml', 'bash', 'ini']
        },
        navigation: {
          fields: ['icon', 'to', 'target']
        }
      })

      // @ts-ignore
      nuxt.hook('tailwindcss:config', function (tailwindConfig) {
        // @ts-ignore
        tailwindConfig.content.files.push(resolver.resolve('./runtime/components/**/*.{vue,mjs,ts}'))
      })
    }
  }
})
