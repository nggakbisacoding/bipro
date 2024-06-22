import { Link } from '@inertiajs/react'
import { ReactNode } from 'react'
import reactStringReplace from 'react-string-replace'

function removeQueryParameters(url: string) {
  const urlObj = new URL(url)
  urlObj.search = ''
  urlObj.hash = ''

  let newPathname = urlObj.pathname
  if (newPathname.endsWith('/amp')) {
    newPathname = newPathname.slice(0, -4)
  }

  urlObj.pathname = newPathname

  return urlObj.toString()
}

export const getMentionUrl = (
  source: string,
  message: string | ReactNode[],
  userType: string,
) => {
  let patternMention = /@([\w\_\.]+)/g
  let patternHashtag = /#(\w+)/g
  let patternUrl = /(https?:\/\/\S+)/g

  message = reactStringReplace(message, /(https?:\/\/\S+)/g, (match, i) => {
    return (
      <a key={match + i} href={match} target="_blank">
        {removeQueryParameters(match).slice(0, 50)}...
      </a>
    )
  })

  let routeUser = route('admin.post.show.user', '')
  let routeTag = route('admin.post.show.tag', '')

  if (userType === 'user') {
    routeUser = route('frontend.user.post.show.user', '')
    routeTag = route('frontend.user.post.show.tag', '')
  }

  message = reactStringReplace(message, patternMention, (mention, i) => (
    <Link key={mention + i} href={`${routeUser}/${mention}`}>
      @{mention}
    </Link>
  ))

  message = reactStringReplace(message, patternHashtag, (tag, i) => (
    <Link key={tag + i} href={`${routeTag}/${tag}`}>
      #{tag}
    </Link>
  ))

  return message
}
