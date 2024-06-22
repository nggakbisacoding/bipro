import { handleOnSuccess } from '@/Utils/notification'
import { PageProps } from '@/types'
import { Head, useForm } from '@inertiajs/react'
import { Card, Form } from 'antd'
import { useEffect, useState } from 'react'
import { RoleForm } from './components'

import { omit } from 'radash'
import { RoleProps } from './types/role'
import { prepareInitialPermissions, preparePermissions } from './utils'

export default function Edit({
  categories,
  general,
  role,
  userPermissions,
}: PageProps<RoleProps>) {
  const [submit, setSubmit] = useState(false)
  const { data, setData, put, processing, errors } = useForm({
    type: role.type,
    name: role.name,
    permissions: userPermissions,
  })

  useEffect(() => {
    if (submit) {
      put(route('admin.roles.update', role.id), {
        onSuccess: handleOnSuccess,
      })
    }
  }, [submit])

  const handleSubmit = async (e: any) => {
    const permissions = preparePermissions(e)

    setData({ name: e.name, type: e.type, permissions })
    setSubmit((prev) => !prev)
  }

  const initialValuePermissions = prepareInitialPermissions(data.permissions)

  return (
    <>
      <Head title="Edit role" />

      <Form
        initialValues={{
          ...omit(data, ['permissions']),
          ...initialValuePermissions,
        }}
        layout="vertical"
        onFinish={handleSubmit}
        scrollToFirstError
      >
        <Card title="Edit Role" bordered={false}>
          <RoleForm
            loading={processing}
            errors={errors}
            categories={categories}
            general={general}
          />
        </Card>
      </Form>
    </>
  )
}
