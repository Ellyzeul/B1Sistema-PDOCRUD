import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"

const configureInvoiceField = () => {
  const invoiceIdx = getColumnFieldIndex("NF")
  const sellercentralIdx = getColumnFieldIndex("ORIGEM")
  if(invoiceIdx === -1 || sellercentralIdx === -1) return
  const rows = document.querySelector(".pdocrud-table > tbody")?.children as HTMLCollectionOf<HTMLTableRowElement>
  const cells = {} as {[key: string]: HTMLTableCellElement[]}
  const numbersList: Array<string> = []

  for(let i = 1; i < rows.length; i++) {
    const orderNumber = (rows[i].children[sellercentralIdx] as HTMLTableCellElement).textContent?.trim() as string
    const cell = rows[i].children[invoiceIdx] as HTMLTableCellElement
    const input = cell.children[0] as HTMLInputElement

    input.oninput = (event) => {
      const input = event.target as HTMLInputElement
      const value = input.value
      if(value.length !== 44) return
    
      input.value = value.substring(29, 34)
    }

    numbersList.push(orderNumber)
    cells[orderNumber] = cells[orderNumber] ? cells[orderNumber] : []
    cells[orderNumber].push(cell)
  }

  api.get(`/api/photo/verify-list?numbers_list=${numbersList.join(',')}`)
    .then(response => response.data as {[key: string]: boolean})
    .then(response => Object.keys(response).forEach(number => {
      if(!response[number]) return

      cells[number].forEach(cell => {
        const div = document.createElement('div')
        const i = document.createElement('i')
        i.className = "fa-solid fa-image"
        div.appendChild(i)
        div.className = "photo-icon"
        div.onclick = () => {
          const anchor = document.createElement('a')
          anchor.href = `/expedicao/fotos/pesquisar?photo_number=${number}`
          anchor.target = "blank"
          anchor.click()
        }

        cell.appendChild(div)
        cell.className = cell.className + " photo-link"
      })
    }))
}

export default configureInvoiceField
