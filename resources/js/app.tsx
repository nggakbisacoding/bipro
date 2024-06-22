import 'antd/dist/reset.css'
import './bootstrap'

import { createInertiaApp } from '@inertiajs/react'
import { ConfigProvider, notification } from 'antd'
import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import { resolvePage } from './Utils/resolverPage'

const appName =
  window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel'

notification.config({
  placement: 'topRight',
  duration: 3,
  maxCount: 1,
})

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: resolvePage,
  setup({ el, App, props }) {
    const root = createRoot(el)
    root.render(
      <StrictMode>
        <ConfigProvider>
          <App {...props} />
        </ConfigProvider>
      </StrictMode>,
    )
  },
  progress: {
    color: '#1677ff',
  },
})
