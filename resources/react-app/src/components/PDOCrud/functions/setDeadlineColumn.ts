import { differenceInCalendarDays } from "date-fns";
import getColumnFieldIndex from "./getColumnFieldIndex";
import setNewColumn from "./setNewColumn";

const setDeadlineColumn = (phase: number) => {
	setNewColumn.columns = {}
	const generateData = (row: HTMLTableRowElement) => {
		const expectedDateIdx = getColumnFieldIndex("Data prevista")
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

		return div

	}
	setNewColumn("Dias para entrega", generateData)
}

export default setDeadlineColumn
