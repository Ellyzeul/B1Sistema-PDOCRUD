import { toast } from "react-toastify"
import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableRows from "./getTableRows"

const configureDeliveryMethodField = () => {
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
		container.style.gridTemplateColumns = "80% 10% 10%"

		cell.removeChild(select)
		container.appendChild(select)

		icon.className = "fa-solid fa-cloud-arrow-down icon-css"
		icon.style.position = "relative"
		icon.style.paddingTop = "10px"

		icon.addEventListener("click", () =>{
			api.patch('/api/orders/traking-service', {
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

	rows.forEach(async(row) => {
		const container = row.cells[deliveryMethodIdx].querySelector('div') as HTMLDivElement
		const blingNumberInput = row.cells[numberBlingIdx].querySelector('input') as HTMLInputElement
		const deliveryMethodSelect = container.querySelector('select') as HTMLSelectElement
		const deliveryMethod = deliveryMethodSelect.value
		const hasBlingNumber = !!blingNumberInput.value
		const hasDeliveryMethod = !!deliveryMethodSelect.value

		if(!hasDeliveryMethod || !hasBlingNumber) return
		
		const orderId = row.cells[idIdx].textContent?.trim() as string
		const link = await setLink(deliveryMethod, orderId)
		const i = document.createElement('i')

		i.onclick = () => window.open(link, "_blank", "noreferrer")
	
		i.className = 'fa-solid fa-tag icon-css'
		i.style.position = "relative"
		i.style.paddingTop = "10px"
		i.style.paddingLeft = "10px"
		container.appendChild(i)
	})

	document.styleSheets[1].addRule('.icon-css:hover', 'cursor: pointer;');
}

export default configureDeliveryMethodField

const setLink = async(deliveryMethod: string, orderId: string): Promise<string> => {
	if(deliveryMethod === '8') return await api.get(`/api/tracking/envia-dot-com-shipment-label?order_id=${orderId}`)
		.then(response => response.data)
		.then(({ link }) => link)

	return `/etiquetas/${orderId}`
}
