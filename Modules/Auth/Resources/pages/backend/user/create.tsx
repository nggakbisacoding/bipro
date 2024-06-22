import { handleOnSuccess } from '@/Utils/notification'

import { PageProps } from '@/types'
import { Head, useForm } from '@inertiajs/react'
import { Card, Col, Form, Row } from 'antd'
import { useEffect, useState } from 'react'
import { preparePermissions } from '../role/utils'
import { UserInformationForm } from './components'
import { UserPageFormData, UserPageProps } from './types/user'

export default function Users({ roles, categories }: PageProps<UserPageProps>) {
  const [submit, setSubmit] = useState(false)
  const { data, setData, post, processing, errors, reset } = useForm({
    type: 'user',
    name: '',
    email: '',
    password: '',
    active: true,
    email_verified: false,
    send_confirmation_email: true,
    roles: [] as number[],
    permissions: [] as number[],
  })

  useEffect(() => {
    return () => {
      reset('password')
    }
  }, [])

  useEffect(() => {
    if (submit) {
      post(route('admin.users.store'), {
        onSuccess: handleOnSuccess,
      })
    }
  }, [submit])

  const handleSubmit = (e: UserPageFormData) => {
    setData({
      type: e.type,
      name: e.name,
      email: e.email,
      password: e.password,
      active: e.active,
      email_verified: e.email_verified,
      send_confirmation_email: e.send_confirmation_email,
      roles: e.roles,
      permissions: preparePermissions(e),
    })
    setSubmit((prev) => !prev)
  }

  return (
    <>
      <Head title="Create user" />

      <Form
        initialValues={data}
        layout="vertical"
        onFinish={handleSubmit}
        scrollToFirstError
      >
        <Row>
          <Col span={24} order={2} md={{ span: 16, order: 1 }}>
            <Card title="User Information" bordered={false}>
              <UserInformationForm
                loading={processing}
                errors={errors}
                roles={roles}
                permissions={categories}
              />
            </Card>
          </Col>
          <Col span={24} order={1} md={{ span: 8, order: 2 }}></Col>
        </Row>
      </Form>
    </>
  )
}
