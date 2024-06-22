import { handleOnSuccess } from '@/Utils/notification'

import { PageProps } from '@/types'
import { Head, useForm } from '@inertiajs/react'
import { Card, Col, Form, Row } from 'antd'
import { omit } from 'radash'
import { useEffect, useState } from 'react'
import { prepareInitialPermissions, preparePermissions } from '../role/utils'
import { UserInformationForm } from './components'
import { UserPageFormData, UserPageProps } from './types/user'

export default function Users({
  roles,
  categories,
  user,
}: PageProps<UserPageProps>) {
  const [submit, setSubmit] = useState(false)
  const { data, setData, put, processing, errors } = useForm({
    type: user.type,
    name: user.name,
    email: user.email,
    active: user.active,
    email_verified: user.email_verified_at ? true : false,
    send_confirmation_email: user.email_verified_at ? false : true,
    roles: user.roles.map((role) => role.id),
    permissions: user.permissions.map((permission) => permission.id),
  })

  useEffect(() => {
    if (submit) {
      put(route('admin.users.update', user.id), {
        onSuccess: handleOnSuccess,
      })
    }
  }, [submit])

  const handleSubmit = (e: UserPageFormData) => {
    const permissions = preparePermissions(e)
    setData({
      type: e.type,
      name: e.name,
      email: e.email,
      active: e.active,
      email_verified: e.email_verified,
      send_confirmation_email: e.send_confirmation_email,
      roles: e.roles,
      permissions,
    })
    setSubmit((prev) => !prev)
  }

  return (
    <>
      <Head title="Edit user" />

      <Form
        initialValues={{
          ...omit(data, ['permissions']),
          ...prepareInitialPermissions(data.permissions),
        }}
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
                user={user}
              />
            </Card>
          </Col>
          <Col span={24} order={1} md={{ span: 8, order: 2 }}></Col>
        </Row>
      </Form>
    </>
  )
}
