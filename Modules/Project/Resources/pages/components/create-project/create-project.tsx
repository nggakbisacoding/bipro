import { handleOnSuccess } from '@/Utils/notification'
import { useForm } from '@inertiajs/react'
import { Card, Form } from 'antd'
import { ProjectForm } from '..'

export const CreateProject = () => {
  const { setData, data, post, processing, errors } = useForm({
    name: '',
    description: '',
  })

  const handleSubmit = () => {
    post(route('admin.project.store'), {
      onSuccess: handleOnSuccess,
    })
  }
  return (
    <Card bordered={false} title="Create Project">
      <Form
        layout="vertical"
        scrollToFirstError
        onFinish={handleSubmit}
        onValuesChange={(_, values) => {
          setData(values)
        }}
      >
        <ProjectForm errors={errors} loading={processing} />
      </Form>
    </Card>
  )
}
