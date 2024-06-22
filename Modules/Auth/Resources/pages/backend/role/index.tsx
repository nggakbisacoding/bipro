import { Table } from '@/Components'
import { handleShowConfirmModal } from '@/Utils/notification'

import { PageProps } from '@/types'
import { DeleteOutlined, EditOutlined, PlusOutlined } from '@ant-design/icons'
import { Head, Link, router } from '@inertiajs/react'
import { Button, Card, Space } from 'antd'
import { ColumnType } from 'antd/es/table/interface'
import { useMemo } from 'react'

export default function Users({ roles }: PageProps<{ roles: any }>) {
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
        title: 'Permissions',
        dataIndex: 'permissions',
        sorter: false,
        render: (value) => <div dangerouslySetInnerHTML={{ __html: value }} />,
      },
      {
        title: 'Number of Users',
        dataIndex: 'users_count',
      },
      {
        title: 'Actions',
        dataIndex: 'id',
        align: 'center',
        sorter: false,
        render: (value) => {
          return (
            <Space>
              <Link href={route('admin.roles.edit', value)}>
                <Button icon={<EditOutlined />} title="Edit" />
              </Link>

              <Button
                icon={<DeleteOutlined />}
                type="primary"
                ghost
                danger
                title="Delete"
                onClick={handleShowConfirmModal(
                  route('admin.roles.destroy', value),
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
    router.get(route('admin.roles.create'))
  }

  return (
    <>
      <Head title="Roles" />

      <Card
        title="Roles"
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
          route={route('admin.roles.index')}
          dataSource={roles}
          columns={columns}
        />
      </Card>
    </>
  )
}
