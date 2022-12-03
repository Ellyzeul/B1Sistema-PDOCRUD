import { useEffect, useState, useRef, MouseEventHandler, KeyboardEventHandler, FormEventHandler } from "react"
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

const getRows = (data: {[key: string]: string}[], fieldsKeys: string[], actualPage: number) => {
  const rowsElements = [] as JSX.Element[]
  const offset = actualPage * ROWS_PER_PAGE

  data.slice(offset, offset + ROWS_PER_PAGE).forEach((row, idx) => {
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

const ROWS_PER_PAGE = 20

export const TrackingTable = (props: TrackingTableProp) => {
  const { data } = props as {data: {[key: string]: string}[]}
  const fieldsKeys = Object.keys(fields)
  const [filteredData, setFilteredData] = useState(data)
  const [filterFields, setFilterFields] = useState([] as JSX.Element[])
  const [selectOptions, setSelectOptions] = useState([] as JSX.Element[])
  const [headers, setHeaders] = useState(null as JSX.Element | null)
  const [rows, setRows] = useState([] as JSX.Element[])
  const [actualPage, setActualPage] = useState(0)
  const filterField = useRef(null)
  const filterInput = useRef(null)

  const headerSort = (field: string) => {
    const filtered = getFilteredData(data, filterInput, filterField)
      .sort((a, b) => a[field] > b[field] ? 1 : -1)

    setRows(getRows(filtered, fieldsKeys, actualPage))
  }

  const filterData = () => setFilteredData(
    getFilteredData(data, filterInput, filterField)
  )

  const filterDataHotkey: KeyboardEventHandler = (event) => event.key === "Enter"
    ? filterData()
    : null

  const changePage: FormEventHandler = (event) => {
    const select = event.target as HTMLSelectElement
    const newPage = Number(select.value) - 1

    setActualPage(newPage)
  }

  useEffect(() => {
    const optionsElements = [] as JSX.Element[]
    const selectElements = [] as JSX.Element[]
    const headerElements = [] as JSX.Element[]
    const rowsElements = getRows(filteredData, fieldsKeys, actualPage)
    const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE)

    fieldsKeys.forEach((key, idx) => {
      headerElements.push(
        <th key={idx} onClick={() => headerSort(key)}>{fields[key].label}</th>
      )

      optionsElements.push(
        <option key={idx} value={key}>{fields[key].label}</option>
      )
    })

    for(let i = 0; i < totalPages; i++) {
      selectElements.push(
        <option key={i}>{i+1}</option>
      )
    }

    setFilterFields(optionsElements)
    setSelectOptions(selectElements)
    setHeaders(<tr>{[<th></th>, ...headerElements]}</tr>)
    setRows(rowsElements)
  }, [filteredData, actualPage])

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
        <div>
          <span>Página </span>
          <select onChange={changePage}>{selectOptions}</select>
        </div>
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
