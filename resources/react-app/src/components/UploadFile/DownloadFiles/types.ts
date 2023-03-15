type DownloadFilesProp = {
  upload_type: string,
  fields: {
    label: string, 
    field_name: string, 
    updatable: boolean,
    required: boolean
  }[],
  update: {
    [key: string]: string
  }[]
}

export default DownloadFilesProp
