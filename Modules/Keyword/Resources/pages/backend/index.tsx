import { Table } from '@/Components'
import {
  handleShowConfirmModal,
  showSuccessNotification,
} from '@/Utils/notification'

import { PageProps } from '@/types'
import {
  DeleteOutlined,
  EditOutlined,
  ImportOutlined,
  PauseOutlined,
  PlayCircleOutlined,
  PlusOutlined,
} from '@ant-design/icons'
import { Head, Link, router } from '@inertiajs/react'
import { Button, Card, Space, Tag, Tooltip } from 'antd'
import { ColumnsType } from 'antd/es/table'
import { Suspense, lazy, useCallback, useMemo, useState } from 'react'
import { KeywordPageProps, Keyword as KeywordType } from '../types/keyword'

const BulkAddModal = lazy(() =>
  import('./components').then((mod) => ({ default: mod.BulkAddModal })),
)

export default function Keyword({ data }: PageProps<KeywordPageProps>) {
  const [showImportModal, setShowImportModal] = useState(false)

  const handleToggleKeywordStatus = useCallback(
    (keyword: KeywordType) => () => {
      router.put(
        route('admin.keyword.update', keyword),
        {
          type: keyword.type.toLowerCase(),
          name: keyword.name,
          status: !keyword.status,
          source: keyword.source.toLowerCase(),
        },
        {
          onSuccess: () => {
            showSuccessNotification({
              title: 'Success',
              description: 'Status changed successfully',
            })
          },
        },
      )
    },
    [],
  )

  const columns = useMemo<ColumnsType<any>>(
    () => [
      {
        title: 'Name',
        dataIndex: 'name',
        render: (text, record) => {
          if (record.type.toLowerCase() === 'keyword') {
            return (
              <Link href={route('admin.post.show.keyword', text)}>{text}</Link>
            )
          }

          return (
            <Link href={route('admin.post.show.user', `${text}`)}>{text}</Link>
          )
        },
      },
      {
        title: 'Type',
        dataIndex: 'type',
      },
      {
        title: 'Source',
        dataIndex: 'source',
      },
      {
        title: 'Status',
        dataIndex: 'status',
        render: (v) => (
          <Tag color={v ? 'green' : 'red'}>{v ? 'Active' : 'Inactive'}</Tag>
        ),
      },
      {
        title: 'Total Post',
        dataIndex: 'total_post',
        responsive: ['md'],
      },
      {
        title: 'Last Post',
        dataIndex: 'last_post',
        responsive: ['md'],
      },
      {
        title: 'Last Crawled',
        dataIndex: 'last_crawled',
        responsive: ['md'],
      },
      {
        title: 'Actions',
        dataIndex: 'id',
        align: 'center',
        ellipsis: false,
        sorter: false,
        render: (value, record) => {
          return (
            <Space>
              {record.status ? (
                <Tooltip title="Pause">
                  <Button
                    type="primary"
                    icon={<PauseOutlined color="red" />}
                    title="Pause"
                    onClick={handleToggleKeywordStatus(record)}
                  />
                </Tooltip>
              ) : (
                <Tooltip title="Resume">
                  <Button
                    type="primary"
                    icon={<PlayCircleOutlined />}
                    title="Resume"
                    onClick={handleToggleKeywordStatus(record)}
                  />
                </Tooltip>
              )}

              <Link href={route('admin.keyword.edit', value)}>
                <Button icon={<EditOutlined />} title="Edit" />
              </Link>

              <Button
                icon={<DeleteOutlined />}
                type="primary"
                ghost
                danger
                title="Delete"
                onClick={handleShowConfirmModal(
                  route('admin.keyword.destroy', value),
                )}
              />
            </Space>
          )
        },
      },
    ],
    [data, handleShowConfirmModal, handleToggleKeywordStatus],
  )

  const handleClickAddProduct = () => {
    router.get(route('admin.keyword.create'))
  }

  const handleClickImport = () => {
    setShowImportModal((p) => !p)
  }
  const handleClickCancelImport = () => {
    setShowImportModal(false)
  }

  return (
    <>
      <Head title="Target Crawl" />

      <Card
        title="Target Crawl"
        bordered={false}
        className="table"
        extra={
          <Space>
            <Button
              icon={<ImportOutlined />}
              type="primary"
              onClick={handleClickImport}
            >
              Import
            </Button>
            <Button
              icon={<PlusOutlined />}
              type="primary"
              onClick={handleClickAddProduct}
            >
              Add
            </Button>
          </Space>
        }
      >
        <Table<any>
          route={route('admin.keyword.index')}
          dataSource={data}
          columns={columns}
          scroll={{
            x: 'max-content',
          }}
        />
      </Card>

      <Suspense>
        <BulkAddModal
          open={showImportModal}
          onCancel={handleClickCancelImport}
          onSuccess={handleClickCancelImport}
        />
      </Suspense>
    </>
  )
}
