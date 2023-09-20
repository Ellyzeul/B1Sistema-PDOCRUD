import { TrackingTableProp } from "./types"
import { ToastContainer, toast } from "react-toastify"
import "./style.css"
import api from "../../services/axios"
import { FormEventHandler, KeyboardEventHandler, useEffect, useRef, useState } from "react"
import { Workbook } from "exceljs"

const fields = {
  tracking_code: {editable: false, label: "Rastreio"},
  delivery_method: {editable: false, label: "⛟"},
  online_order_number: {editable: false, label: "ORIGEM"},
  status: {editable: false, label: "Última movimentação"},
  last_update_date: {isDate: true, editable: false, label: "Data da movimentação"},
  details: {editable: false, label: "Detalhes"},
  expected_date: {isDate: true, editable: false, label: "Prazo para o cliente"},
  delivery_expected_date: {isDate: true, editable: false, label: "Prazo da transportadora"},
  client_deadline: {isDate: true, editable: false, label: "Prazo para retirada"},
  api_calling_date: {isDate: true, editable: false, label: "Última atualização"},
  observation: {editable: true, label: "Observação"},
} as {[key: string]: {isDate?: boolean, editable: boolean, label: string}}

const updateField = (trackingCode: string, input: HTMLInputElement, field: string) => {
  const value = input.value

  api.post('/api/tracking/update-field', {
    tracking_code: trackingCode,
    field: field,
    value: value,
    is_purchases: false
  })
    .then(_ => toast.success('Observação atualizada!'))
    .catch(_ => toast.error('Erro ao salvar a observação...'))
}

interface TextareaProp {
  defaultValue: string,
  tracking_code: string,
  field_name: string
}

const Textarea = (props: TextareaProp) => {
  const { defaultValue, tracking_code, field_name } = props

  return (
    <textarea 
      onKeyDown={(event) => {
        if(event.key !== "Enter") return
        const input = event.target as HTMLInputElement
        updateField(tracking_code, input, field_name)
        input.blur()
      }} 
      defaultValue={defaultValue}
    ></textarea>
  )
}

const updateRow = (trackingCode: string, deliveryMethod: string, row: any, fields: {[key: string]: {editable: boolean, label: string}}) => {
  api.post('/api/tracking/update', {
    tracking_code: trackingCode,
    delivery_method: deliveryMethod
  })
    .then((response) => {
      if(response.data.length === 0) toast.error("Ocorreu algum erro... Entrar em contato com o setor de TI");
      else toast.success("Rastreio atualizado!");
    }) 
    .catch(_ => toast.error("Ocorreu algum erro... Entrar em contato com o setor de TI"))
}

const updatePhase = (orderNumber: string, lastUpdateDate: string) => {
  if(!orderNumber || !lastUpdateDate) return toast.error("Dados incompletos. Atualize o pedido e tente novamente")
  api.post('/api/tracking/update-phase', {
    order_number: orderNumber,
    delivered_date: lastUpdateDate
  })
  .then((response) => {
    if(response.data[1] === 200) return toast.success(response.data[0])
    return toast.error(response.data[0]) 
  })
  .catch(_ => toast.error("Ocorreu algum erro... Entrar em contato com o setor de TI"))
}

