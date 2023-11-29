import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableRows from "./getTableRows"
import { InvoiceButtons } from "../components/InvoiceButtons"
import { createRoot } from "react-dom/client"

const setInvoiceNumberUpdateButton = () => {
	const numberBlingIdx = getColumnFieldIndex("Nº Bling")
	const orderIdx = getColumnFieldIndex("Nº")
	const companyIdx = getColumnFieldIndex("Empresa")
	const invoiceIdx = getColumnFieldIndex("NF")
	const sellercentralIdx = getColumnFieldIndex("Canal de venda")

	if(invoiceIdx === -1 || numberBlingIdx === -1 || orderIdx === -1 || companyIdx === -1 || sellercentralIdx === -1) return
	const rows = getTableRows()

	rows.forEach(row => {
		const cell = row.cells[invoiceIdx]
		const companyId = row.cells[companyIdx].textContent
		const sellercentralId = row.cells[sellercentralIdx].textContent
		const blingNumber = (row.cells[numberBlingIdx].children[0] as HTMLInputElement).value
		const orderId = row.cells[orderIdx].textContent?.trim()
		const invoiceNumberInput = (cell.children[0] as HTMLInputElement)

		const div = document.createElement('div')
		const btnRoot = createRoot(div)
		btnRoot.render(<InvoiceButtons 
			orderId={orderId} 
			companyId={companyId} 
			sellercentralId={sellercentralId} 
			blingNumber={blingNumber} 
			invoiceNumber={invoiceNumberInput.value} 
			invoiceInput={invoiceNumberInput}
		/>)
		cell.appendChild(div)
		
		cell.style.display = "grid"
		cell.style.gridTemplateColumns = "70% 30%"
	})
}

export default setInvoiceNumberUpdateButton