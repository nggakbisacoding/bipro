import { handleOnSuccess } from '@/Utils/notification'

import { useForm } from '@inertiajs/react'
import {
  Alert,
  DatePicker,
  Form,
  Input,
  Modal,
  ModalProps,
  Select,
  Space,
} from 'antd'
import { FC, useEffect, useState } from 'react'

type ExportPostModalProps = ModalProps
export const ExportPostModal: FC<ExportPostModalProps> = (props) => {
  const [submit, setSubmit] = useState(false)
  const [form] = Form.useForm()

  const { setData, post, processing } = useForm()

  useEffect(() => {
    if (submit) {
      post(route('admin.post.export.store'), {
        onSuccess: handleOnSuccess,
      })
    }
  }, [submit])

  const handleExport = async () => {
    const values = await form.validateFields()

    values.start_date = ''
    values.end_date = ''

    if (values.date?.[0]) {
      values.start_date = values.date[0].format('YYYY-MM-DD HH:mm:ss')
    }

    if (values.date?.[1]) {
      values.end_date = values.date[1].format('YYYY-MM-DD HH:mm:ss')
    }

    delete values.date

    setData(values)
    setSubmit((prev) => !prev)
  }
  return (
    <Modal
      okText="Export"
      title="Export Posts"
      onOk={handleExport}
      okButtonProps={{
        loading: processing,
      }}
      {...props}
    >
      <Space
        direction="vertical"
        style={{
          width: '100%',
        }}
      >
        <Alert
          message="Eksport semua akan berlangsung sangat lama, disarankan untuk menambahkan filter ketika exports. Biarkan kosong jika ingin eksport semua"
          type="warning"
        />
        <Form layout="vertical" form={form}>
          <Form.Item label="Username/Keyword" name="username">
            <Input allowClear placeholder="Select username/keyword" />
          </Form.Item>

          <Form.Item label="Source" name="source">
            <Select
              allowClear
              mode="multiple"
              options={['twitter', 'facebook', 'tiktok', 'instagram'].map(
                (o) => ({
                  value: o,
                  label: o.charAt(0).toUpperCase() + o.slice(1),
                }),
              )}
              placeholder="Select source"
            />
          </Form.Item>

          <Form.Item label="Date" name="date">
            <DatePicker.RangePicker
              showTime
              allowClear
              style={{
                width: '100%',
              }}
            />
          </Form.Item>

          <Form.Item label="Sentiment" name="sentiment">
            <Select
              allowClear
              mode="multiple"
              placeholder="Select sentiment"
              options={['positive', 'neutral', 'negative'].map((o) => ({
                value: o,
                label: o.charAt(0).toUpperCase() + o.slice(1),
              }))}
            />
          </Form.Item>
        </Form>
      </Space>
    </Modal>
  )
}
