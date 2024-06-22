import { PageProps } from '@/types'
import { ExclamationCircleFilled } from '@ant-design/icons'
import { router } from '@inertiajs/react'
import { Modal, notification } from 'antd'
import { createElement } from 'react'

export const handleOnSuccess = ({ props }: any) => {
  const iProps = props as unknown as PageProps

  const isSuccess = iProps.notification.success
  notification[isSuccess ? 'success' : 'error']({
    message: iProps.notification.success || iProps.notification.error,
  })
}

export const handleOnError = ({ props }: any) => {
  const iProps = props as unknown as PageProps
  notification.error({
    message: iProps.notification.error,
  })
}

export const handleShowConfirmModal = (route: string) => () => {
  Modal.confirm({
    title: 'Do you want to delete these items?',
    icon: createElement(ExclamationCircleFilled),
    content: 'Some descriptions',
    onOk() {
      router.delete(route, {
        onSuccess: handleOnSuccess,
        onError: (e) => {
          console.log('error', e)
        },
      })
    },
  })
}

export const showSuccessNotification = ({
  title,
  description,
}: {
  title: string
  description: string
}) => {
  notification.success({
    message: title,
    description,
  })
}
