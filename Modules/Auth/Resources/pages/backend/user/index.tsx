import { Table } from '@/Components'
import { handleShowConfirmModal } from '@/Utils/notification'

import { PageProps } from '@/types'
import {
  DeleteOutlined,
  EditOutlined,
  PlusOutlined,
  UserSwitchOutlined,
} from '@ant-design/icons'
import { Head, Link, router } from '@inertiajs/react'
import { Button, Card, Space, Tag } from 'antd'
import { ColumnType } from 'antd/es/table/interface'
import { useMemo } from 'react'

export default function Users({ users }: PageProps<{ users: any }>) {
  const columns = useMemo<ColumnType<any>[]>(
    () => [
      {
        title: 'Type',
        dataIndex: 'type',
        width: 120,
      },
      {
        title: 'Name',
        dataIndex: 'name',
      },
      {
        title: 'Email',
        dataIndex: 'email',
      },
      {
        title: 'Vertified',
        dataIndex: 'email_verified_at',
        render: (value) => (
          <Tag color={value !== 'Not verified' ? 'green' : 'red'}>{value}</Tag>
        ),
      },
      {
        title: '2FA',
        dataIndex: 'two_factor_auth_count',
        render: (value) => (
          <Tag color={value !== 'Not enabled' ? 'green' : 'red'}>{value}</Tag>
        ),
      },
      {
        title: 'Roles',
        dataIndex: 'roles',
        sorter: false,
        render: (value) => (
          <>
            {value?.map((role: string) => (
              <span key={role}>
                {role}
                <br />
              </span>
            ))}
          </>
        ),
      },
      {
        title: 'Additional Permissions',
        dataIndex: 'permissions',
        sorter: false,
        render: (value) => <div dangerouslySetInnerHTML={{ __html: value }} />,
      },
      {
        title: 'Last Login At',
        dataIndex: 'last_login_at',
      },
      {
        title: 'Created At',
        dataIndex: 'created_at',
      },
      {
        title: 'Actions',
        dataIndex: 'id',
        align: 'center',
        sorter: false,
        render: (value) => {
          return (
            <Space>
              <Link href={route('admin.users.impersonate', value)}>
                <Button icon={<UserSwitchOutlined />} title="Login As" />
              </Link>

              <Link href={route('admin.users.edit', value)}>
                <Button icon={<EditOutlined />} title="Edit" />
              </Link>

              <Button
                icon={<DeleteOutlined />}
                type="primary"
                ghost
                danger
                title="Delete"
                onClick={handleShowConfirmModal(
                  route('admin.users.destroy', value),
                )}
              />
            </Space>
          )
        },
      },
    ],
    [handleShowConfirmModal],
  )

  const handleClickAddProduct = () => {
    router.get(route('admin.users.create'))
  }

  return (
    <>
      <Head title="Users" />

      <Card
        title="Users"
        bordered={false}
        className="table"
        extra={
          <Button
            icon={<PlusOutlined />}
            type="primary"
            onClick={handleClickAddProduct}
          >
            Add
          </Button>
        }
      >
        <Table<any>
          route={route('admin.users.index')}
          dataSource={users}
          columns={columns}
          scroll={{
            x: 'max-content',
          }}
        />
      </Card>
    </>
  )
}
