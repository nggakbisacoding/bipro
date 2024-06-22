import react from '@vitejs/plugin-react'
import laravel from 'laravel-vite-plugin'
import { defineConfig } from 'vite'

import path from 'node:path'

export default defineConfig({
  plugins: [
    laravel({
      input: 'resources/js/app.tsx',
      refresh: true,
    }),
    react(),
  ],
  resolve: {
    alias: {
      Modules: path.resolve(__dirname, 'Modules'),
    },
  },
})
