import { toast } from "react-toastify"
import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableRows from "./getTableRows"
import { MutableRefObject } from "react"

const setTrackingCodeUpdateButton = (refTrackingMethodModal: MutableRefObject<HTMLDivElement | null>, refOnlineOrderNumber: MutableRefObject<null>) => {
	const trackingCodeIdx = getColumnFieldIndex("Código de rastreio")
	const numberBlingIdx = getColumnFieldIndex("Nº Bling")
	const orderIdx = getColumnFieldIndex("Nº")
	const companyIdx = getColumnFieldIndex("Empresa")
	const orderNumberIdx = getColumnFieldIndex("ORIGEM")
	const shipDateIdx = getColumnFieldIndex("Data para envio")
	const sellercentralIdx = getColumnFieldIndex("Canal de venda")

	if(trackingCodeIdx === -1 || numberBlingIdx === -1 || orderIdx === -1 || companyIdx === -1 || orderNumberIdx === -1 || shipDateIdx === -1 || sellercentralIdx === -1) return

	const rows = getTableRows()

	rows.forEach(row => {
		const cell = row.cells[trackingCodeIdx]
		const orderNumber = row.cells[orderNumberIdx].textContent as string
		const sellercentral = row.cells[sellercentralIdx].textContent as string
		const companyId = row.cells[companyIdx].textContent?.trim()
		const blingNumber = (row.cells[numberBlingIdx].children[0] as HTMLInputElement).value
		const shipDate = row.cells[shipDateIdx].textContent as string
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
			api.patch('/api/orders/traking-id', {
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
		setChangeTrackingCodeButton(
			container, 
			orderNumber.trim(), 
			sellercentral.trim(), 
			companyId === '0' ? 'seline' : 'b1', 
			trackingCodeInput.value,
			shipDate.trim(),
			refTrackingMethodModal,
			refOnlineOrderNumber,
		)
		cell.appendChild(container)
	})

	document.styleSheets[1].addRule('.icon-css:hover', 'cursor: pointer;');
}

export default setTrackingCodeUpdateButton

const setChangeTrackingCodeButton = (container: HTMLDivElement, orderNumber: string, sellercentral: string, company: string, trackingNumber: string, shipDate: string, refTrackingMethodModal: MutableRefObject<HTMLDivElement | null>, refOnlineOrderNumber: MutableRefObject<null>) => {
	const button = document.createElement('i')
	const dateParts = shipDate.split(' ')[0].split('/')
	const treatedShipDate = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`

	button.addEventListener('click', async() => {
		if(sellercentralsToOpenModal.includes(sellercentral)) {
			if(!refTrackingMethodModal || !refOnlineOrderNumber.current) {
				toast.error('Tente novamente...')
				return
			}
			const trackingMethodModal = refTrackingMethodModal.current as HTMLDivElement

			(refOnlineOrderNumber.current as HTMLSpanElement).textContent = JSON.stringify({
				orderNumber,
				company,
				sellercentral,
				trackingNumber,
				shipDate: treatedShipDate,
			})
			trackingMethodModal.classList.remove('close')

			return
		}
		const { success, reason, errorPayload }: { success: boolean, reason?: string, errorPayload?: {} } = await api.post('/api/orders/tracking-code/on-sellercentral', {
			orderNumber,
			sellercentral,
			company,
			trackingNumber,
			shipDate: treatedShipDate,
		}).then(response => response.data)

		if(success) {
			toast.success('Rastreio atualizado!')
		}

		toast.error(`Erro ao atualizar o rastreio: ${reason}. ${!!errorPayload ? JSON.stringify(errorPayload) : ''}`)
	})
	button.className = 'fa-solid fa-upload'
	button.style.padding = '4px 6px'
	button.style.cursor = 'pointer'

	container.appendChild(button)
}

const sellercentralsToOpenModal = [
	'Amazon-US',
	'Amazon-ES',
	'Amazon-CA',
	'Amazon-UK',
]
