import { PageProps } from '@/types'
import { Head, Link, router } from '@inertiajs/react'
import { Card, Col, Row, Select, Statistic, Table, Tag, Tooltip } from 'antd'
import { FC, Suspense, lazy, useEffect } from 'react'
import { ProjectDetailPageProps } from '../project'

import { LineConfig } from '@ant-design/plots'

const Line = lazy(() =>
  import('@ant-design/plots').then((mod) => ({ default: mod.Line })),
)
const ProjectDetail: FC<PageProps<ProjectDetailPageProps>> = ({
  query,
  project,
  posts,
  totalPost,
  totalActiveKeyword,
  totalKeyword,
  keywords,
  sentiments,
}) => {
  useEffect(() => {
    router.reload({
      only: ['sentiments'],
    })
  }, [])

  const config: LineConfig = {
    data: sentiments ? sentiments : [],
    xField: 'date',
    yField: 'value',
    seriesField: 'label',
    xAxis: {
      type: 'time',
    },
    loading: !sentiments,
    smooth: true,
    color: ({ label }: any) => {
      if (label.toLowerCase() === 'positive') {
        return '#ADF7B6'
      }
      if (label.toLowerCase() === 'negative') {
        return '#C97064'
      }

      return ''
    },
  }

  const handleChangeSentimentFilter = (e: string) => {
    router.reload({
      only: ['sentiments', 'query'],
      data: {
        sf: e,
      },
    })
  }

  return (
    <>
      <Head title="Project Detail" />

      <Row gutter={[8, 8]}>
        <Col span={24}>
          <Row gutter={[8, 8]}>
            <Col span={24} md={8}>
              <Card bordered={false}>
                <Statistic value={totalPost} title="Total Posts" />
              </Card>
            </Col>
            <Col span={24} md={8}>
              <Card bordered={false}>
                <Statistic value={totalKeyword} title="Target Crawl" />
              </Card>
            </Col>
            <Col span={24} md={8}>
              <Card bordered={false}>
                <Statistic
                  value={totalActiveKeyword}
                  title="Active Target Crawl"
                />
              </Card>
            </Col>
          </Row>
        </Col>

        <Col span={24} lg={12}>
          <Card
            title="Latest Target Crawl"
            bordered={false}
            extra={<Link href={route('admin.keyword.index')}>View More</Link>}
          >
            <Table
              bordered={false}
              rowKey={(r) => r.id}
              columns={[
                {
                  dataIndex: ['name'],
                  title: 'Name',
                  ellipsis: true,
                },
                {
                  dataIndex: ['type'],
                  title: 'Type',
                },
                {
                  dataIndex: ['status'],
                  title: 'Status',
                  render: (value) => (
                    <Tag color={value ? 'green' : 'red'}>
                      {value ? 'Active' : 'Inactive'}
                    </Tag>
                  ),
                },
                {
                  dataIndex: ['created_at'],
                  title: 'Created At',
                },
              ]}
              dataSource={keywords}
              pagination={{
                hideOnSinglePage: true,
              }}
              scroll={{
                y: 250,
                x: 500,
              }}
              style={{
                minHeight: 300,
                maxHeight: 300,
              }}
            />
          </Card>
        </Col>

        <Col span={24} lg={12}>
          <Card
            title="Latest Posts"
            bordered={false}
            extra={<Link href={route('admin.post.index')}>View More</Link>}
          >
            <Table
              bordered={false}
              rowKey={(r) => r.id}
              columns={[
                {
                  dataIndex: ['username'],
                  title: 'Username',
                },
                {
                  dataIndex: ['message'],
                  title: 'Message',
                  ellipsis: true,
                  render: (value) => (
                    <Tooltip
                      destroyTooltipOnHide
                      placement="topLeft"
                      title={value}
                    >
                      {value}
                    </Tooltip>
                  ),
                },
                {
                  dataIndex: ['date'],
                  title: 'Date',
                },
              ]}
              dataSource={posts}
              pagination={{
                hideOnSinglePage: true,
              }}
              scroll={{
                y: 250,
                x: 500,
              }}
              style={{
                minHeight: 300,
                maxHeight: 300,
              }}
            />
          </Card>
        </Col>

        <Col span={24}>
          <Card
            bordered={false}
            title="Sentiment Analysis"
            extra={
              <Select
                defaultActiveFirstOption
                style={{
                  width: 150,
                }}
                defaultValue={query['sf'] ?? '7_days'}
                value={query['sf']}
                onChange={handleChangeSentimentFilter}
              >
                <Select.Option value="7_days">Last 7 days</Select.Option>
                <Select.Option value="30_days">Last 30 days</Select.Option>
                <Select.Option value="90_days">Last 90 days</Select.Option>
              </Select>
            }
          >
            <Suspense>
              <Line {...config} />
            </Suspense>
          </Card>
        </Col>
      </Row>
    </>
  )
}

export default ProjectDetail
