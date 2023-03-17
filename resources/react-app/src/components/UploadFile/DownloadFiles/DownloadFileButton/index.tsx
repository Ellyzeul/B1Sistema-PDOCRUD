import DownloadFileButtonProp from "./types"
import "./style.css"
import getXLSXFileFor from "./functions"
import { toast } from "react-toastify"

const DownloadFileButton = (props: DownloadFileButtonProp) => {
  const { file_for, ...xlsxData } = props

  const onClick = () => {
    getXLSXFileFor(file_for, xlsxData)
      .then(({ file, name }) => {
        const url = URL.createObjectURL(file)
        const anchor = document.createElement('a')
        anchor.href = url
        anchor.download = name
        anchor.click()

        URL.revokeObjectURL(url)
        toast.success(`Dados ${file_for} gerado!`)
      })
  }

  return (
    <div className="download-file-button" onClick={onClick}>
      {file_for}
    </div>
  )
}

export default DownloadFileButton
