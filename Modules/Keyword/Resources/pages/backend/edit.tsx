import { PageProps } from '@/types'
import { Head } from '@inertiajs/react'
import { KeywordEditPageProps } from '../types/keyword'
import { EditKeywordForm } from './components'

export default function EditKeyword({
  keyword,
}: PageProps<KeywordEditPageProps>) {
  return (
    <>
      <Head title="Edit Target Crawl" />
      <EditKeywordForm keyword={keyword} />
    </>
  )
}
