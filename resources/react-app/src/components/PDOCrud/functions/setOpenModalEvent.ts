import { MutableRefObject } from "react"
import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"

const setOpenModalEvent = (refModal: MutableRefObject<null>, refModalId: MutableRefObject<null>, refOnlineOrderNumber: MutableRefObject<null>, refURLInput: MutableRefObject<null>) => {
	const isbnIdx = getColumnFieldIndex("ISBN")
	const onlineNumberIdx = getColumnFieldIndex("ORIGEM")
	if(isbnIdx === -1 || onlineNumberIdx === -1) return
	if((!refModal.current) || (!refModalId.current) || (!refOnlineOrderNumber.current) || (!refURLInput.current)) return
	const modal = (refModal.current as HTMLDivElement)
	const modalId = (refModalId.current as HTMLDivElement)
	const onlineOrderNumber = (refOnlineOrderNumber.current as HTMLSpanElement)
	const urlInput = (refURLInput.current as HTMLTextAreaElement)
	const rows = document.querySelectorAll('.pdocrud-data-row') as NodeListOf<HTMLTableRowElement>

	rows.forEach(row => {
		const rowData = Array.from(row.children) as HTMLTableCellElement[]
		const isbnCell = rowData[isbnIdx]
		const rowId = ((rowData[0].textContent as string).match(/[0-9]{1,}/) as string[])[0]
		const rowOnlineOrderNumber = rowData[onlineNumberIdx].textContent as string

		isbnCell.style.cursor = 'pointer'
		isbnCell.onclick = () => openModal(modal, modalId, onlineOrderNumber, urlInput, rowId, rowOnlineOrderNumber)
	})
}

const openModal = (
	modal: HTMLDivElement, 
	modalId: HTMLDivElement, 
	onlineOrderNumber: HTMLSpanElement, 
	urlInput: HTMLTextAreaElement,
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

export default setOpenModalEvent
