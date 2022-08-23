import api from "../../../services/axios"

const setURLColumn = () => {
	const headers = document.querySelector(".pdocrud-header-row") as HTMLTableRowElement
	const rows = (document.querySelector(".pdocrud-table > tbody") as HTMLTableSectionElement).children
	const colIdx = headers.children.length
	const totalRows = rows.length
	const newHeader = document.createElement('th')

	newHeader.innerText = "Comentários"
	headers.appendChild(newHeader)
	
	for(let i = 1; i < totalRows; i++) {
		const newCell = document.createElement('td')
		const rowId = (rows[i].children[0] as HTMLTableCellElement).outerText.trim()
		rows[i].appendChild(newCell)
		newCell.className = "pdocrud-row"
		api.get(`/api/supplier_url/read?id=${rowId}`)
			.then(response => response.data)
			.then(response => {
				const { url } = response
				newCell.innerText = url
			})
	}
}

export default setURLColumn
