type DownloadFileButtonProp = {
  file_for: string,
  files_headers: {
    [key: string]: {
      header: string, 
      key: string, 
    }[]
  },
  data: {[key: string]: string}[]
}

export default DownloadFileButtonProp
