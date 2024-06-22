import { handleOnSuccess } from '@/Utils/notification'

import { useForm } from '@inertiajs/react'
import { Card, Form, Modal } from 'antd'
import { useEffect, useState } from 'react'
import { KeywordForm } from '..'

export const CreateKeywordForm = () => {
  const [submit, setSubmit] = useState(false)
  const { data, setData, post, processing, errors } = useForm({
    username: '',
    name: '',
    source: '',
    total_post: 0,
    status: true,
    type: 'account',
  })

  useEffect(() => {
    if (submit) {
      post(route('frontend.user.keyword.store'), {
        onSuccess: handleOnSuccess,
      })
    }
  }, [submit])

  const handleSubmit = (e: typeof data) => {
    Modal.confirm({
      title: 'Are you sure?',
      content: 'Your data will be crawled immediately after saving.',
      okText: 'Save',
      onOk: () => {
        setData(e)
        setSubmit((prev) => !prev)
      },
    })
  }

  return (
    <Form
      initialValues={data}
      layout="vertical"
      onFinish={handleSubmit}
      scrollToFirstError
    >
      <Card title="Add New Keyword" bordered={false}>
        <KeywordForm loading={processing} errors={errors} />
      </Card>
    </Form>
  )
}
