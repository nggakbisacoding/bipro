import { handleOnSuccess } from '@/Utils/notification'

import { InboxOutlined } from '@ant-design/icons'
import { useForm } from '@inertiajs/react'
import {
  Alert,
  Modal,
  ModalProps,
  Space,
  Table,
  Tooltip,
  Typography,
  UploadProps,
  notification,
} from 'antd'
import { ColumnsType } from 'antd/es/table'
import Dragger from 'antd/es/upload/Dragger'
import { FC, useMemo } from 'react'

type Props = ModalProps & {
  onSuccess: () => void
}
type ImportData = {
  type: string
  username: string
  username_error: string
  source: string
  source_error: string
  start_date: string
  end_date: string
}

export const BulkAddModal: FC<Props> = ({ open, onSuccess, ...props }) => {
  const { data, setData, post, processing, errors, clearErrors } = useForm<{
    keywords: ImportData[]
  }>('ImportKeyword', {
    keywords: [],
  })

  const dataHasError = data.keywords.some(
    (item) => item.source_error || item.username_error,
  )

  const uploadProps: UploadProps = {
    name: 'file',
    multiple: false,
    showUploadList: false,
    beforeUpload() {
      return false
    },
    accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    maxCount: 1,
    async onChange(info) {
      const { status } = info.file
      if (status !== 'uploading') {
        const readXlsxFile = (await import('read-excel-file')).default
        const data = await readXlsxFile(info.file as unknown as File)

        const checkSourceExists = (source: string) => {
          return [
            'facebook',
            'instagram',
            'twitter',
            'youtube',
            'tiktok',
          ].includes(source.toLowerCase())
        }

        try {
          const dayjs = (await import('dayjs')).default
          const newData = data.slice(1).map((item) => {
            if (item.length !== 5) {
              throw new Error('Invalid file format!')
            }

            const source = (item[2] as string).toLowerCase()
            const startDate = item[3] as string
            const endDate = item[4] as string

            return {
              type: (item[0] as string).toLowerCase(),
              username: item[1] as string,
              username_error: '',
              source,
              source_error: checkSourceExists(source)
                ? ''
                : 'Source does not exist!',
              start_date: dayjs(startDate).format('DD-MM-YYYY'),
              end_date: dayjs(endDate).format('DD-MM-YYYY'),
            }
          })

          const uniqueKeywords = Array.from(
            newData
              .reduce((map, obj) => {
                map.set(JSON.stringify(obj), obj)
                return map
              }, new Map())
              .values(),
          )

          setData({
            keywords: uniqueKeywords,
          })
        } catch (error: any) {
          notification.error({
            message: 'Error',
            description: error.message,
          })
        }
      }
    },
  }

  const errorsKeyword = errors as unknown as Record<string, string>
  const columns: ColumnsType<ImportData> = useMemo(
    () => [
      {
        title: 'Type',
        dataIndex: 'type',
        key: 'type',
        width: 80,
        sorter: true,
      },
      {
        title: 'Username/Keyword',
        dataIndex: 'username',
        key: 'username',
        sorter: true,
        render: (text, _record, index) => {
          const errorUsername = errorsKeyword[`keywords.${index}.username`]
          if (!errorUsername) {
            return text
          }

          return (
            <Space>
              <Tooltip title={errorUsername}>
                <Typography.Text type="danger">{text}</Typography.Text>
              </Tooltip>
            </Space>
          )
        },
      },
      {
        title: 'Source',
        dataIndex: 'source',
        key: 'source',
        sorter: true,
        render: (text, _record, index) => {
          const errorSource = errorsKeyword[`keywords.${index}.source`]
          if (!errorSource) {
            return text
          }

          return (
            <Space>
              <Tooltip title={errorSource}>
                <Typography.Text type="danger">{text}</Typography.Text>
              </Tooltip>
            </Space>
          )
        },
      },
      {
        title: 'Start Date',
        dataIndex: 'start_date',
        key: 'start_date',
        sorter: true,
        width: 120,
        render: (text, _record, index) => {
          const errorStartDate = errorsKeyword[`keywords.${index}.start_date`]
          if (!errorStartDate) {
            return text
          }

          return (
            <Space>
              <Tooltip title={errorStartDate}>
                <Typography.Text type="danger">{text}</Typography.Text>
              </Tooltip>
            </Space>
          )
        },
      },
      {
        title: 'End Date',
        dataIndex: 'end_date',
        key: 'end_date',
        sorter: true,
        width: 120,
        render: (text, _record, index) => {
          const errorEndDate = errorsKeyword[`keywords.${index}.end_date`]
          if (!errorEndDate) {
            return text
          }

          return (
            <Space>
              <Tooltip title={errorEndDate}>
                <Typography.Text type="danger">{text}</Typography.Text>
              </Tooltip>
            </Space>
          )
        },
      },
    ],
    [data, errorsKeyword],
  )

  const afterCloseModal = () => {
    setData({ keywords: [] })
    clearErrors()
  }

  const onSuccessImport = (e: any) => {
    handleOnSuccess(e)
    onSuccess()
  }
  const handleClickImport = () => {
    post(route('admin.keyword.import'), {
      onSuccess: onSuccessImport,
      preserveState: true,
    })
  }

  return (
    <>
      <Modal
        title="Bulk Add Keyword"
        open={open}
        width={data.keywords.length === 0 ? 600 : 1000}
        okButtonProps={{
          disabled: data.keywords.length === 0 || dataHasError,
          loading: processing,
        }}
        closable={!processing}
        destroyOnClose
        maskClosable={false}
        okText="Import"
        afterClose={afterCloseModal}
        onOk={handleClickImport}
        {...props}
      >
        {data.keywords.length === 0 ? (
          <Space
            direction="vertical"
            style={{
              width: '100%',
            }}
          >
            <Alert
              type="warning"
              message="Duplicate values will be removed from list"
            />

            <Dragger {...uploadProps}>
              <p className="ant-upload-drag-icon">
                <InboxOutlined />
              </p>
              <p className="ant-upload-text">
                Click or drag file to this area to upload
              </p>
              <p className="ant-upload-hint">
                Support for a single. Strictly prohibited from uploading company
                data or other banned files.
              </p>
            </Dragger>

            <Typography.Link href={route('admin.keyword.export.template')}>
              Download Template
            </Typography.Link>
          </Space>
        ) : (
          <Space
            direction="vertical"
            style={{
              width: '100%',
            }}
          >
            <Alert
              type="warning"
              message="It may take a few minutes to import the data. Do not close or refresh the page!"
            />
            <Table
              rowKey={'username'}
              columns={columns}
              dataSource={data.keywords}
              pagination={{
                hideOnSinglePage: true,
              }}
              loading={processing}
              scroll={{
                x: 'max-content',
              }}
            />
          </Space>
        )}
      </Modal>
    </>
  )
}
