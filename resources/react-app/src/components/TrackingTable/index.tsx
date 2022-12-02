import { useEffect, useState, useRef, MouseEventHandler, KeyboardEventHandler } from "react"
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

const getRows = (data: {[key: string]: string}[], fieldsKeys: string[]) => {
  const rowsElements = [] as JSX.Element[]

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
    rowsElements.push(rowElement)
  })

  return rowsElements
}

const getFilteredData = (
  data: {[key: string]: string}[],
  filterInput: React.MutableRefObject<null>, 
  filterField: React.MutableRefObject<null>
) => {
  if(!filterInput.current) return data
  if(!filterField.current) return data
  const input = filterInput.current as HTMLInputElement
  const searchTerm = input.value
  if(searchTerm === "") return data
  const select = filterField.current as HTMLSelectElement
  const key = select.value

  return data.filter(row => row[key].startsWith(searchTerm))
}

export const TrackingTable = (props: TrackingTableProp) => {
  const { data } = props as {data: {[key: string]: string}[]}
  const fieldsKeys = Object.keys(fields)
  const [filterFields, setFilterFields] = useState([] as JSX.Element[])
  const [headers, setHeaders] = useState(null as JSX.Element | null)
  const [rows, setRows] = useState([] as JSX.Element[])
  const filterField = useRef(null)
  const filterInput = useRef(null)

  const headerSort = (field: string) => {
    const filtered = getFilteredData(data, filterInput, filterField)
      .sort((a, b) => a[field] > b[field] ? 1 : -1)

    setRows(getRows(filtered, fieldsKeys))
  }

  const filterData = () => setRows(
    getRows(getFilteredData(data, filterInput, filterField), fieldsKeys)
  )

  const filterDataHotkey: KeyboardEventHandler = (event) => event.key === "Enter"
    ? filterData()
    : null

  useEffect(() => {
    const headerElements = [] as JSX.Element[]
    const optionsElements = [] as JSX.Element[]
    const rowsElements = getRows(data, fieldsKeys)

    fieldsKeys.forEach((key, idx) => {
      headerElements.push(
        <th key={idx} onClick={() => headerSort(key)}>{fields[key].label}</th>
      )

      optionsElements.push(
        <option key={idx} value={key}>{fields[key].label}</option>
      )
    })

    setHeaders(<tr>{[<th></th>, ...headerElements]}</tr>)
    setFilterFields(optionsElements)
    setRows(rowsElements)
  }, [])

  return (
    <div className="tracking-table-container">
      <div className="tracking-nav-buttons">
        <div className="tracking-filter">
          <input 
            ref={filterInput} 
            type="text" 
            placeholder="Pesquisa" 
            onKeyDown={filterDataHotkey}
          />
          <select ref={filterField}>{filterFields}</select>
          <button onClick={filterData}>Filtrar</button>
        </div>
        <select name="" id=""></select>
      </div>
      <table className="tracking-table">
        <thead>
          {headers}
        </thead>
        <tbody>
          {rows}
        </tbody>
      </table>
      <ToastContainer/>
    </div>
  )
}
