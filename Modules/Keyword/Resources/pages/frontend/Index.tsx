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
import { useCallback, useMemo, useState } from 'react'
import { BulkAddModal } from '../backend/components'
import { KeywordPageProps, Keyword as KeywordType } from '../types/keyword'

export default function Keyword({ data }: PageProps<KeywordPageProps>) {
  const [showImportModal, setShowImportModal] = useState(false)

  const handleToggleKeywordStatus = useCallback(
    (keyword: KeywordType) => () => {
      router.put(
        route('frontend.keyword.update', keyword),
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
          return (
            <Link href={route('frontend.user.post.show', record.id)}>
              {text}
            </Link>
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
        title: 'Update At',
        dataIndex: 'updated_at',
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

              <Link href={route('frontend.user.keyword.edit', value)}>
                <Button icon={<EditOutlined />} title="Edit" />
              </Link>

              <Button
                icon={<DeleteOutlined />}
                type="primary"
                ghost
                danger
                title="Delete"
                onClick={handleShowConfirmModal(
                  route('frontend.user.keyword.destroy', value),
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
    router.get(route('frontend.user.keyword.create'))
  }

  const handleClickImport = () => {
    setShowImportModal((p) => !p)
  }
  const handleClickCancelImport = () => {
    setShowImportModal(false)
  }

  return (
    <>
      <Head title="Keywords" />

      <Card
        title="Keywords"
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
          route={route('frontend.user.keyword.index')}
          dataSource={data}
          columns={columns}
        />
      </Card>

      <BulkAddModal
        open={showImportModal}
        onCancel={handleClickCancelImport}
        onSuccess={handleClickCancelImport}
      />
    </>
  )
}
