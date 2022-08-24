import { intervalToDuration } from "date-fns";
import getColumnFieldIndex from "./getColumnFieldIndex";

type SetNewColumnPrototype = {
	(fieldname: string, generateData: (row: HTMLTableRowElement) => string): void,
	columns: {
		[key: string]: string[]
	}
}
const setNewColumn: SetNewColumnPrototype = (fieldname: string, generateData: (row: HTMLTableRowElement) => string) => {
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
		newCell.innerText = getValue(i)
	}
}
setNewColumn.columns = {}

const setDeadlineColumn = () => {
	setNewColumn.columns = {}
	const generateData = (row: HTMLTableRowElement) => {
		const expectedDateIdx = getColumnFieldIndex("Data prevista")
		if(expectedDateIdx === -1) return ""
		const date = (row.children[expectedDateIdx] as HTMLTableCellElement)
			.outerText.split('/')
			.map(part => Number(part))
		const start = new Date()
		const end = new Date(date[2], date[1]-1, date[0])

		if(end < start) return "Prazo vencido"
		if(end === start) return "O prazo é hoje"

		const interval = intervalToDuration({
			start: start,
			end: end
		})

		return `${(interval.days || 0) + 1} dias restantes`
	}
	setNewColumn("Dias para entrega", generateData)
}

export default setDeadlineColumn
