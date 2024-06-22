import { PageProps } from '@/types'
import { Head } from '@inertiajs/react'
import { KeywordCreatePageProps } from '../types/keyword'
import { CreateKeywordForm } from './components'

export default function CreateKeyword({}: PageProps<KeywordCreatePageProps>) {
  return (
    <>
      <Head title="Create Target Crawl" />
      <CreateKeywordForm />
    </>
  )
}
