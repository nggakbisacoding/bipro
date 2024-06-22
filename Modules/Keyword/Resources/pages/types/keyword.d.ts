import { Pagination } from '@/types'

export type KeywordPageProps = {
  data: {
    data: Keyword[]
    pagination: Pagination
  }
}

export type KeywordCreatePageProps = {}

export type KeywordEditPageProps = {
  keyword: Keyword
}

export type KeywordCreatePageFormData = {}

export type Keyword = {
  id: number
  name: string
  total_post: string
  updated_at: string
  source: string
  status: boolean
  type: string
  since?: string
  until?: string
}
