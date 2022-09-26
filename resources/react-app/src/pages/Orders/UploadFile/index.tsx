import { Navbar } from "../../../components/Navbar"
import { UploadFile } from "../../../components/UploadFile"
import "./style.css"

const UploadFilePage = () => {
  return (
    <div id="upload-file-page">
      <Navbar items={[]} />
      <div className="upload-file-container">
        <UploadFile />
      </div>
    </div>
  )
}

export default UploadFilePage
