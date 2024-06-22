import { FormSubmitButton } from '@/Components'
import { router } from '@inertiajs/react'
import { Form, Input } from 'antd'
import { FC } from 'react'

type ProjectForm = {
  loading?: boolean
  errors?: Partial<Record<'name', string>>
}
export const ProjectForm: FC<ProjectForm> = ({ loading = false, errors }) => {
  const handleCancelProject = () => {
    router.get(route('admin.project.index'))
  }

  return (
    <>
      <Form.Item name="name" label="Name" rules={[{ required: true }]}>
        <Input placeholder="My Greatest Project" disabled={loading} />
      </Form.Item>

      <Form.Item name="description" label="Description">
        <Input.TextArea placeholder="Long description" disabled={loading} />
      </Form.Item>

      <Form.Item>
        <FormSubmitButton loading={loading} onClickBack={handleCancelProject} />
      </Form.Item>
    </>
  )
}
