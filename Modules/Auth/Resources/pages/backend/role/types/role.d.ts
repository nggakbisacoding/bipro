type Category = {
  id: number
  name: string
  description: string
  type: string
  module: string
  children: Category[]
}

export type RoleProps = {
  role: {
    id: number
    name: string
    type: string
  }
  userPermissions: number[]
  categories: Category[]
  general: Category[]
}
