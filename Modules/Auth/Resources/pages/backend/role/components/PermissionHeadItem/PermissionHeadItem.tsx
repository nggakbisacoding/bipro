import { Checkbox, Form } from 'antd'
import { useEffect } from 'react'

type PermissionHeadItemProps = {
  category: any
  isIndeterminate?: boolean
}

type PermissionHeadItemRenderProps = {
  category: any
  isIndeterminate?: boolean
  isAllChecked?: boolean
  setFieldValue: any
}

const PermissionHeadItemRender = ({
  category,
  isIndeterminate,
  isAllChecked,
  setFieldValue,
}: PermissionHeadItemRenderProps) => {
  const form = Form.useFormInstance()

  useEffect(() => {
    if (!isIndeterminate) {
      setFieldValue(`permissions[${category.id}]`, isAllChecked)
    } else if (!isAllChecked) {
      setFieldValue(`permissions[${category.id}]`, false)
    }
  }, [isAllChecked, isIndeterminate])

  const handleClickCheckBoxAll = (e: any) => {
    const updateFields = category.children
      .map((child: any) => {
        return {
          key: child.id,
          value: e.target.checked,
        }
      })
      .reduce(
        (acc: any, curr: any) => ({
          ...acc,
          [`permissions[${curr.key}]`]: curr.value,
        }),
        {},
      )
    form.setFieldsValue(updateFields)
  }
  return (
    <Form.Item
      key={category.id}
      name={`permissions[${category.id}]`}
      valuePropName="checked"
      noStyle
    >
      <Checkbox
        indeterminate={isIndeterminate}
        onClick={handleClickCheckBoxAll}
      >
        {category.description}
      </Checkbox>
    </Form.Item>
  )
}
export const PermissionHeadItem = ({ category }: PermissionHeadItemProps) => {
  return (
    <Form.Item
      dependencies={category.children.map(
        (child: any) => `permissions[${child.id}]`,
      )}
      noStyle
    >
      {({ getFieldValue, setFieldValue }) => {
        const allChildrenValues = category.children.map((child: any) =>
          getFieldValue(`permissions[${child.id}]`),
        )

        const isSomeChecked = allChildrenValues.some((value: any) => value)
        const isAllChecked = allChildrenValues.every((value: any) => value)

        return (
          <PermissionHeadItemRender
            category={category}
            setFieldValue={setFieldValue}
            isAllChecked={isAllChecked}
            isIndeterminate={!isAllChecked && isSomeChecked}
          />
        )
      }}
    </Form.Item>
  )
}
