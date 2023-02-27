import { FieldSelectionProp } from "./types"
import "./style.css"
import { MouseEventHandler, useEffect, useState } from "react"
import api from "../../../services/axios"
import { toast, ToastContainer } from "react-toastify"

export const FieldsSelection = (props: FieldSelectionProp) => {
  const { upload_type, fields, update } = props
  const [selections, setSelections] = useState([] as JSX.Element[])
  const [updateFields, setUpdateFields] = useState(new Set(
    fields
      .filter(field => field.required)
      .map(field => field.field_name)
  ))

  const toogleSelection: MouseEventHandler = (event) => {
    const div = event.target as HTMLDivElement
    const fieldName = (div.attributes.getNamedItem("itemID") as Attr).value

    if(div.className === "field-toogle-disabled") {
      div.className = "field-toogle-enabled"
      updateFields.add(fieldName)
      setUpdateFields(updateFields)
      return
    }
    div.className = "field-toogle-disabled"
    updateFields.delete(fieldName)
    setUpdateFields(updateFields)
  }

  const sendUpdate: MouseEventHandler = () => {
    const loadingToastId = toast.loading("Processando...")
    const filteredUpdate = update
      .map(row => {
        const filteredRow = {} as {[key: string]: string}
        fields.forEach(field => {
          if(updateFields.has(field.field_name)) filteredRow[field.field_name] = row[field.label]
        })

        return filteredRow
      })
    console.log(JSON.stringify(filteredUpdate))
    api.post(`/api/file-upload/${upload_type}`, {
      upload_data: filteredUpdate
    })
      .then(response => response.data)
      .then(response => {
        toast.dismiss(loadingToastId)
        toast.success("Envio concluído com êxito!")
      })
      .catch(() => {
        toast.dismiss(loadingToastId)
        toast.error('Algum erro interno ocorreu...')
      })
  }

  useEffect(() => {
    setSelections(fields.map((field, idx) => 
      <div 
        key={idx} 
        itemID={field.field_name}
        className={field.updatable ? "field-toogle-disabled" : "field-untoogle"}
        onClick={field.updatable ? toogleSelection : () => {}}
      >
        {field.label}
      </div>
    ))
  }, [])

  return (
    <section className="fields-selection">
      <div>
        <p>Escolha os campos para enviar</p>
        <button className="fields-selection-send-button" onClick={sendUpdate}>Enviar</button>
      </div>
      <div className="fields-selection-list">{selections}</div>
      <ToastContainer/>
    </section>
  )
}
