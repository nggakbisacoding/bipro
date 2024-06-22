import { Checkbox, Form } from 'antd'

type PermissionItemProps = {
  category: any
}
export const PermissionItem = ({ category }: PermissionItemProps) => {
  return (
    <Form.Item
      name={`permissions[${category.id}]`}
      valuePropName="checked"
      noStyle
    >
      <Checkbox>{category.description}</Checkbox>
    </Form.Item>
  )
}
