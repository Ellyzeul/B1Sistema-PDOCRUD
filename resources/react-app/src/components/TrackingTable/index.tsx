import { useEffect, useState, useRef } from "react"
import { TrackingTableProp } from "./types"
import "./style.css"
import api from "../../services/axios"
import { toast, ToastContainer } from "react-toastify"

const fields = {
  tracking_code: {editable: false, label: "Rastreio"},
  delivery_method: {editable: false, label: "⛟"},
  online_order_number: {editable: false, label: "ORIGEM"},
  status: {editable: false, label: "Última movimentação"},
  last_update_date: {editable: false, label: "Data da movimentação"},
  details: {editable: false, label: "Detalhes"},
  expected_date: {editable: false, label: "Prazo para o cliente"},
  delivery_expected_date: {editable: false, label: "Prazo da transportadora"},
  observation: {editable: true, label: "Observação"},
} as {[key: string]: {editable: boolean, label: string}}

const updateRow = (trackingCode: string, deliveryMethod: string, row: any, fields: {[key: string]: {editable: boolean, label: string}}) => {
  api.post('/api/tracking/update', {
    tracking_code: trackingCode,
    delivery_method: deliveryMethod
  })
    .then(response => response.data)
    .then(response => {
      toast.success("Rastreio atualizado!")
    })
    .catch(err => toast.error("Ocorreu algum erro... Entrar em contato com o setor de TI"))
  console.log(row.children[0].props)
}

export const TrackingTable = (props: TrackingTableProp) => {
  const { data } = props as {data: {[key: string]: string}[]}
  const fieldsKeys = Object.keys(fields)
  const [headers, setHeaders] = useState(null as JSX.Element | null)
  const [rows, setRows] = useState([] as JSX.Element[])

  useEffect(() => {
    const headerElements = [] as JSX.Element[]
    const rowElements = [] as JSX.Element[]

    fieldsKeys.forEach((key, idx) => headerElements.push(
      <th key={idx}>{fields[key].label}</th>
    ))

    setHeaders(<tr>{[<th></th>, ...headerElements]}</tr>)

    data.forEach((row, idx) => {
      const btnCell = <td 
        className="tracking-update-button" 
        onClick={event => updateRow(row.tracking_code, row.delivery_method, rowElement.props, fields)}
      >Atualizar</td>
      const rowElement = <tr key={idx}>{[
        btnCell, 
        ...fieldsKeys.map((key, idx) => <td key={idx}>{
          fields[key].editable
          ? <textarea defaultValue={row[key]}></textarea>
          : row[key]
          }</td>
      )]}</tr>
      rowElements.push(rowElement)
    })

    setRows(rowElements)
  }, [])

  return (
    <>
      <table className="tracking-table">
        <thead>
          {headers}
        </thead>
        <tbody>
          {rows}
        </tbody>
      </table>
      <ToastContainer/>
    </>
  )
}
