import { Head, useForm } from '@inertiajs/react'
import { useEffect } from 'react'

import { LockOutlined, UserOutlined } from '@ant-design/icons'
import { Button, Checkbox, Col, Form, Input, Row } from 'antd'

type LoginFormData = {
  email: string
  password: string
  remember: boolean
}
export default function Login({
  status,
  canResetPassword,
}: {
  status?: string
  canResetPassword: boolean
}) {
  const { data, setData, post, processing, errors, clearErrors, reset } =
    useForm({
      email: 'admin@admin.com',
      password: 'secret',
      remember: false,
    })

  useEffect(() => {
    return () => {
      reset('password')
    }
  }, [])

  const submit = () => {
    post(route('frontend.auth.login'))
  }

  const handleFormChange =
    (key: keyof LoginFormData) => (e: React.ChangeEvent<HTMLInputElement>) => {
      if (Object.keys(errors).length !== 0) {
        clearErrors(key)
      }
      setData(key, e.target.value)
    }

  return (
    <>
      <Head title="Log in" />

      {status && (
        <div className="mb-4 font-medium text-sm text-green-600">{status}</div>
      )}

      <Row>
        <Col span={24} md={12}>
          <div
            style={{
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center',
              height: '100vh',
              padding: 10,
            }}
          >
            <Form
              initialValues={data}
              onFinish={submit}
              style={{
                width: 400,
              }}
            >
              <Form.Item
                name="email"
                rules={[
                  {
                    required: true,
                    type: 'email',
                  },
                ]}
                validateStatus={!!errors.email ? 'error' : ''}
                help={errors.email}
              >
                <Input
                  prefix={<UserOutlined className="site-form-item-icon" />}
                  placeholder="email@example.com"
                  disabled={processing}
                  onChange={handleFormChange('email')}
                  autoComplete="email"
                />
              </Form.Item>
              <Form.Item
                name="password"
                rules={[
                  {
                    required: true,
                    message: 'Please input your Password!',
                  },
                ]}
              >
                <Input
                  prefix={<LockOutlined className="site-form-item-icon" />}
                  type="password"
                  placeholder="Password"
                  disabled={processing}
                  onChange={handleFormChange('password')}
                  autoComplete="current-password"
                />
              </Form.Item>
              <Form.Item>
                <Form.Item name="remember" valuePropName="checked" noStyle>
                  <Checkbox
                    disabled={processing}
                    onChange={(e) => {
                      setData('remember', e.target.checked)
                    }}
                  >
                    Remember me
                  </Checkbox>
                </Form.Item>

                {canResetPassword && (
                  <a
                    style={{
                      float: 'right',
                    }}
                    href=""
                  >
                    Forgot password
                  </a>
                )}
              </Form.Item>

              <Form.Item>
                <Button
                  type="primary"
                  htmlType="submit"
                  block
                  loading={processing}
                >
                  Masuk
                </Button>
                atau <a href="">Daftar sekarang!</a>
              </Form.Item>
            </Form>
          </div>
        </Col>
        <Col span={0} md={12}>
          Image
        </Col>
      </Row>
    </>
  )
}
