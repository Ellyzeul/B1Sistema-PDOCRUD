import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableRows from "./getTableRows"

const setBlacklistIcon = () => {
    const isbnIdx = getColumnFieldIndex("ISBN")
    if(isbnIdx === -1) return
    const rows = getTableRows()

    let isbnList = ""
    rows.forEach(row => {
		const cell = row.cells[isbnIdx]
        const isbn = cell.textContent
        if(!isbn) return
        isbnList += isbnList === "" ? isbn : `,${isbn}`
	})

    api.post('/api/blacklist/verify-list', {
        isbns: isbnList        
      })
      .then(response => response.data as {[key: string]: boolean})
      .then(response => {        
        rows.forEach(row => {
            const cell = row.cells[isbnIdx]
            const isbn = cell.textContent?.toString().trim()

            if(!isbn) return
            if(!response[isbn]) return

            const div = document.createElement('div')
            const icon = document.createElement('i')
            icon.className = "fa-solid fa-ban"
            div.appendChild(icon)
            div.className = "blacklist-icon"
            cell.appendChild(div)
        })
      })
      .catch((error) => console.log(error));
}

export default setBlacklistIcon