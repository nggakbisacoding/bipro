const dotenvExpand = require('dotenv-expand')
dotenvExpand(require('dotenv').config({ path: '../../.env' /*, debug: true*/ }))

import laravel from 'laravel-vite-plugin'
import { defineConfig } from 'vite'

export default defineConfig({
  optimizeDeps: {
    exclude: ['@ant-design/plots'],
  },
  build: {
    outDir: '../../public/build-keyword',
    emptyOutDir: true,
    manifest: true,
  },
  plugins: [
    laravel({
      publicDirectory: '../../public',
      buildDirectory: 'build-keyword',
      input: [
        __dirname + '/Resources/assets/sass/app.scss',
        __dirname + '/Resources/assets/js/app.js',
      ],
      refresh: true,
    }),
  ],
})
