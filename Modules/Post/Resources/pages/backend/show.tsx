import { getMentionUrl } from '@/Utils/getMentionUrl'

import { getAttachmentUrl } from '@/Utils/getAttachmentUrl'
import { getProfileImage } from '@/Utils/getProfileImage'
import {
  LikeOutlined,
  MessageOutlined,
  RetweetOutlined,
  TwitterOutlined,
} from '@ant-design/icons'
import { Head, router } from '@inertiajs/react'
import {
  Affix,
  Avatar,
  Button,
  Card,
  Carousel,
  Col,
  DatePicker,
  Empty,
  Form,
  Image,
  List,
  Row,
  Select,
  Space,
  Typography,
} from 'antd'
import { RangePickerProps } from 'antd/es/date-picker'
import dayjs from 'dayjs'
import { createElement, useState } from 'react'
import { Post, PostPageProps } from './types/post'

const MAX_ITEM = 10

const IconText = ({
  icon,
  text,
}: {
  icon: React.FC
  text: string | number
}) => (
  <Space>
    {createElement(icon)}
    {text}
  </Space>
)

type ShowPostPageProps = PostPageProps & {
  postId: string
  hashtags: string[]
}

const disabledDate: RangePickerProps['disabledDate'] = (current) => {
  return current && current > dayjs().endOf('day')
}
export default function PostShow({
  data,
  postId,
  hashtags = [],
  auth,
}: ShowPostPageProps) {
  const [page, setPage] = useState(1)
  const [loading, setLoading] = useState(false)
  const [posts, setPosts] = useState(data.data)

  const currentRoute = route().current() as string

  const handleSubmitFilter = (values: any) => {
    const hashtags = values.hashtags?.join(',') ?? ''
    const dateRange = values.date_range ?? []
    const startDate = dateRange[0]
      ? dateRange[0].format('YYYY-MM-DD HH:mm:ss')
      : ''
    const endDate = dateRange[1]
      ? dateRange[1].format('YYYY-MM-DD HH:mm:ss')
      : ''

    router.get(
      route('admin.post.show', {
        post: postId,
        page: 1,
        start_date: startDate,
        end_date: endDate,
        limit: MAX_ITEM,
        hashtags: encodeURIComponent(hashtags),
      }),
      undefined,
      {
        preserveState: true,
        preserveScroll: true,
        only: ['data'],
        onSuccess: ({ props }) => {
          const data = props.data as any
          setPosts(data.data)
          window.history.replaceState({}, '', route('admin.post.show', postId))

          setPage(1)
        },
      },
    )
  }

  const handlePageChange = (page: number, pageSize: number) => {
    setLoading((prev) => !prev)
    const currentQueries = new URLSearchParams(location.search)
    const searchQuery = currentQueries.get('type')
      ? `?type=${currentQueries.get('type')}`
      : ''

    router.get(
      route(currentRoute, {
        post: postId,
        page,
        limit: pageSize,
      }),
      undefined,
      {
        preserveState: true,
        preserveScroll: false,
        only: ['data'],
        onSuccess: () => {
          setLoading((prev) => !prev)
        },
      },
    )
  }

  return (
    <>
      <Head title="Posts" />

      <Row gutter={[16, 16]}>
        <Col span={24} md={18}>
          {posts.length === 0 ? (
            <Card>
              <Empty
                image={Empty.PRESENTED_IMAGE_SIMPLE}
                description="No posts yet"
              />
            </Card>
          ) : (
            <List
              itemLayout="vertical"
              loading={loading}
              dataSource={data.data}
              pagination={{
                onChange: handlePageChange,
                pageSize: data.pagination.per_page,
                total: data.pagination.total,
                current: data.pagination.current_page,
              }}
              renderItem={(item: Post) => (
                <Card
                  key={item.id}
                  style={{
                    marginBottom: '0.2rem',
                  }}
                  bodyStyle={{
                    padding: '0.725rem 1.5rem',
                  }}
                >
                  <List.Item
                    actions={[
                      <Typography.Link
                        href={item.link}
                        title={item.source}
                        target="_blank"
                      >
                        <IconText
                          icon={TwitterOutlined}
                          text=""
                          key="list-vertical-origin-o"
                        />
                      </Typography.Link>,
                      <IconText
                        icon={LikeOutlined}
                        text={item.stats.like}
                        key="list-vertical-like-o"
                      />,
                      <IconText
                        icon={MessageOutlined}
                        text={item.stats.reply}
                        key="list-vertical-message"
                      />,
                      <IconText
                        icon={RetweetOutlined}
                        text={item.stats.retweet || item.stats.share}
                        key="list-vertical-message"
                      />,
                    ]}
                  >
                    <List.Item.Meta
                      avatar={
                        <Avatar
                          src={getProfileImage({
                            source: item.source.toLowerCase(),
                            username: item.user.username,
                            filename: item.user.avatar,
                          })}
                        />
                      }
                      title={
                        <Row align="middle" justify="space-between">
                          <Col>
                            <Space direction="vertical" size={0}>
                              <Typography.Text>
                                {item.user.name}
                              </Typography.Text>
                              <Typography.Link
                                type="secondary"
                                href={item.user.link}
                                target="_blank"
                              >
                                @{item.user.username}
                              </Typography.Link>
                            </Space>
                          </Col>
                          <Col>{item.date}</Col>
                        </Row>
                      }
                    />
                    <Typography.Paragraph title={item.message}>
                      {getMentionUrl(item.source, item.message, auth.user.type)}
                    </Typography.Paragraph>

                    {item.attachments.length > 0 && (
                      <Carousel
                        style={{
                          width: 250,
                          borderRadius: 10,
                        }}
                      >
                        {item.attachments.map((attachment) => (
                          <div key={attachment.id}>
                            <Image.PreviewGroup
                              items={item.attachments.map((a) =>
                                getAttachmentUrl({
                                  source: item.source,
                                  username: item.user.username,
                                  filename: a.path,
                                }),
                              )}
                              preview={{
                                destroyOnClose: true,
                              }}
                            >
                              <Image
                                key={attachment.id}
                                src={getAttachmentUrl({
                                  source: item.source,
                                  username: item.user.username,
                                  filename: attachment.path,
                                })}
                                style={{
                                  borderRadius: 5,
                                }}
                                width={250}
                                preview={{
                                  destroyOnClose: true,
                                }}
                              />
                            </Image.PreviewGroup>
                          </div>
                        ))}
                      </Carousel>
                    )}
                  </List.Item>
                </Card>
              )}
            ></List>
          )}
        </Col>

        <Col span={0} md={6}>
          <Affix offsetTop={90}>
            <Card
              title="Filters"
              bordered={false}
              bodyStyle={{
                paddingTop: 0,
                paddingBottom: 0,
              }}
            >
              <Form layout="vertical" onFinish={handleSubmitFilter}>
                <Form.Item label="Social Media">
                  <Select
                    allowClear
                    placeholder="Select Social Media"
                    options={['twitter', 'instagram', 'tiktok'].map((o) => ({
                      value: o,
                      label: o.charAt(0).toUpperCase() + o.slice(1),
                    }))}
                  />
                </Form.Item>

                <Form.Item name="date_range" label="Date Range">
                  <DatePicker.RangePicker
                    allowClear
                    disabledDate={disabledDate}
                    showTime
                  />
                </Form.Item>

                <Form.Item name="hashtags" label="Hashtags">
                  <Select
                    allowClear
                    mode="tags"
                    style={{ width: '100%' }}
                    placeholder="Select hashtag"
                    options={hashtags.map((o) => ({
                      value: o,
                      label: o,
                    }))}
                  />
                </Form.Item>

                <Form.Item>
                  <Button type="primary" htmlType="submit">
                    Submit
                  </Button>
                </Form.Item>
              </Form>
            </Card>
          </Affix>
        </Col>
      </Row>
    </>
  )
}
