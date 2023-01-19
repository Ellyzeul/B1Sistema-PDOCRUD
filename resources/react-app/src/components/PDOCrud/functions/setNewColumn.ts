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

export default setNewColumn
