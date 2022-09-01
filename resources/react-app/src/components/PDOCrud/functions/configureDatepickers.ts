import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableRows from "./getTableRows"

const COLUMNS_TO_CHECK = ["Data da compra", "Data de entrega"]
const DATE_CELL_ATTR = {month: "data-month", year: "data-year"}

const configureDatepickers = () => {
  const indexes = COLUMNS_TO_CHECK
    .filter(column => getColumnFieldIndex(column) !== -1)
    .map(column => getColumnFieldIndex(column))
  const rows = getTableRows()

  const datepickers = [] as HTMLInputElement[]
  rows.forEach(row => {
    datepickers.push(...indexes.map(index => row.cells[index].children[0] as HTMLInputElement))
  })

  datepickers.forEach(datepicker => {
    datepicker.onclick = () => {
      const rows = document.querySelectorAll(".ui-datepicker-calendar > tbody > tr") as NodeListOf<HTMLTableRowElement>
      rows.forEach(row => {
        const cells = row.cells
        for(let i = 0; i < cells.length; i++) {
          if(cells[i].children.length === 0) continue
          cells[i].replaceWith(cells[i].cloneNode(true) as HTMLTableCellElement)
          cells[i].onclick = () => {
            const attr = cells[i].attributes
            const day = Number((cells[i].children[0] as HTMLAnchorElement).innerText)
            const month = Number((attr.getNamedItem(DATE_CELL_ATTR.month) as Attr).value) + 1
            const year = (attr.getNamedItem(DATE_CELL_ATTR.year) as Attr).value
            const date = `${day < 10 ? `0${day}` : day}/${month < 10 ? `0${month}` : month}/${year}`

            datepicker.value = date
            datepicker.focus()
          }
        }
      })
    }
  })
}

export default configureDatepickers
