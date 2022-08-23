const getColumnFieldIndex: {(fieldName: string): number, headers?: HTMLTableCellElement[]} = (fieldName: string) => {
	if(!getColumnFieldIndex.headers) getColumnFieldIndex.headers = Array.from(
		document.querySelectorAll(".pdocrud-header-row > th") as NodeListOf<HTMLTableCellElement>
	)
	return getColumnFieldIndex.headers.findIndex(header => header.outerText === fieldName)
}

export default getColumnFieldIndex
