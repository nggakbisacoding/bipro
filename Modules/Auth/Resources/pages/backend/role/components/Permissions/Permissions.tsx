import { Collapse } from 'antd'
import { PermissionHeadItem } from '../PermissionHeadItem/PermissionHeadItem'
import { PermissionItem } from '../PermissionItem/PermissionItem'

type PermissionsProps = {
  items: any
}
export const Permissions = ({ items }: PermissionsProps) => {
  return (
    <Collapse
      accordion
      ghost
      expandIconPosition="end"
      items={items.map((category: any) => {
        return {
          key: category.id,
          label: <PermissionHeadItem category={category} />,
          forceRender: true,
          children: (
            <>
              {category.children.map((child: any) => (
                <PermissionItem key={child.id} category={child} />
              ))}
            </>
          ),
        }
      })}
    />
  )
}
