export const getAttachmentUrl = ({
  source,
  username,
  filename,
}: {
  source: string
  username: string
  filename: string
}) => {
  if (!filename) return `https://picsum.photos/seed/${source}/200/300`
  return `/storage/${source.toLowerCase()}/${username}/${filename}`
}
