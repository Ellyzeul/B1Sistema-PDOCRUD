import { createRoot } from "react-dom/client"
import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableRows from "./getTableRows"
import { AddBlacklistModal } from "../components/AddBlacklistModal"
import { DeleteBookModal } from "../components/DeleteBookModal"

const setBlacklistIcon = (phase: number, rawPhase: string) => {
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

  api.post('/api/blacklist/verify-list', {
      isbns: isbnList        
    })
    .then(response => response.data as {[key: string]: boolean})
    .then(response => {        
      rows.forEach(row => {
        const cell = row.cells[isbnIdx]
        const isbn = cell.textContent?.toString().trim()
        const originCell = row.cells[originIdx]
 
        if(!isbn) return
        if(phase === 1.1 || phase === 1.2 || phase === 1.3 || phase === 1.4 || phase === 1.5 || phase === 8.1 || rawPhase === '0.0') {
          if(!response[isbn]) {
            originCell.style.display = 'flex'
            originCell.appendChild(createModalButton(<AddBlacklistModal isbn={isbn}/>))
          }
          cell.appendChild(createModalButton(<DeleteBookModal isbn={isbn}/>))
        }
        
        if(!response[isbn]) return
        
        cell.appendChild(createBlacklistIcon())
        cell.style.display = 'flex'
      })
    })
    .catch((error) => console.log(error));
}

export default setBlacklistIcon

const createBlacklistIcon = () => {
  const div = document.createElement('div')
  const icon = document.createElement('i')

  icon.className = "fa-solid fa-ban"
  div.appendChild(icon)
  div.className = "blacklist-icon"
  div.style.padding = "0px 7px"
  
  return div
}

const createModalButton = (component: JSX.Element) => {
  const modalContainer = document.createElement('div')
  const modalRoot = createRoot(modalContainer)

  modalRoot.render(component)

  return modalContainer
}