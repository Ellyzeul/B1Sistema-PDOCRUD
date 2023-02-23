import { toast } from "react-toastify"
import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableRows from "./getTableRows"

const setTrackingCodeUpdateButton = () => {
	const trackingCodeIdx = getColumnFieldIndex("Código de rastreio")
	const numberBlingIdx = getColumnFieldIndex("Nº Bling")
	const orderIdx = getColumnFieldIndex("Nº")
	const companyIdx = getColumnFieldIndex("Empresa")

	if(trackingCodeIdx === -1 || numberBlingIdx === -1 || orderIdx === -1 || companyIdx === -1) return

	const rows = getTableRows()

	rows.forEach(row => {
		const cell = row.cells[trackingCodeIdx]
		const companyId = row.cells[companyIdx].textContent
		const blingNumber = (row.cells[numberBlingIdx].children[0] as HTMLInputElement).value
		const orderId = row.cells[orderIdx].textContent?.trim()
		const trackingCodeInput = (cell.children[0] as HTMLInputElement)
		const container = document.createElement("div")
		const input = cell.children[0]
		const icon = document.createElement("i")

		cell.removeChild(input)
		container.appendChild(input)
		container.style.display = "grid"
		container.style.gridTemplateColumns = "90% 10%"

		icon.classList.add("fa-solid")
		icon.classList.add("fa-cloud-arrow-down")
		icon.classList.add("icon-css")
		icon.style.paddingTop = "10px"
		
		icon.addEventListener("click", () =>{
			api.patch('/api/orders/traking-id/update', {
				company_id: companyId,
				order_id: orderId,
				bling_number: blingNumber
			})
				.then(response => response.data[0])
				.then((data) => {
					const { tracking_code, message } = data

					trackingCodeInput.value = tracking_code
					toast.success(message)
				})
				.catch(() => toast.error("Falha na requisição de dados do Bling..."))
		})

		container.appendChild(icon)
		cell.appendChild(container)
	})

	document.styleSheets[1].addRule('.icon-css:hover', 'cursor: pointer;');
}

export default setTrackingCodeUpdateButton