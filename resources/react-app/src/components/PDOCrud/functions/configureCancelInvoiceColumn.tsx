import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableHeaders from "./getTableHeaders"
import getTableRows from "./getTableRows"

const configureCancelInvoiceColumn: {(): void, colIdx?: number} = () => {
  if(!configureCancelInvoiceColumn.colIdx) configureCancelInvoiceColumn.colIdx = getColumnFieldIndex("NF cancelada")
  if(configureCancelInvoiceColumn.colIdx === -1) return
  const headers = getTableHeaders()
  const rows = getTableRows()
  if(!headers || !rows) return

  configureRows(rows, configureCancelInvoiceColumn.colIdx)
}

const configureRows = (rows: HTMLTableRowElement[], colIdx: number) => {
  const saveBtn = document.querySelector(".pdocrud-button-save") as HTMLAnchorElement
  const checkboxes = [] as {id: string, checkbox: HTMLInputElement}[]
  const onKeyDown = (key: string) => {
    if(key !== "Enter") return
    saveBtn.click()
  }
  const onClick = () => {
    const request = [] as {id: number, cancel_invoice: number}[]
    checkboxes.forEach(checkbox => request.push({
      id: Number(checkbox.id),
      cancel_invoice: Number(checkbox.checkbox.value)
    }))

    api.patch('/api/orders/cancel-invoice', {
      verifieds: request
    })
  }

  rows.forEach(row => {
    const cell = row.cells[colIdx] as HTMLTableCellElement
    const input = cell.children[0] as HTMLInputElement
    const style = cell.style

    style.display = 'grid'
    style.placeItems = 'center'

    addCheckbox(input, checkboxes, onKeyDown)
  })

  saveBtn.addEventListener('click', onClick)
}

const addCheckbox = (
  input: HTMLInputElement, 
  checkboxes: {id: string, checkbox: HTMLInputElement}[], 
  onKeyDown: (key: string) => void
) => {
  const checkbox = document.createElement("input")
  const dataIdAttr = input.attributes.getNamedItem("data-id") as Attr
  const dataId = dataIdAttr.value

  checkbox.value = input.value
  checkbox.onclick = () => checkbox.value = checkbox.value === "0" ? "1" : "0"
  checkbox.onkeydown = (event) => {
    event.preventDefault()
    onKeyDown(event.key)
  }
  checkbox.type = "checkbox"
  checkbox.checked = checkbox.value === "1"

  input.replaceWith(checkbox)
  checkboxes.push({
    id: dataId,
    checkbox: checkbox
  })
}

export default configureCancelInvoiceColumn