const getRows = (data: {[key: string]: string}[], fieldsKeys: string[], actualPage: number) => {
  const rowsElements = [] as JSX.Element[]
  const offset = actualPage * ROWS_PER_PAGE

  data.slice(offset, offset + ROWS_PER_PAGE).forEach((row, idx) => {
    const btnCell = <td 
      className="tracking-update-button" 
      onClick={() => updateRow(row.tracking_code, row.delivery_method, rowElement.props, fields)}
    >Atualizar</td>
    const btnUpdate6dot1 = <td 
      className="tracking-update-button" 
      onClick={() => updatePhase(row.online_order_number, row.last_update_date)}
    >Sim</td>    
    const rowElement = <tr key={idx}>{[
      btnCell, 
      ...fieldsKeys.map((key, idx) => {
        if(key === "last_update_date") return [<td key={idx}></td>,btnUpdate6dot1]
        return <td key={idx}>{
        fields[key].editable
        ? <Textarea defaultValue={row[key]} tracking_code={row.tracking_code} field_name={key}/>
        : fields[key].isDate ? row[key] ? (new Date(`${row[key]} 00:00`)).toLocaleDateString("pt-BR") : "" : row[key]
        }</td>
    })]}</tr>
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

  return data.filter(row => String(row[key]).startsWith(searchTerm))
}

const ROWS_PER_PAGE = 20

export const TrackingTable = (props: TrackingTableProp) => {
  const { data } = props as {data: {[key: string]: string}[]}
  const fieldsKeys = Object.keys(fields)
  const [filteredData, setFilteredData] = useState(data)
  const [filterFields, setFilterFields] = useState([] as JSX.Element[])
  const [selectOptions, setSelectOptions] = useState([] as JSX.Element[])
  const [headers, setHeaders] = useState(null as JSX.Element | null)
  const [rows, setPartialRows] = useState([] as JSX.Element[])
  const setRows = (rows: JSX.Element[]) => {
    setPartialRows([])
    setTimeout(() => setPartialRows(rows), 1)
  }
  const [actualPage, setActualPage] = useState(0)
  const filterField = useRef(null)
  const filterInput = useRef(null)

  const headerSort = (field: string) => {
    const filtered = getFilteredData(data, filterInput, filterField)
      .sort((a, b) => a[field] < b[field] ? 1 : -1)

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

  const downloadExcel = () => {
    const idToast = toast.info('Processando...')
    const orderNumbers = Array.from(new Set(data.map(row => row.online_order_number)))
    api.post('/api/tracking/read-for-excel', {
      order_numbers: orderNumbers
    })
      .then(response => response.data)
      .then(response => {
        const { columns, data: xlsxRows } = response as {columns: {[key: string]: string}, data: {
          tracking_code: string,
          online_order_number: string,
          [key: string]: string
        }[]}
        const xlsxColumns = [] as {key: string, header: string}[]
        Object.keys(xlsxRows[0]).forEach((key) => {
          xlsxColumns.push({
            key: key,
            header: columns[key]
          })
        })
        const workbook = new Workbook()
        const worksheet = workbook.addWorksheet("Pedidos")
        toast.dismiss(idToast)

        worksheet.columns = [
          ...xlsxColumns,
          {key: "status", header: "Última movimentação"},
          {key: "last_update_date", header: "Data da movimentação"},
        ]
        const rows = xlsxRows.map(row => {
          const tracking = data.find(tracking => (tracking.tracking_code === row.tracking_code) && (tracking.online_order_number === row.online_order_number))

          return {
            ...row,
            status: tracking !== undefined ? tracking.status : null,
            last_update_date: tracking !== undefined ? tracking.last_update_date : null,
          }
        })
        worksheet.addRows(rows)

        workbook.xlsx.writeBuffer()
          .then(buffer => new Blob([buffer], {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'}))
          .then(blob => new File([blob], 'Rastreamento.xlsx'))
          .then(file => {
            const url = URL.createObjectURL(file)
            const anchor = document.createElement('a')
            anchor.href = url
            anchor.download = "Rastreamento.xlsx"
            anchor.click()

            URL.revokeObjectURL(url)
            toast.success('Excel gerado!')
          })
          .catch(_ => toast.error('Erro. Tente novamente ou contate o TI em caso de muitos erros...'))
      })
      .catch(_ => {
        toast.dismiss(idToast)
        toast.error('Algum erro interno ocorreu...')
      })
  }

  const updateAllTrackings = () => {
    toast.info("Processando. Por favor, aguarde um momento...")
    api.post('/api/tracking/update-all', {
      'is_purchases': '0'
    })
      .then((response) => response.data)
      .then(response => {
        if(response.error_code === 0) toast.success("Todos os rastreamentos foram atualizados com sucesso...")
        if(response.error_code === 1) toast.warning(`Erro ao atualizar ${response.total_errors} rastreamentos. Por favor, tente atualizar eles individualmente.`)
        if(response.error_code === 2) toast.error("Nenhum dos rastreamentos foi atualizado. Tente novamente ou contate o TI em caso de muitos erros...")
      })
      .catch(_ => toast.error("Ocorreu algum erro no sistema... Entrar em contato com o setor de TI"))
  }

  useEffect(() => {
    const optionsElements = [] as JSX.Element[]
    const selectElements = [] as JSX.Element[]
    const headerElements = [] as JSX.Element[]
    const rowsElements = getRows(filteredData, fieldsKeys, actualPage)
    const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE)

    fieldsKeys.forEach((key, idx) => {
      if(key === "last_update_date") {
        headerElements.push(<th key={idx} onClick={() => headerSort(key)}>{fields[key].label}</th>)
        headerElements.push(<th>Atualizar para 6.1</th>)
        return 
      }

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
        <button className="tracking-download-xlsx" onClick={downloadExcel}>
          <div className="tracking-download-xslx-img-container xlsx-color-light-green">
            <img src="/icons/download-white.png" alt="Excel" />
          </div>
          <div className="tracking-download-xslx-img-container xlsx-color-dark-green xlsx-download-hiddable">
            <img src="/icons/excel-white.png" alt="Excel" />
          </div>
        </button>
          <button onClick={updateAllTrackings} className="tracking-update-button tracking-updateall-button">Atualizar todos</button>
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

export default TrackingTable
