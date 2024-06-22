export interface User {
  id: number
  name: string
  email: string
  email_verified_at: string
  type: 'admin' | 'user'
  avatar: string
  last_login_at: string
  last_login_ip: string
}

export type PageProps<
  T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
  auth: {
    user: User
    activeProjectId: string
  }
  notification: {
    error?: string
    success?: string
  }
  ziggy: {
    location: string
  }
  query: Record<string, string>
}

export type Pagination = {
  current_page: number
  per_page: number
  total: number
}
