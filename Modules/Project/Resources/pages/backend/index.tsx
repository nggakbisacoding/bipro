import { handleOnSuccess } from '@/Utils/notification'
import { PageProps } from '@/types'
import { DeleteOutlined } from '@ant-design/icons'
import { Head, Link, router } from '@inertiajs/react'
import { Button, Card, Col, Modal, Row } from 'antd'
import { FC } from 'react'
import { ProjectPageProps } from '../project'

const ProjectIndex: FC<PageProps<ProjectPageProps>> = ({ data }) => {
  const handleCreateProject = () => {
    router.get(route('admin.project.create'))
  }

  const handleConfirmDelete = (projectId: string) => () => {
    Modal.confirm({
      title: 'Are you sure?',
      content: 'Your data will be deleted immediately after saving.',
      okText: 'Delete',
      onOk: () => {
        router.delete(route('admin.project.destroy', projectId), {
          onSuccess: handleOnSuccess,
        })
      },
    })
  }

  return (
    <>
      <Head title="Projects" />

      <Row
        gutter={[
          {
            xs: 0,
            md: 16,
          },
          { xs: 8, md: 16 },
        ]}
      >
        <Col span={24}>
          <Button type="primary" onClick={handleCreateProject}>
            Create Project
          </Button>
        </Col>

        {data.data.map((d) => (
          <Col span={24} md={6} key={d.id}>
            <Card
              title={
                <Link href={route('admin.project.show', d.id)}>{d.name}</Link>
              }
              extra={
                <Button
                  size="small"
                  type="primary"
                  ghost
                  danger
                  icon={<DeleteOutlined />}
                  onClick={handleConfirmDelete(d.id)}
                />
              }
            >
              <span>{d.created_at}</span>
            </Card>
          </Col>
        ))}
      </Row>
    </>
  )
}

export default ProjectIndex
