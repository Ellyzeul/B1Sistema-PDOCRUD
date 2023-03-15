import { useEffect, useState } from "react"
import { DOWNLOAD_FILES } from "./constants"
import DownloadFileButton from "./DownloadFileButton"
import DownloadFilesProp from "./types"
import "./style.css"

const DownloadFiles = (props: DownloadFilesProp) => {
  const { upload_type, fields, update } = props
  const filesHeaders = DOWNLOAD_FILES[upload_type]
  const filesTypes = Object.keys(filesHeaders)
  const [files, setFiles] = useState([] as JSX.Element[])

  useEffect(() => {
    if(!filesHeaders) return
    const toUpdate = [] as JSX.Element[]

    filesTypes.forEach((key, idx) => toUpdate.push(<DownloadFileButton 
      file_for={key} 
      files_headers={filesHeaders} 
      data={update} 
      key={idx} 
    />))
    
    setFiles(toUpdate)
  }, [])

  return (
    <div className="download-files">
      {filesTypes.length === 1 ? "Arquivo" : "Arquivos"} para baixar: 
      {files}
    </div>
  )
}

export default DownloadFiles
