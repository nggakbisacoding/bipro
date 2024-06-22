import AuthenticatedLayout, { getMenuItem } from '../AuthenticatedLayout'

import {
  BarChartOutlined,
  CommentOutlined,
  ExportOutlined,
  ProjectOutlined,
  UserAddOutlined,
  UserOutlined,
} from '@ant-design/icons'
import { Link } from '@inertiajs/react'
import type { MenuProps } from 'antd'
import { PropsWithChildren } from 'react'

const items: MenuProps['items'] = [
  getMenuItem(
    <Link href={route('admin.project.index')}>Projects</Link>,
    'projects',
    <ProjectOutlined />,
  ),

  {
    key: 'insight',
    icon: <BarChartOutlined />,
    label: <Link href={route('admin.insight.index')}>Insight</Link>,
  },
  {
    key: 'posts',
    icon: <CommentOutlined />,
    label: <Link href={route('admin.post.index')}>Posts</Link>,
  },
  {
    key: 'exports',
    icon: <ExportOutlined />,
    label: <Link href={route('admin.post.export.index')}>Exports</Link>,
  },
  //   getItem(
  //     <Link href={route('admin.export.index')}>Exports</Link>,
  //     'exports.menu',
  //     <ExportOutlined />,
  //   ),
  {
    key: 'keywords',
    icon: <UserAddOutlined />,
    label: <Link href={route('admin.keyword.index')}>Target Crawling</Link>,
  },

  getMenuItem(
    'System',
    'group.system',
    null,
    [
      getMenuItem('Access', 'access.menu', <UserOutlined />, [
        {
          key: 'access.accounts',
          label: <Link href={route('admin.users.index')}>Accounts</Link>,
        },
        {
          key: 'access.roles',
          label: <Link href={route('admin.roles.index')}>Roles</Link>,
        },
      ]),
    ],
    'group',
  ),
]

type Props = PropsWithChildren<{}>
export const AdminAuthenticatedLayout = ({ children }: Props) => {
  return <AuthenticatedLayout menus={items}>{children}</AuthenticatedLayout>
}
