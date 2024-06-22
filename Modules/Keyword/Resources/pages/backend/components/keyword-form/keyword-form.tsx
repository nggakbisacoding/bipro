import { router } from '@inertiajs/react'
import {
  Button,
  Col,
  DatePicker,
  Form,
  Input,
  Modal,
  Row,
  Select,
  Switch,
} from 'antd'
import { RangePickerProps } from 'antd/es/date-picker'
import dayjs from 'dayjs'
import { FC } from 'react'

type KeywordForm = {
  loading: boolean
  errors: Partial<Record<'username' | 'name' | 'source' | 'total_post', string>>
}

const { RangePicker } = DatePicker
const disabledDate: RangePickerProps['disabledDate'] = (current) => {
  return current && current > dayjs().endOf('day')
}
export const KeywordForm: FC<KeywordForm> = ({ loading }) => {
  const f = Form.useFormInstance()

  const isEdit = window.location.href?.endsWith('edit')
  const isDisabled = isEdit || loading

  const handleBack = () => {
    if (!f.isFieldsTouched()) {
      router.get(route('admin.keyword.index'))
      return
    }

    Modal.confirm({
      title: 'Are you sure?',
      content:
        'You will lose all unsaved changes and you will not be able to recover this data.',
      okText: 'Yes',
      onOk: () => {
        router.get(route('admin.keyword.index'))
      },
    })
  }

  return (
    <Row
      gutter={[
        { xs: 8, md: 16 },
        { xs: 0, lg: 16 },
      ]}
    >
      <Col span={24}>
        <Form.Item name="type" rules={[{ required: true }]} label="Type">
          <Select placeholder="Type" disabled={isDisabled}>
            <Select.Option value="keyword">Keyword</Select.Option>
            <Select.Option value="account">Account</Select.Option>
          </Select>
        </Form.Item>

        {
          <Form.Item noStyle dependencies={['type']}>
            {({ getFieldValue }) => {
              const isKeyword = getFieldValue('type') === 'keyword'
              return (
                <>
                  <Form.Item
                    name="name"
                    rules={[{ required: true }]}
                    label={isKeyword ? 'Keyword' : 'Username'}
                  >
                    <Input
                      placeholder={isKeyword ? 'Keyword' : 'Username'}
                      allowClear
                      disabled={isDisabled}
                    />
                  </Form.Item>
                </>
              )
            }}
          </Form.Item>
        }

        <Form.Item dependencies={['type']}>
          {({ getFieldValue }) => (
            <Form.Item
              name="source"
              rules={[{ required: true }]}
              label="Source"
            >
              <Select
                disabled={isDisabled}
                placeholder="Source"
                defaultActiveFirstOption
                allowClear
              >
                {/* <Select.Option value="facebook">Facebook</Select.Option> */}
                <Select.Option value="twitter">Twitter</Select.Option>
                <Select.Option value="tiktok">Tiktok</Select.Option>
                {getFieldValue('type') === 'account' && (
                  <Select.Option value="instagram">Instagram</Select.Option>
                )}
              </Select>
            </Form.Item>
          )}
        </Form.Item>

        <Form.Item
          name="date"
          label="Date"
          help="Only monitor data within the selected date range. If left empty, it will default to the last 5 years."
        >
          <RangePicker disabled={isDisabled} disabledDate={disabledDate} />
        </Form.Item>

        <Form.Item
          name="status"
          label="Status"
          valuePropName="checked"
          help="Enables or disables the keyword."
        >
          <Switch />
        </Form.Item>

        <Form.Item
          name="is_monitor"
          label="Monitor"
          valuePropName="checked"
          help="Enables automatic monitoring of entered keywords or accounts."
        >
          <Switch />
        </Form.Item>

        <Form.Item>
          <Row gutter={[8, 8]} align="middle" justify="end">
            <Col
              span={24}
              order={1}
              md={{ span: 4, order: 2 }}
              xl={{ span: 2, order: 2 }}
            >
              <Button type="primary" htmlType="submit" block loading={loading}>
                Submit
              </Button>
            </Col>
            <Col
              span={24}
              order={2}
              md={{ span: 4, order: 1 }}
              xl={{ span: 2, order: 1 }}
            >
              <Button block onClick={handleBack} disabled={loading}>
                Cancel
              </Button>
            </Col>
          </Row>
        </Form.Item>
      </Col>
    </Row>
  )
}
