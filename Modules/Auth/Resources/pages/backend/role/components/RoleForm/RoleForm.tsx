import { Button, Collapse, Form, Grid, Input, Select } from 'antd'
import { useEffect } from 'react'

import { Permissions } from '..'

export const RoleForm = ({
  loading,
  errors,
  categories = [],
  general = [],
}: any) => {
  const screens = Grid.useBreakpoint()

  const form = Form.useFormInstance()
  const formType = Form.useWatch('type')
  useEffect(() => {
    form.setFieldValue('roles', [])
  }, [formType])

  const getGeneralPermissionsByType = (type: any) => {
    return general.filter((general: any) => general.type === type)
  }
  const getCategoryPermissionsByType = (type: any) => {
    return categories.filter((category: any) => category.type === type)
  }
  return (
    <>
      <Form.Item
        label="Type"
        name="type"
        rules={[{ required: true }]}
        validateStatus={!!errors.type ? 'error' : ''}
        help={errors.type}
      >
        <Select placeholder="Select a type" disabled={loading}>
          <Select.Option value="admin">Admin</Select.Option>
          <Select.Option value="user">User</Select.Option>
        </Select>
      </Form.Item>

      <Form.Item
        label="Name"
        name="name"
        rules={[{ required: true }]}
        validateStatus={!!errors.name ? 'error' : ''}
        help={errors.name}
      >
        <Input placeholder="Name" disabled={loading} allowClear />
      </Form.Item>

      {getGeneralPermissionsByType(formType).length > 0 && (
        <Form.Item label="General Permissions">
          <Collapse
            ghost
            items={getGeneralPermissionsByType(formType).map(
              (category: any) => ({
                key: category.id,
                label: category.description,
                children: category.description,
              }),
            )}
          />
        </Form.Item>
      )}

      <Form.Item label="Additional Permissions">
        {getCategoryPermissionsByType(formType).length > 0 ? (
          <Permissions items={getCategoryPermissionsByType(formType)} />
        ) : (
          'There are no additional permissions to choose from for this type.'
        )}
      </Form.Item>

      <Form.Item
        style={{
          textAlign: 'right',
        }}
      >
        <Button
          type="primary"
          size="large"
          htmlType="submit"
          block={screens.xs}
          loading={loading}
        >
          Save
        </Button>
      </Form.Item>
    </>
  )
}
