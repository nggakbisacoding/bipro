import { LogoutOutlined, PlusOutlined } from '@ant-design/icons'
import { Link, router } from '@inertiajs/react'
import {
  Avatar,
  Button,
  Col,
  Divider,
  Dropdown,
  MenuProps,
  Row,
  theme,
} from 'antd'
import { cloneElement, useMemo } from 'react'

type Props = {
  avatar: string
  projects: {
    id: string
    name: string
    isActive: boolean
  }[]
  activeProjectId: string
}
const { useToken } = theme

const menuStyle: React.CSSProperties = {
  boxShadow: 'none',
}

export const RightHeader = ({
  avatar,
  projects = [],
  activeProjectId,
}: Props) => {
  const { token } = useToken()

  const items = useMemo<MenuProps['items']>(
    () => [
      {
        key: '4',
        danger: true,
        label: (
          <Link href={route('frontend.auth.logout')} method="post" as="span">
            Logout
          </Link>
        ),
        icon: <LogoutOutlined />,
      },
    ],
    [],
  )

  const projectItems = useMemo(
    () =>
      projects.map((project) => ({
        key: project.id,
        label: project.name,
        disabled: project.id === activeProjectId,
      })),
    [projects, activeProjectId],
  )

  const activeProject = useMemo(
    () =>
      projects.find((project) => project.id === activeProjectId)?.name ??
      'No Project Selected',
    [projects, activeProjectId],
  )

  const contentStyle: React.CSSProperties = {
    backgroundColor: token.colorBgElevated,
    borderRadius: token.borderRadiusLG,
    boxShadow: token.boxShadowSecondary,
  }

  const handleClickCreateProject = () => {
    router.get(route('admin.project.create'))
  }

  const handleSelectProject = ({ key: projectId }: { key: string }) => {
    router.get(route('admin.project.active', projectId))
  }

  return (
    <Row align="middle" justify="center" gutter={[8, 8]}>
      <Col>
        <Dropdown
          destroyPopupOnHide
          menu={{ items: projectItems, onClick: handleSelectProject }}
          dropdownRender={(menu) => (
            <div style={contentStyle}>
              {cloneElement(menu as React.ReactElement, { style: menuStyle })}
              <Divider style={{ margin: 0 }} />
              <div style={{ width: '100%', padding: 10 }}>
                <Button
                  type="primary"
                  icon={<PlusOutlined />}
                  block
                  onClick={handleClickCreateProject}
                >
                  Create Project
                </Button>
              </div>
            </div>
          )}
        >
          <Button type="text">{activeProject}</Button>
        </Dropdown>
      </Col>
      <Col>
        <Dropdown menu={{ items }}>
          <Button
            type="text"
            icon={<Avatar src={avatar} />}
            style={{
              fontSize: '16px',
              width: 64,
              height: 64,
            }}
          />
        </Dropdown>
      </Col>
    </Row>
  )
}
