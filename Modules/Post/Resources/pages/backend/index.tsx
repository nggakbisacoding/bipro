import { Table } from '@/Components'

import { ExportOutlined } from '@ant-design/icons'
import { Head } from '@inertiajs/react'
import { Avatar, Button, Card, Space, Tooltip } from 'antd'
import { ColumnType } from 'antd/es/table/interface'
import { useMemo, useState } from 'react'
import { ExportPostModal } from './components'
import { PostPageProps } from './types/post'
export default function PostIndex({ data }: PostPageProps) {
  const [showExportModal, setShowExportModal] = useState(false)

  const columns = useMemo<ColumnType<any>[]>(
    () => [
      {
        title: 'Account',
        dataIndex: 'username',
        ellipsis: false,
        width: 200,
        render: (value, record) => {
          return (
            <Space direction="horizontal">
              <Avatar src={record.avatar} />
              <Space direction="vertical" size={0}>
                <span>{record.name}</span>
                <span>@{record.username}</span>
              </Space>
            </Space>
          )
        },
      },
      {
        title: 'Message',
        dataIndex: 'message',
        ellipsis: true,
      },
      {
        title: 'Source',
        dataIndex: 'source',
      },
      {
        title: 'Topic',
        dataIndex: ['stats', 'topic'],
      },
      {
        title: 'Sentiment',
        dataIndex: ['stats', 'sentiment'],
      },
      {
        title: 'Intolerance',
        dataIndex: ['stats', 'intolerance'],
        render: (value, record) => {
          const intoleranceLevel = value ?? record.stats.intolerance_level ?? 0
          return <Tooltip title={intoleranceLevel}>{intoleranceLevel}</Tooltip>
        },
      },
      {
        title: 'Date',
        dataIndex: 'date',
      },
    ],
    [],
  )

  const handleExport = () => {
    setShowExportModal((prev) => !prev)
    // location.href = '/admin/post/export'
  }

  const handleCloseExportModal = () => {
    setShowExportModal((prev) => !prev)
  }
  return (
    <>
      <Head title="Posts" />

      <Card
        title="Posts"
        bordered={false}
        className="table"
        extra={
          <Button
            icon={<ExportOutlined />}
            type="primary"
            onClick={handleExport}
          >
            Export
          </Button>
        }
      >
        <Table<any>
          route={route('admin.post.index')}
          dataSource={data}
          columns={columns}
        />
      </Card>

      <ExportPostModal
        open={showExportModal}
        onCancel={handleCloseExportModal}
      />
    </>
  )
}
