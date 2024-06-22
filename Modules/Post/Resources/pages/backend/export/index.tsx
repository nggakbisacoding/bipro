import { Table } from '@/Components'

import { Head } from '@inertiajs/react'
import { Button, Card, Progress, Tag } from 'antd'
import { ColumnType } from 'antd/es/table/interface'
import { useMemo } from 'react'
import { PostPageProps } from '../types/post'

export default function PostExportIndex({ data }: PostPageProps) {
  const columns = useMemo<ColumnType<any>[]>(
    () => [
      {
        title: 'Progress',
        dataIndex: 'progress',
        sorter: false,
        render: (value) => <Progress percent={value} />,
        responsive: ['md'],
      },
      {
        title: 'Name',
        dataIndex: 'name',
        sorter: false,
      },
      {
        title: 'Status',
        dataIndex: 'status',
        sorter: false,
        render: (value, record) => {
          return (
            <Tag color={record.status_color} title={value}>
              {value}
            </Tag>
          )
        },
      },
      {
        title: 'Created At',
        dataIndex: 'created_at',
        responsive: ['md'],
      },
      {
        title: 'Action',
        dataIndex: 'id',
        align: 'center',
        sorter: false,
        render: (_value, record) => {
          return (
            <Button
              type="primary"
              disabled={!record.status.includes('finish')}
              download={record.name}
              href={record.url}
            >
              Download
            </Button>
          )
        },
      },
    ],
    [],
  )

  return (
    <>
      <Head title="Post Exports" />

      <Card title="Post Exports" bordered={false} className="table">
        <Table<any>
          route={route('admin.post.export.index')}
          dataSource={data}
          columns={columns}
        />
      </Card>
    </>
  )
}
