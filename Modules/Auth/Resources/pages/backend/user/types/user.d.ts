export type UserPageProps = {
  user: User
  roles: Roles
  categories: Permission
}

export type User = {
  id: number
  type: string
  name: string
  email: string
  email_verified_at: string
  password_changed_at: any
  active: boolean
  timezone: any
  last_login_at: any
  last_login_ip: any
  to_be_logged_out: boolean
  provider: any
  provider_id: any
  created_at: string
  updated_at: string
  deleted_at: any
  avatar: string
  permissions: any[]
  roles: any[]
}

export type Roles = {
  id: number
  type: string
  name: string
  guard_name: string
  created_at: string
  updated_at: string
  permissions: Permission[]
}

export type Permission = {
  id: number
  type: string
  name: string
  module: string
  guard_name: string
  description: string
  parent_id: number
  sort: number
  created_at: string
  updated_at: string
  pivot: Pivot
}

export type Pivot = {
  role_id: number
  permission_id: number
}

export type UserPageFormData = {
  [key: string]: string | boolean | number[] | string[]
  type: string
  name: string
  email: string
  password: string
  active: boolean
  email_verified: boolean
  send_confirmation_email: boolean
  roles: number[]
}
