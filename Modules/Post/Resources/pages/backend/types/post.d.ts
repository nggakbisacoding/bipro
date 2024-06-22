import { PageProps, Pagination } from '@/types'

export type PostPageProps = PageProps<{
  data: {
    data: Post[]
    pagination: Pagination
  }
}>

export type Post = {
  id: number
  account_keyword: string
  username: string
  name: string
  message: string
  source: string
  stats: PostStats
  date: string
  link: string

  user: {
    name: string
    username: string
    avatar: string
    link: string
  }

  attachments: {
    id: string
    path: string
  }[]
}

export type PostStats = {
  like: number
  reply: number
  retweet: number
  quote_retweet: number
  is_retweet: boolean
  share: number
}
