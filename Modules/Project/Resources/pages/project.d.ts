import { Pagination } from '@/types'

export type ProjectPageProps = {
  data: {
    data: Project[]
    pagination: Pagination
  }
}

export type ProjectCreatePageProps = {}

export type ProjectEditPageProps = {
  project: Project
}

export type ProjectCreatePageFormData = {}

export type ProjectDetailPageProps = {
  project: Project
  posts: {
    id: string
    username: string
    message: string
  }[]
  keywords: {
    id: string
    name: string
    status: boolean
  }[]
  totalPost: number
  totalKeyword: number
  totalActiveKeyword: number

  sentiments: {
    date: string
    label: string
    value: number
  }[]
}

export type Project = {
  id: string
  name: string
  is_complete: boolean
  created_at: string
}
