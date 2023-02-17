import { toast } from "react-toastify"
import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableRows from "./getTableRows"

const setDeliveryMethodUpdateButton = () => {
	const deliveryMethodIdx = getColumnFieldIndex("Forma de envio")
	const numberBlingIdx = getColumnFieldIndex("Nº Bling")
	const idIdx = getColumnFieldIndex("Nº")
	const companyIdx = getColumnFieldIndex("Empresa")

	if(deliveryMethodIdx === -1 || numberBlingIdx === -1 || idIdx === -1 || companyIdx === -1) return

	const rows = getTableRows()

	rows.forEach(row => {
		const cell = row.cells[deliveryMethodIdx]
		const companyId = row.cells[companyIdx].textContent
		const blingNumber = (row.cells[numberBlingIdx].children[0] as HTMLInputElement).value
		const orderId = row.cells[idIdx].textContent?.trim()
		const deliveryMethodSelect = (cell.children[0] as HTMLSelectElement)
		const container = document.createElement('div')
		const select = cell.children[0]
		const icon = document.createElement("i")

		container.style.display = "grid"
		container.style.gridTemplateColumns = "90% 10%"

		cell.removeChild(select)
		container.appendChild(select)

		icon.className = "fa-solid fa-cloud-arrow-down icon-css"
		icon.style.position = "relative"
		icon.style.paddingTop = "10px"

		icon.addEventListener("click", () =>{
			api.patch('/api/orders/traking-service/update', {
				company_id: companyId,
				order_id: orderId,
				bling_number: blingNumber
			})
				.then(response => response.data[0])
				.then((data) => {
					const { delivery_method, message } = data

					deliveryMethodSelect.selectedIndex = delivery_method
					toast.success(message)
				})
				.catch(() => toast.error("Falha na requisição de dados do Bling..."))
		})

		container.appendChild(icon)
		cell.appendChild(container)
	})

	document.styleSheets[1].addRule('.icon-css:hover', 'cursor: pointer;');
}

export default setDeliveryMethodUpdateButton