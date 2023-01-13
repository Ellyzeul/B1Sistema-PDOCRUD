import { differenceInCalendarDays } from "date-fns";
import { toast } from "react-toastify";
import api from "../../../services/axios";
import getColumnFieldIndex from "./getColumnFieldIndex";

type SetNewColumnPrototype = {
	(fieldname: string, generateData: (row: HTMLTableRowElement) => HTMLElement): void,
	columns: {
		[key: string]: HTMLElement[]
	}
}
const setNewColumn: SetNewColumnPrototype = (fieldname: string, generateData: (row: HTMLTableRowElement) => HTMLElement) => {
	const headers = document.querySelector(".pdocrud-header-row") as HTMLTableRowElement
	const rows = (document.querySelector(".pdocrud-table > tbody") as HTMLTableSectionElement).children
	const newHeader = document.createElement('th')
	const getValue = fieldname in setNewColumn.columns 
		? (i: number) => setNewColumn.columns[fieldname][i]
		: (i: number) => {
			if(!setNewColumn.columns[fieldname]) setNewColumn.columns[fieldname] = []
			setNewColumn.columns[fieldname][i] = generateData(rows[i] as HTMLTableRowElement)
			return setNewColumn.columns[fieldname][i]
		}

	newHeader.innerText = fieldname
	headers.appendChild(newHeader)

	for(let i = 1; i < rows.length; i++) {
		const newCell = document.createElement('td')
		newCell.className = "pdocrud-row-cols"
		rows[i].appendChild(newCell)
		newCell.appendChild(getValue(i))
	}
}
setNewColumn.columns = {}

const setDeadlineColumn = () => {
	setNewColumn.columns = {}
	const generateData = (row: HTMLTableRowElement) => {
		const expectedDateIdx = getColumnFieldIndex("Data prevista")
		const askRatingIdx = getColumnFieldIndex("Pedir avaliação")
		const idIdx = getColumnFieldIndex("Nº")
		const isAskable = ((row.children[askRatingIdx] as HTMLTableCellElement).children[0] as HTMLSelectElement).selectedIndex === 1
		const div = document.createElement('div')
		if(expectedDateIdx === -1) return div
		const date = (row.children[expectedDateIdx] as HTMLTableCellElement)
			.outerText.split('/')
			.map(part => Number(part))
		const start = new Date()
		const end = new Date(date[2], date[1]-1, date[0])

		if(end < start) {
			div.innerText = "Prazo vencido"
			return div
		}
		if(end === start) {
			div.innerText = "O prazo é hoje"
			return div
		}

		const interval = differenceInCalendarDays(end, start)
		div.innerText = `${interval || 0} dias restantes`

		if(isAskable) {
			const button = document.createElement('button')
			const orderId = (row.children[idIdx] as HTMLTableCellElement).innerText.trim()
			button.innerText = 'Enviar mensagem'
			button.className = 'order-control-ask-rating-button'
			button.addEventListener('click', () => {
				api.post('/api/orders/ask-rating/send', {
					order_id: orderId
				})
					.then(response => response.data)
					.then(response => {
						toast.success(response.message)
						const select = (row.children[askRatingIdx] as HTMLTableCellElement).children[0] as HTMLSelectElement
						select.selectedIndex = 3
					})
					.catch(err => {
						toast.error(err.response.data.message)
						div.appendChild(button)
					})

					div.removeChild(button)
			})

			div.style.display = 'flex'
			div.style.flexDirection = 'column'
			div.appendChild(button)
		}

		return div
	}
	setNewColumn("Dias para entrega", generateData)
}

export default setDeadlineColumn
