import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { lazy } from 'react'

const AdminAuthenticatedLayout = lazy(() =>
  import('@/Layouts').then((mod) => ({
    default: mod.AdminAuthenticatedLayout,
  })),
)
const UserAuthenticatedLayout = lazy(() =>
  import('@/Layouts').then((mod) => ({ default: mod.UserAuthenticatedLayout })),
)
const GuestLayout = lazy(() => import('@/Layouts/GuestLayout'))

export function toPascalCase(str: string) {
  return str
    .replace(/[-_]+([a-zA-Z])/g, function (match, letter) {
      return letter.toUpperCase()
    })
    .replace(/^([a-zA-Z])/, function (match, letter) {
      return letter.toUpperCase()
    })
}

const resolvePageComponentCustom = async (
  path: string,
  pages: Record<string, any>,
) => {
  const page = pages[path.startsWith('/') ? path : `/${path}`]

  if (!page) {
    throw new Error(`Page not found: ${path}`)
  }

  return typeof page == 'function' ? page() : page
}

export async function resolvePage(name: string) {
  if (!name.includes('::')) {
    const page = await resolvePageComponent(
      `../Pages/${name}.tsx`,
      import.meta.glob('../Pages/**/*.tsx'),
    )

    return page
  }

  const [module, page] = name.split('::')

  const pagePath = page
    .split('.')
    .map((x) => x.toLowerCase())
    .join('/')

  const pages: any = await resolvePageComponentCustom(
    `/Modules/${toPascalCase(
      module,
    )}/Resources/pages/${pagePath.toLowerCase()}.tsx`,
    import.meta.glob(`/Modules/**/Resources/pages/**/*.tsx`),
  )

  if (name.includes('backend')) {
    pages.default.layout = (page: any) => (
      <AdminAuthenticatedLayout children={page} />
    )
  } else if (name.includes('frontend')) {
    pages.default.layout = (page: any) => (
      <UserAuthenticatedLayout children={page} />
    )
  } else if (module === 'auth') {
    pages.default.layout = (page: any) => <GuestLayout children={page} />
  }

  return pages
}
