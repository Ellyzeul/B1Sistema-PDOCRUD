import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableRows from "./getTableRows"

const configureReadyForShipColumn: {(): void, colIdx?: number} = () => {
  if(!configureReadyForShipColumn.colIdx) configureReadyForShipColumn.colIdx = getColumnFieldIndex("Pronto p/ envio")
  if(configureReadyForShipColumn.colIdx === -1) return
  const rows = getTableRows()
  if(!rows) return

  configureRows(rows, configureReadyForShipColumn.colIdx)
}

const configureRows = (rows: HTMLTableRowElement[], colIdx: number) => {
  const saveBtn = document.querySelector(".pdocrud-button-save") as HTMLAnchorElement
  const checkboxes = [] as {id: string, checkbox: HTMLInputElement}[]
  const onKeyDown = (key: string) => {
    if(key !== "Enter") return
    saveBtn.click()
  }
  const onClick = () => {
    const request = [] as {
      id: number,
      ready_for_ship: number
    }[]
    checkboxes.forEach(checkbox => request.push({
      id: Number(checkbox.id),
      ready_for_ship: Number(checkbox.checkbox.value)
    }))
    api.patch('/api/orders/read-for-ship', {
      verifieds: request
    })
    .then(response => response.data)
    .then(console.log)
  }

  rows.forEach(row => {
    const cell = row.cells[colIdx] as HTMLTableCellElement
    const input = cell.children[0] as HTMLInputElement

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
  checkbox.onkeydown = (event) => onKeyDown(event.key)
  checkbox.type = "checkbox"
  checkbox.checked = checkbox.value === "1"

  input.replaceWith(checkbox)
  checkboxes.push({
    id: dataId,
    checkbox: checkbox
  })
}

export default configureReadyForShipColumn
