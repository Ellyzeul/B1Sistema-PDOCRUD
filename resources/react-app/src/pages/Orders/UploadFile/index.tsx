import { useEffect, useState } from "react"
import "./style.css"

const UploadFilePage = () => {
  const uploadTypes = [
    "Atualização de pedidos"
  ]
  const [selectOptions, setSelectOptions] = useState([] as JSX.Element[])

  useEffect(() => {
    setSelectOptions(uploadTypes.map((type, idx) => <option key={idx}>
      {type}
    </option>))
  }, [uploadTypes])

  return (
    <div className="upload-file-page">
      <div className="upload-container">
        <select name="" id="">{selectOptions}</select>
        <input type="file" name="" id="" />
      </div>
    </div>
  )
}

export default UploadFilePage
