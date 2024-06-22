import { handleOnSuccess } from '@/Utils/notification'

import { PageProps } from '@/types'
import { Head, useForm } from '@inertiajs/react'
import { Card, Form } from 'antd'
import { useEffect, useState } from 'react'
import { RoleForm } from './components'
import { RoleProps } from './types/role'
import { preparePermissions } from './utils'

export default function Users({ categories, general }: PageProps<RoleProps>) {
  const [submit, setSubmit] = useState(false)
  const { data, setData, post, processing, errors } = useForm({
    type: 'user',
    name: '',
    permissions: [] as number[],
  })

  useEffect(() => {
    if (submit) {
      post(route('admin.roles.store'), {
        onSuccess: handleOnSuccess,
      })
    }
  }, [submit])

  const handleSubmit = async (e: any) => {
    const permissions = preparePermissions(e)

    setData({ name: e.name, type: e.type, permissions })
    setSubmit((prev) => !prev)
  }

  return (
    <>
      <Head title="Create role" />

      <Form
        initialValues={data}
        layout="vertical"
        onFinish={handleSubmit}
        scrollToFirstError
      >
        <Card title="Create Role" bordered={false}>
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
