export const preparePermissions = (e: any) =>
  Object.keys(e)
    .filter((f) => f.includes('permissions'))
    .map((key) => ({
      key: key.replace(/\D/g, ''),
      value: e[key],
    }))
    .filter((f) => f.value)
    .map((f) => Number(f.key))

export const prepareInitialPermissions = (permissions: any) =>
  permissions
    .map((f: any) => `permissions[${f}]`)
    .reduce((acc: any, curr: any) => ({ ...acc, [curr]: true }), {})
