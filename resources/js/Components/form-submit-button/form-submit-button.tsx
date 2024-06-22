import { Button, Col, Row } from 'antd'
import { FC } from 'react'

type FormSubmitButtonProp = {
  loading?: boolean
  onClickBack?: () => void
}
export const FormSubmitButton: FC<FormSubmitButtonProp> = ({
  loading,
  onClickBack,
}) => {
  return (
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
        <Button block onClick={onClickBack} disabled={loading}>
          Cancel
        </Button>
      </Col>
    </Row>
  )
}
