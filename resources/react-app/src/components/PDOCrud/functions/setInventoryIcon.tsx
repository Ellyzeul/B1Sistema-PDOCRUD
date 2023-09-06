import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableRows from "./getTableRows"
import { createRoot } from "react-dom/client"
import { InventoryModal } from "../components/InventoryModal"

const setInventoryIcon = () => {
    const isbnIdx = getColumnFieldIndex("ISBN")
    const originIdx = getColumnFieldIndex("ORIGEM")
    if(isbnIdx === -1) return
    const rows = getTableRows()

    let isbnList = ""
    rows.forEach(row => {
		const cell = row.cells[isbnIdx]
        const isbn = cell.textContent
        if(!isbn) return
        isbnList += isbnList === "" ? isbn : `,${isbn}`
	})

    api.post('/api/inventory/verify-list', {
        isbns: isbnList        
      })
      .then(response => response.data as {[key: string]: boolean})
      .then(response => {
        console.log(response)
        rows.forEach(row => {
          const originCell = row.cells[originIdx]
          const cell = row.cells[isbnIdx]
          const isbn = cell.textContent?.toString().trim()

          if(!isbn) return
          if(!response[isbn]) return

          const div = document.createElement('div')
          const modalRoot = createRoot(div)
          modalRoot.render(<InventoryModal isbn={isbn}/>)

          originCell.appendChild(div)
        })
      })
      .catch((error) => console.log(error));
}

export default setInventoryIcon