import AuthenticatedLayout, { getMenuItem } from '../AuthenticatedLayout'

import { DashboardOutlined, UserAddOutlined } from '@ant-design/icons'
import { Link } from '@inertiajs/react'
import { PropsWithChildren, useMemo } from 'react'

type Props = PropsWithChildren<{}>
export const UserAuthenticatedLayout = ({ children }: Props) => {
  const items = useMemo(
    () => [
      getMenuItem(
        <Link href={route('frontend.user.insight.index')}>Insight</Link>,
        'insight.index',
        <DashboardOutlined />,
      ),
      getMenuItem(
        <Link href={route('frontend.user.keyword.index')}>Keywords</Link>,
        'keyword.index',
        <UserAddOutlined />,
      ),
    ],
    [],
  )
  return <AuthenticatedLayout menus={items}>{children}</AuthenticatedLayout>
}
