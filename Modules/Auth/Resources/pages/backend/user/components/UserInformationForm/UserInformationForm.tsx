import type { CollapseProps } from 'antd'
import {
  Button,
  Checkbox,
  Collapse,
  Form,
  Grid,
  Input,
  Select,
  Switch,
  Typography,
} from 'antd'
import { useEffect } from 'react'
import { Permissions } from '../../../role/components'
import { Roles } from '../../types/user'

export const UserInformationForm = ({
  loading,
  errors,
  roles,
  permissions = [],
  user,
}: any) => {
  const screens = Grid.useBreakpoint()

  const form = Form.useFormInstance()
  const formType = Form.useWatch('type')
  useEffect(() => {
    if (!user?.id) {
      form.setFieldValue('roles', [])
    }
  }, [formType, user])

  const getRolesByType = roles.filter((role: any) => role.type === formType)
  const getPermissionsByType = permissions.filter(
    (permission: any) => permission.type === formType,
  )

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

      <Form.Item
        label="Email"
        name="email"
        rules={[{ required: true, type: 'email' }]}
        validateStatus={!!errors.email ? 'error' : ''}
        help={errors.email}
      >
        <Input placeholder="Email" type="email" disabled={loading} allowClear />
      </Form.Item>

      <Form.Item
        label="Password"
        name="password"
        rules={[{ required: !user?.id, min: 8 }]}
        validateStatus={!!errors.password ? 'error' : ''}
        help={user?.id ? 'Leave blank to keep current' : errors.password}
      >
        <Input.Password placeholder="Password" disabled={loading} allowClear />
      </Form.Item>

      <Form.Item
        label="Active"
        name="active"
        valuePropName="checked"
        rules={[{ required: true }]}
        validateStatus={!!errors.active ? 'error' : ''}
        help={errors.active}
      >
        <Switch disabled={loading} />
      </Form.Item>

      <Form.Item
        label="Email Verified"
        name="email_verified"
        valuePropName="checked"
        validateStatus={!!errors.email_verified ? 'error' : ''}
        help={errors.email_verified}
      >
        <Switch />
      </Form.Item>

      <Form.Item dependencies={['email_verified']} noStyle>
        {({ getFieldValue }) =>
          !getFieldValue('email_verified') && (
            <Form.Item
              label="Send Confirmation Email"
              name="send_confirmation_email"
              valuePropName="checked"
              validateStatus={!!errors.send_confirmation_email ? 'error' : ''}
              help={errors.send_confirmation_email}
            >
              <Switch disabled={loading} />
            </Form.Item>
          )
        }
      </Form.Item>

      <Form.Item dependencies={['type']} noStyle>
        {() => (
          <Form.Item
            label="Roles"
            name={getRolesByType.length > 0 ? 'roles' : undefined}
            rules={[{ required: getRolesByType.length > 0 }]}
          >
            {getRolesByType.length > 0 ? (
              <Select
                mode="multiple"
                options={getRolesByType.map((role: any) => ({
                  value: role.id,
                  label: role.name,
                }))}
                disabled={loading}
                placeholder="Select Roles"
                allowClear
              />
            ) : (
              'There are no roles to choose from for this type.'
            )}
          </Form.Item>
        )}
      </Form.Item>

      <Form.Item dependencies={['roles']} label="Permissions">
        {({ getFieldValue }) => {
          if (getFieldValue('roles').length === 0) {
            return 'No Permissions'
          }

          const permissionsRoles: CollapseProps['items'] = (
            getRolesByType as Roles[]
          )
            .filter((f: Roles) => getFieldValue('roles').includes(f.id))
            .map((role: Roles) => ({
              key: role.id,
              label: <Typography.Text strong>{role.name}</Typography.Text>,
              children:
                role.name === 'Administrator' ? (
                  <Typography.Text strong>All Permissions</Typography.Text>
                ) : (
                  <>
                    {role.permissions.length > 0 ? (
                      role.permissions.map((permission: any) => (
                        <Checkbox key={permission.id} checked disabled>
                          {permission.description}
                        </Checkbox>
                      ))
                    ) : (
                      <Typography.Text strong>No Permissions</Typography.Text>
                    )}
                  </>
                ),
            }))

          return (
            <Collapse
              expandIconPosition="end"
              ghost
              accordion
              items={permissionsRoles}
            />
          )
        }}
      </Form.Item>

      <Form.Item dependencies={['type']} label="Additional Permissions">
        {() =>
          getPermissionsByType.length > 0 ? (
            <Permissions items={getPermissionsByType} />
          ) : (
            'There are no additional permissions to choose from for this type.'
          )
        }
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
