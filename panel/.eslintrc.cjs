module.exports = {
  root: true,
  env: {
    browser: true,
    node: true,
  },
  parser: 'vue-eslint-parser',
  parserOptions: {
    parser: '@typescript-eslint/parser',
    ecmaVersion: 2022,
    sourceType: 'module',
  },
  plugins: ['simple-import-sort', 'unused-imports'],
  extends: [
    '@nuxt/eslint-config',
    'eslint:recommended',
    'plugin:prettier/recommended',
    'plugin:typescript-sort-keys/recommended',
  ],
  ignorePatterns: ['.output/**', 'dist/**', 'node_modules/**', 'api.d.ts'],
  rules: {
    '@typescript-eslint/no-unused-vars': 'off',
    'comma-dangle': ['error', 'always-multiline'],
    'no-multiple-empty-lines': ['error', { max: 1 }],
    'no-undef': 'off',
    'no-unused-vars': 'off',
    'object-curly-spacing': [
      'error',
      'always',
      {
        arraysInObjects: true,
        objectsInObjects: true,
      },
    ],
    quotes: [
      'error',
      'single',
      {
        avoidEscape: true,
        allowTemplateLiterals: true,
      },
    ],
    semi: ['error', 'always'],
    'simple-import-sort/imports': 'error',
    'unused-imports/no-unused-imports': 'error',
    'vue/attributes-order': ['error', { alphabetical: true }],
    'vue/html-quotes': [
      'error',
      'double',
      {
        avoidEscape: true,
      },
    ],
    'vue/static-class-names-order': 'error',
    'vue/multi-word-component-names': 'off',
  },
};
