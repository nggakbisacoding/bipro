import { handleOnSuccess } from '@/Utils/notification'

import { router, useForm } from '@inertiajs/react'
import { Card, Form, Modal } from 'antd'
import { FC, useEffect, useState } from 'react'
import { KeywordForm } from '..'
import { Keyword } from '../../../types/keyword'

type Props = {
  keyword: Keyword
}
export const EditKeywordForm: FC<Props> = ({ keyword }) => {
  const [form] = Form.useForm()
  const [submit, setSubmit] = useState(false)

  const { data, setData, put, processing, errors } = useForm({
    name: keyword.name,
    source: keyword.source,
    status: Number(keyword.status) === 1,
    type: keyword.type,
    date: [keyword.since, keyword.until],
    is_monitor: undefined,
  })

  useEffect(() => {
    if (submit) {
      put(route('admin.keyword.update', keyword.id), {
        onSuccess: handleOnSuccess,
      })
    }
  }, [submit])

  const handleSubmit = (e: typeof data) => {
    if (form.isFieldsTouched()) {
      Modal.confirm({
        title: 'Are you sure?',
        content: 'Ini akan memulai dari awal lagi loh.',
        okText: 'Save',
        onOk: () => {
          setData(e)
          setSubmit((prev) => !prev)
        },
      })
      return
    }

    router.get(route('admin.keyword.index'))
  }

  return (
    <Form
      initialValues={data}
      layout="vertical"
      onFinish={handleSubmit}
      scrollToFirstError
      form={form}
    >
      <Card title="Edit Target Crawl" bordered={false}>
        <KeywordForm loading={processing} errors={errors} />
      </Card>
    </Form>
  )
}
