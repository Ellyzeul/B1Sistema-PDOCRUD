import { differenceInCalendarDays } from "date-fns";
import getColumnFieldIndex from "./getColumnFieldIndex";
import setNewColumn from "./setNewColumn";

const setDeadlineColumn = (phase: number) => {
	if([4.11, 4.21].indexOf(phase) !== -1) return
	setNewColumn.columns = {}
	const generateData = (row: HTMLTableRowElement) => {
		const sellercentralIdx = getColumnFieldIndex("Canal de venda")
		const sellercentral = (row.children[sellercentralIdx] as HTMLTableCellElement || {}).innerText
		const dateIdx = getColumnFieldIndex(getDateColumnName(sellercentral))
		const div = document.createElement('div')

		if(dateIdx === -1) return div
		const date = (row.children[dateIdx] as HTMLTableCellElement)
			.outerText.split(' ')[0].split('/')
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

		return div

	}
	setNewColumn("Dias para entrega", generateData)
}

const getDateColumnName = (sellercentral: string) => {
	try {
		return sellercentralToDateColumn[sellercentral] || 'Data prevista'
	}
	catch(err) {
		return 'Data prevista'
	}
}

const sellercentralToDateColumn = {
	'MercadoLivre-BR': 'Data para envio', 
	'FNAC-PT': 'Data para envio', 
	'MagazineLuiza-BR': 'Data para envio', 
	'Seline-BR': 'Data para envio', 
} as {[key: string]: string}

export default setDeadlineColumn
