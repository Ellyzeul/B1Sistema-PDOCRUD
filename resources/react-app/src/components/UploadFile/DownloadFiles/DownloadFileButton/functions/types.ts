type XLSXFileData = {
  files_headers: {
    [key: string]: {
      header: string, 
      key: string, 
    }[]
  },
  data: {[key: string]: string}[]
}

export default XLSXFileData
