import getColumnFieldIndex from "./getColumnFieldIndex"

const setConditionalStyling = () => {
  const getParams = new URLSearchParams(window.location.search)
  const phase = Number(getParams.get("phase"))

  if(phase >= 2 && phase <= 2.3) fitContentSupplierNameColumn()
}

const fitContentSupplierNameColumn = () => {
  const supplierNameIdx = getColumnFieldIndex("Fornecedor")
  if(supplierNameIdx === -1) return
  const rows = Array.from(document.querySelectorAll(".pdocrud-table > tbody > tr") as NodeListOf<HTMLTableRowElement>)

  rows.forEach(row => {
    const input = row.cells[supplierNameIdx].children[0] as HTMLInputElement
    input.className += " supplier-name-input"
  })
}

export default setConditionalStyling
