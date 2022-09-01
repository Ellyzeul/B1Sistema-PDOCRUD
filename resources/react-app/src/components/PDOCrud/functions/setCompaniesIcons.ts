import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableRows from "./getTableRows"

const setCompaniesIcons = () => {
  const colIdx = getColumnFieldIndex("Empresa")
  if(colIdx === -1) return
  const rows = getTableRows()
  
  api.get('/api/company/read-thumbnails')
    .then(response => response.data as {
      message: string,
      thumbs: Array<{id: number, thumbnail: string}>
    })
    .then(response => {
      const { message, thumbs } = response
      const thumbsMap = {} as {[id: number]: string}
      thumbs.forEach(thumb => thumbsMap[thumb.id] = thumb.thumbnail)

      rows.forEach(row => {
        const cell = row.cells[colIdx]
        const id_company = Number(cell.innerText)
        const icon = document.createElement('img')
        icon.src = thumbsMap[id_company]
        icon.height = 35
        cell.innerText = ""
        cell.appendChild(icon)
      })
    })
  return
}

export default setCompaniesIcons
