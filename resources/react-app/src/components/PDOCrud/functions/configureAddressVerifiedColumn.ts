import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"

const configureAddressVerifiedColumn: {(): void, colIdx?: number} = () => {
  if(!configureAddressVerifiedColumn.colIdx) configureAddressVerifiedColumn.colIdx = getColumnFieldIndex("Endereço arrumado")
  if(configureAddressVerifiedColumn.colIdx === -1) return
  const table = document.querySelector(".pdocrud-table") as HTMLTableElement
  if(!table) return

  configureHeader(table, configureAddressVerifiedColumn.colIdx)
  configureRows(table, configureAddressVerifiedColumn.colIdx)
}

export default configureAddressVerifiedColumn

const configureHeader = (table: HTMLTableElement, colIdx: number) => {
  const thead = table.tHead as HTMLTableSectionElement
  const header = thead.rows[0].cells[colIdx] as HTMLTableCellElement
  const newIcon = document.createElement("i")
  newIcon.className = "fa-solid fa-truck"
  
  header.children[0].replaceWith(newIcon)
}

const configureRows = (table: HTMLTableElement, colIdx: number) => {
  const rows = Array.from(table.tBodies[0].rows)
  const saveBtn = document.querySelector(".pdocrud-button-save") as HTMLAnchorElement
  const checkboxes = [] as {id: string, checkbox: HTMLInputElement}[]
  const onKeyDown = (key: string) => {
    if(key !== "Enter") return
    saveBtn.click()
  }
  const onClick = () => {
    const request = [] as {
      id: number,
      address_verified: number
    }[]
    checkboxes.forEach(checkbox => request.push({
      id: Number(checkbox.id),
      address_verified: Number(checkbox.checkbox.value)
    }))
    api.post('/api/orders/address-verified/update', {
      verifieds: request
    })
  }

  rows.forEach(row => {
    const checkbox = document.createElement("input")
    const input = row.cells[colIdx].children[0] as HTMLInputElement
    const dataId = (input.attributes.getNamedItem("data-id") as Attr).value
    checkbox.value = input.value
    checkbox.onclick = () => checkbox.value = checkbox.value === "0" ? "1" : "0"
    checkbox.onkeydown = (event) => onKeyDown(event.key)
    checkbox.type = "checkbox"
    checkbox.checked = checkbox.value === "1"
    row.cells[colIdx].children[0].replaceWith(checkbox)
    checkboxes.push({
      id: dataId,
      checkbox: checkbox
    })
  })

  saveBtn.addEventListener('click', onClick)
}
