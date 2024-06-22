import {
  CheckCircleOutlined,
  CloseOutlined,
  MenuFoldOutlined,
  MenuUnfoldOutlined,
} from '@ant-design/icons'
import {
  Button,
  Card,
  Grid,
  Layout,
  Menu,
  MenuProps,
  Result,
  Space,
  Typography,
  theme,
} from 'antd'
import { PropsWithChildren, useEffect, useState } from 'react'

import { router, usePage } from '@inertiajs/react'
import { ItemType, MenuItemType } from 'antd/es/menu/hooks/useItems'
import styles from './AuthenticatedLayout.module.css'
import { RightHeader } from './components'

const { Header, Content, Footer, Sider } = Layout
const { useBreakpoint } = Grid

type MenuItem = Required<MenuProps>['items'][number]
type Props = PropsWithChildren<{
  menus?: ItemType<MenuItemType>[]
}>
export default function Authenticated({ children, menus }: Props) {
  const {
    auth: { user, activeProjectId },
  } = usePage().props as any
  const {
    token: { colorBgContainer },
  } = theme.useToken()

  const screens = useBreakpoint()

  const isXs = Object.keys(screens).length === 0 || screens.xs
  const [collapsed, setCollapsed] = useState(isXs)

  const [scrollDown, setScrollDown] = useState(false)
  useEffect(() => {
    const handleScroll = () => {
      const scrollTop = window.scrollY || document.documentElement.scrollTop
      setScrollDown(scrollTop > 90)
    }

    window.addEventListener('scroll', handleScroll)
    return () => {
      window.removeEventListener('scroll', handleScroll)
    }
  }, [])

  const handleToggleCollapse = () => {
    setCollapsed((prev) => !prev)
  }

  if (Object.keys(screens).length === 0) return null

  const handleClickMenu = () => {
    if (isXs) {
      setCollapsed(true)
    }
  }

  const renderContent = () => {
    if (
      !activeProjectId &&
      !['project', 'users', 'roles'].some((s) => route().current()?.includes(s))
    ) {
      const handleClickCreateProject = () => {
        router.get(route('admin.project.create'))
      }
      const handleClickBackToHome = () => {
        router.get(route('admin.insight.index'))
      }
      return (
        <Card>
          <Result
            status="warning"
            title="Please create or select a project first."
            subTitle="You can create a project by clicking the button below."
            extra={[
              <Button
                type="primary"
                key="create-new-project"
                onClick={handleClickCreateProject}
              >
                Create Project
              </Button>,
              <Button key="back-to-home" onClick={handleClickBackToHome}>
                Back to Home
              </Button>,
            ]}
          >
            <div className="desc">
              <Typography.Paragraph>
                <Typography.Text
                  strong
                  style={{
                    fontSize: 16,
                  }}
                >
                  How to create a project?
                </Typography.Text>
              </Typography.Paragraph>
              <Typography.Paragraph>
                <CheckCircleOutlined className="site-result-demo-error-icon" />{' '}
                Click the above button to create a project.
              </Typography.Paragraph>
              <Typography.Paragraph>
                <CheckCircleOutlined className="site-result-demo-error-icon" />{' '}
                Fill in the required fields.
              </Typography.Paragraph>
              <Typography.Paragraph>
                <CheckCircleOutlined className="site-result-demo-error-icon" />{' '}
                Click the button to save the project.
              </Typography.Paragraph>
            </div>
          </Result>
        </Card>
      )
    }

    return children
  }

  return (
    <>
      <Layout hasSider className={styles.layout}>
        <div
          style={{
            position: 'relative',
          }}
        >
          <div
            style={{
              position: 'absolute',
              left: 0,
              top: 0,
              bottom: 0,
              zIndex: 11,
            }}
          >
            <Sider
              trigger={null}
              collapsible
              collapsed={collapsed}
              breakpoint="lg"
              collapsedWidth={isXs ? 0 : 80}
              className={styles.sidebar}
              onCollapse={setCollapsed}
            >
              <div
                style={{
                  height: 32,
                  margin: 16,
                  backgroundColor: 'rgba(255,255,255,.2)',
                  borderRadius: 6,
                }}
              />
              <Menu
                theme="dark"
                mode="inline"
                defaultSelectedKeys={['4']}
                items={menus}
                onClick={handleClickMenu}
              />
            </Sider>
          </div>
        </div>
        <Layout
          className="site-layout"
          style={
            isXs ? { marginLeft: 0 } : { marginLeft: collapsed ? 80 : 200 }
          }
        >
          {isXs && !collapsed ? (
            <>
              <div
                className={styles['overlay-menu']}
                onClick={handleToggleCollapse}
              />
              <div
                className={styles['overlay-menu-button-container']}
                onClick={handleToggleCollapse}
              >
                <div className={styles['overlay-menu-button']}>
                  <CloseOutlined
                    style={{
                      fontSize: '18px',
                      color: '#fff',
                    }}
                  />
                </div>
              </div>
            </>
          ) : null}

          <Header
            style={{ background: colorBgContainer }}
            className={[
              styles.header,
              scrollDown ? styles.headerScroll : '',
            ].join(' ')}
          >
            <Space
              direction="horizontal"
              align="center"
              style={{
                width: '100%',
                justifyContent: 'space-between',
              }}
            >
              <Button
                type="text"
                icon={collapsed ? <MenuUnfoldOutlined /> : <MenuFoldOutlined />}
                onClick={handleToggleCollapse}
                style={{
                  fontSize: '16px',
                  width: 64,
                  height: 64,
                }}
              />

              <RightHeader
                avatar={user.avatar}
                projects={user.projects}
                activeProjectId={activeProjectId}
              />
            </Space>
          </Header>
          <Content className={styles.content}>{renderContent()}</Content>
          <Footer style={{ textAlign: 'center' }}>X ©2023 Created by X</Footer>
        </Layout>
      </Layout>
    </>
  )
}

export function getMenuItem(
  label: React.ReactNode,
  key: React.Key,
  icon?: React.ReactNode,
  children?: MenuItem[],
  type?: 'group',
): MenuItem {
  return {
    key,
    icon,
    children,
    label,
    type,
  } as MenuItem
}
