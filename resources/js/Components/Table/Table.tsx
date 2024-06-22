import { SearchOutlined } from '@ant-design/icons'
import { router } from '@inertiajs/react'
import type { TableProps as AntTableProps, TablePaginationConfig } from 'antd'
import { Table as AntTable, Col, Input, Row, Tooltip } from 'antd'
import type {
  ColumnType,
  FilterValue,
  SorterResult,
} from 'antd/es/table/interface'
import { debounce } from 'radash'
import { ChangeEvent, useEffect, useMemo, useState } from 'react'

import styles from './Table.module.css'

export interface TableProps {
  tableProps: AntTableProps<any>
}

export function Table<T>({
  ...props
}: Omit<AntTableProps<any>, 'dataSource'> & {
  dataSource: {
    data: T[]
    pagination: {
      total: number
      current_page: number
      per_page: number
    }
  }
  route: string
}) {
  const currentQueries = new URLSearchParams(location.search)
  const [search, setSearch] = useState(currentQueries.get('q') || '')
  const { columns = [], dataSource, route, ...restTableProps } = props
  const { data, pagination } = dataSource

  const defaultColumns = useMemo<ColumnType<any>[]>(
    () =>
      columns.map((column: ColumnType<any>) => {
        const url = new URLSearchParams(location.href)

        return {
          render: (val) => (
            <Tooltip placement="topLeft" title={val}>
              {val}
            </Tooltip>
          ),
          ...column,
          ellipsis: column.ellipsis === false ? false : true,
          sorter: column.sorter === false ? (column.sorter as any) : true,
          defaultSortOrder:
            url.get('field') === column.dataIndex
              ? (url.get('order') as any)
              : '',
        }
      }),
    [],
  )

  const handleChangeTable = (
    pagination: TablePaginationConfig,
    filters: Record<string, FilterValue | null>,
    sorter: SorterResult<any> | SorterResult<any>[],
  ) => {
    const sort = (sorter as SorterResult<any>).order
      ? `&${Object.keys(sorter)
          .filter((f) => f !== 'column' && f !== 'columnKey')
          .map((key) => `${key}=${(sorter as any)[key]}`)
          .join('&')}`
      : ''
    const searchQuery = currentQueries.get('q') ? `&q=${search}` : ''

    router.get(
      route +
        `?page=${pagination.current}&limit=${pagination.pageSize}${Object.keys(
          filters,
        )
          .map((key) => `${key}=${filters[key]}`)
          .join('&')}${sort}${searchQuery}`,

      undefined,
      {
        preserveState: true,
        only: ['data'],
      },
    )
  }

  const handleDoSearch = (searchValue: string) => {
    if (!searchValue) {
      currentQueries.delete('q')
    } else {
      currentQueries.set('q', searchValue)
    }
    router.get(`${route}?${currentQueries.toString()}`, undefined, {
      preserveState: true,
      only: ['data'],
    })
  }

  const [isFirstRender, setIsFirstRender] = useState(true)

  useEffect(() => {
    if (!isFirstRender) {
      handleDoSearch(search)
    } else {
      setIsFirstRender(false)
    }
  }, [search])

  const handleSearch = (e: ChangeEvent<HTMLInputElement>) => {
    setSearch(e.target.value)
  }

  return (
    <Row gutter={[0, 16]}>
      <Col span={24} className={styles['table-control']}>
        <Row gutter={[16, 16]}>
          <Col
            xs={{ span: 24, order: 2 }}
            md={{ span: 8, order: 1 }}
            lg={{ span: 4, order: 1 }}
          >
            <Input
              placeholder="Search"
              suffix={<SearchOutlined />}
              onChange={debounce({ delay: 350 }, handleSearch)}
              // value={search}
              allowClear
            />
          </Col>
        </Row>
      </Col>
      <Col span={24}>
        <AntTable
          dataSource={data}
          columns={defaultColumns}
          pagination={{
            total: pagination.total,
            current: pagination.current_page,
            pageSize: pagination.per_page,
            showTotal: (_, range) =>
              `${range[0]}-${range[1]} of ${pagination.total} items`,
          }}
          rowKey={(record) => record.id}
          onChange={handleChangeTable}
          scroll={{
            x: data.length === 0 ? undefined : '768px',
          }}
          {...restTableProps}
        />
      </Col>
    </Row>
  )
}
