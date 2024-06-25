import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableRows from "./getTableRows"

export default function setSaveEvent() {
  const saveBtn = document.querySelector('.pdocrud-button-save') as HTMLAnchorElement
  const rows = getTableRows()
  const onlineOrderNumberIndex = getColumnFieldIndex('ORIGEM')

  saveBtn.addEventListener('click', async(event) => {
    event.preventDefault()
    const orderNumbers = rows
      .map(({children: row}) => row[onlineOrderNumberIndex] as HTMLTableCellElement)
      .map(cell => cell.innerText)
    
    api.post('/api/tracking/update-internal', {order_numbers: orderNumbers})
  })
}
