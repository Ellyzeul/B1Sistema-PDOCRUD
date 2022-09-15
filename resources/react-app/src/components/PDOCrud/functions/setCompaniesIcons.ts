import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableRows from "./getTableRows"

const setCompaniesIcons = () => {
  const colIdx = getColumnFieldIndex("Empresa")
  if(colIdx === -1) return
  const rows = getTableRows()
  const thumbsMap = {} as {[id: number]: string}
  
  if(Object.keys(thumbsMap).length !== 0) {
    setIcons(thumbsMap, rows, colIdx)
    return
  }

  api.get('/api/company/read-thumbnails')
    .then(response => response.data as {
      message: string,
      thumbs: Array<{id: number, thumbnail: string}>
    })
    .then(response => {
      const { message, thumbs } = response
      thumbs.forEach(thumb => thumbsMap[thumb.id] = thumb.thumbnail)

      setIcons(thumbsMap, rows, colIdx)
    })
  return
}

const setIcons = (thumbsMap: {[id: number]: string}, rows: HTMLTableRowElement[], colIdx: number) => {
  rows.forEach(row => {
    const cell = row.cells[colIdx]
    const id_company = Number(cell.innerText)
    const icon = document.createElement('img')
    icon.src = thumbsMap[id_company]
    icon.height = 35
    cell.innerText = ""
    cell.appendChild(icon)
  })
}

export default setCompaniesIcons
