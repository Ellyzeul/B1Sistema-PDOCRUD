import { MouseEventHandler, useEffect, useRef, useState } from "react"
import Excel from "exceljs"
import { fields, uploadTypes } from "./constants"
import "./style.css"
import { FieldsSelection } from "./FieldsSelection"

export const UploadFile = () => {
  const [selectOptions, setSelectOptions] = useState([] as JSX.Element[])
  const [updloadType, setUpdloadType] = useState("")
  const [selectedFields, setSelectedFields] = useState([] as {label: string, field_name: string, updatable: boolean, required: boolean}[])
  const [update, setUpdate] = useState([] as {[key: string]: string}[])
  const selectRef = useRef(null)
  const inputFileRef = useRef(null)
  const blockRef = useRef(null)

  const onChangeSelect: () => void = () => {
    if(!selectRef.current || !blockRef.current) return onChangeSelect()
    const select = selectRef.current as HTMLSelectElement
    const block = blockRef.current as HTMLDivElement
    const value = select.value

    block.className = value === "select" 
      ? "upload-box-unable" 
      : "upload-box-enable"
    
    setUpdloadType(value)
    setSelectedFields(fields[value] || [])
  }

  const onClick: MouseEventHandler = () => {
    if(!selectRef.current) return
    if((selectRef.current as HTMLSelectElement).value === "select") return
    if(!inputFileRef.current) return
    const input = inputFileRef.current as HTMLInputElement
    input.click()
  }

  const onChangeFileInput: () => void = () => {
    if(!inputFileRef.current) return onChangeFileInput()
    const input = inputFileRef.current as HTMLInputElement
    const wb = new Excel.Workbook()
    const reader = new FileReader()
    let validFields = {} as {[key: string]: string}
    let invalidFields = [] as string[]

    const readHeader = (header: Excel.Row) => {
      header.eachCell(cell => {
        const idx = selectedFields.findIndex(field => field.label === cell.value)
        if(idx !== -1) {
          validFields[cell.col] = cell.value as string
          return
        }
        invalidFields.push(cell.value as string)
      })
    }

    reader.onload = () => {
      const buffer = reader.result as Buffer
      wb.xlsx.load(buffer)
        .then(wb => {
          const sheet = wb.getWorksheet(1)
          const update = [] as {[key: string]: string}[]

          sheet.eachRow((row, index) => {
            const toPush = {} as {[key: string]: string}
            if(index === 1) {
              readHeader(row)
              return
            }

            row.eachCell(cell => 
              toPush[validFields[cell.col]] = cell.value as string
            )
            update.push(toPush)
          })
          setUpdate(update)
        })
    }
    reader.readAsArrayBuffer((input.files as FileList)[0] as File)
  }

  useEffect(() => {
    setSelectOptions(uploadTypes.map((type: {message: string, value: string}, idx) => 
      <option key={idx} value={type.value}>
        {type.message}
      </option>
    ))
  }, [])
  
  return (
    <div className="upload-container">
      <div>
        <span>Tipo de arquivo: 
          <select ref={selectRef} id="upload-type" onChange={onChangeSelect}>
            {selectOptions}
          </select>
        </span>
      </div>
      <div>
        <div id="upload-box" onClick={onClick}>
          <div><i className="fa-solid fa-file"></i></div>
          <div>Clique para escolher o arquivo</div>
          <div className="upload-box-unable" ref={blockRef}>
            Escolha o tipo de arquivo acima.
          </div>
        </div>
        <input 
          ref={inputFileRef} 
          id="upload-input" 
          type="file" 
          accept=".xlsx" 
          onChange={onChangeFileInput}
        />
      </div>
      {update.length === 0 
        ? null 
        : <FieldsSelection 
            upload_type={updloadType} 
            fields={selectedFields.filter(field => field.label in update[0])} 
            update={update} 
        />}
    </div>
  )
}