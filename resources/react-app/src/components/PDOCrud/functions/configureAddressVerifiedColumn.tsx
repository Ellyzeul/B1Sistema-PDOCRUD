import { createRoot } from "react-dom/client"
import api from "../../../services/axios"
import AddressModal from "../components/AddressModal"
import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableHeaders from "./getTableHeaders"
import getTableRows from "./getTableRows"

const configureAddressVerifiedColumn: {(): void, colIdx?: number} = () => {
  if(!configureAddressVerifiedColumn.colIdx) configureAddressVerifiedColumn.colIdx = getColumnFieldIndex("Endereço arrumado")
  if(configureAddressVerifiedColumn.colIdx === -1) return
  const headers = getTableHeaders()
  const rows = getTableRows()
  if(!headers || !rows) return

  configureHeader(headers, configureAddressVerifiedColumn.colIdx)
  configureRows(rows, configureAddressVerifiedColumn.colIdx)
}

const configureHeader = (headers: HTMLTableRowElement, colIdx: number) => {
  const header = headers.cells[colIdx] as HTMLTableCellElement
  const newIcon = document.createElement("i")
  newIcon.className = "fa-solid fa-truck"
  
  header.children[0].replaceWith(newIcon)
}

const configureRows = async(rows: HTMLTableRowElement[], colIdx: number) => {
  const saveBtn = document.querySelector(".pdocrud-button-save") as HTMLAnchorElement
  const checkboxes = [] as {id: string, checkbox: HTMLInputElement}[]
  const orderNumberColIdx = getColumnFieldIndex('ORIGEM')
  const orderIdColIdx = getColumnFieldIndex('Nº')
  const salesChannelColIdx = getColumnFieldIndex('Canal de venda')
  const blacklist = await clientBlacklist(rows)
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
    api.patch('/api/orders/address-verified', {
      verifieds: request
    })
  }

  rows.forEach(row => {
    const cell = row.cells[colIdx] as HTMLTableCellElement
    const input = cell.children[0] as HTMLInputElement
    const orderNumber = row.cells[orderNumberColIdx].textContent?.trim() as string
    const orderId = row.cells[orderIdColIdx].textContent?.trim() as string
    const style = cell.style
    const salesChannel = row.cells[salesChannelColIdx].textContent?.trim() as string

    style.display = 'grid'
    style.placeItems = 'center'

    addCheckbox(input, checkboxes, onKeyDown)
    addModal(cell, orderNumber, orderId, salesChannel, blacklist)
  })

  saveBtn.addEventListener('click', onClick)
}

async function clientBlacklist(rows: Array<HTMLTableRowElement>): Promise<Record<string, boolean>> {
  const orderNumberIdx = getColumnFieldIndex('ORIGEM')
  if(orderNumberIdx === -1) return {}

  return api.get(`/api/client-blacklist/from-orders?${
    rows
      .map(row => (row.children[orderNumberIdx] as HTMLTableCellElement).innerText.trim())
      .map(orderNumber => `order_number[]=${orderNumber}`)
      .join('&')
  }`).then(response => response.data).then(({list}) => list)
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

const addModal = (cell: HTMLTableCellElement, orderNumber: string, orderId: string, salesChannel: string, blacklist: Record<string, boolean>) => {
  const modalContainer = document.createElement('div')
  const modalRoot = createRoot(modalContainer)

  modalRoot.render(<AddressModal
    orderNumber={orderNumber}
    orderId={orderId}
    salesChannel={salesChannel}
    blacklisted={blacklist[orderNumber] ?? false}
  />)

  cell.appendChild(modalContainer)
}

export default configureAddressVerifiedColumn
