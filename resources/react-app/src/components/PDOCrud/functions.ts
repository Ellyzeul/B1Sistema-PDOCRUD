import api from "../../services/axios"
import { MutableRefObject } from "react";

export const setValuesOnSelects = () => {
	const selects = document.querySelectorAll('.pdocrud-row-cols > select') as NodeListOf<HTMLSelectElement>

	selects.forEach(select => {
		const val = select.dataset.originalVal
		const options = Array.from(select.options)

		options.forEach(option => option.value === val ? option.selected = true : null)
	})
}

export const setCurrencySymbols = () => {
	const rows = document.querySelectorAll('.pdocrud-data-row') as NodeListOf<HTMLTableCellElement>
	const regex = [
		{regex: /Brasil/, symbol: "R$"},
		{regex: /Canadá/, symbol: "CA$"},
		{regex: /Estados Unidos/, symbol: "US$"},
		{regex: /Reino Unido/, symbol: "£"},
	]
	const getCurrency = (country: string) => {
		return regex
			.filter(obj => obj.regex.test(country))
			.map(obj => obj.symbol)
	}

	rows.forEach(row => {
		const country = row.children[1].textContent as string
		const currency = row.children[9]

		currency.textContent = `${getCurrency(country)} ${currency.textContent}`
	})
}

export const setOpenModalEvent = (
	refModal: MutableRefObject<null>, 
	refModalId: MutableRefObject<null>, 
	refOnlineOrderNumber: MutableRefObject<null>, 
	refURLInput: MutableRefObject<null>
) => {
	if((!refModal.current) || (!refModalId.current) || (!refOnlineOrderNumber.current) || (!refURLInput.current)) return
	const modal = (refModal.current as HTMLDivElement)
	const modalId = (refModalId.current as HTMLDivElement)
	const onlineOrderNumber = (refOnlineOrderNumber.current as HTMLSpanElement)
	const urlInput = (refURLInput.current as HTMLInputElement)
	const rows = document.querySelectorAll('.pdocrud-data-row') as NodeListOf<HTMLTableRowElement>
	
	rows.forEach(row => {
		const rowData = Array.from(row.children) as HTMLTableCellElement[]
		const isbnCell = rowData[8]
		const rowId = ((rowData[0].textContent as string).match(/[0-9]{1,}/) as string[])[0]
		const rowOnlineOrderNumber = rowData[4].textContent as string

		isbnCell.style.cursor = 'pointer'
		isbnCell.onclick = () => openModal(
			modal, 
			modalId, 
			onlineOrderNumber, 
			urlInput, 
			rowId, 
			rowOnlineOrderNumber
		)
	})
}

const openModal = (
	modal: HTMLDivElement, 
	modalId: HTMLDivElement, 
	onlineOrderNumber: HTMLSpanElement, 
	urlInput: HTMLInputElement,
	rowId: string,
	rowOnlineOrderNumber: string
) => {
	modal.style.visibility = 'visible'
	document.body.style.overflowY = 'hidden'
	modalId.textContent = rowId
	onlineOrderNumber.textContent = rowOnlineOrderNumber
	const id = rowId
	api.get(`/api/supplier_url/read?id=${id}`)
		.then(response => response.data)
		.then(response => urlInput.value = response.url)
}
