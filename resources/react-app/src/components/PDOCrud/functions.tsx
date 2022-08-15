import api from "../../services/axios"
import { MutableRefObject } from "react";
import { TopScrollBar } from "../TopScrollBar";
import { createRoot } from "react-dom/client";

export const configurePage = (
	elemRef: React.MutableRefObject<null>, 
	refModal: React.MutableRefObject<null>, 
	refModalId: React.MutableRefObject<null>, 
	refOnlineOrderNumber: React.MutableRefObject<null>, 
	refURLInput: React.MutableRefObject<null>
) => {
	if(!elemRef.current) return
	const elem = elemRef.current as HTMLDivElement

	if(elem.children.length === 0) return
	if(!document.querySelectorAll('.pdocrud-data-row')[0].children[1]) return

	(document.querySelector(".panel-title") as HTMLHeadingElement).textContent = "Controle de fases"
	setValuesOnSelects()
	setCurrencySymbols()
	setOpenModalEvent(refModal, refModalId, refOnlineOrderNumber, refURLInput)
	setTopScrollBar(document.querySelector(".panel-body") as HTMLDivElement)
	setSearchWorkaround(
		elemRef,
		refModal,
		refModalId,
		refOnlineOrderNumber,
		refURLInput
	)
}

const setSearchWorkaround = (
	elemRef: React.MutableRefObject<null>, 
	refModal: React.MutableRefObject<null>, 
	refModalId: React.MutableRefObject<null>, 
	refOnlineOrderNumber: React.MutableRefObject<null>, 
	refURLInput: React.MutableRefObject<null>
) => {
	const searchBtn = document.querySelector("#pdocrud_search_btn") as HTMLAnchorElement
	const loadGif = document.querySelector("#pdocrud-ajax-loader") as HTMLDivElement
	const pagesItems = document.querySelectorAll(".page-item > a") as NodeListOf<HTMLAnchorElement>
	const totalPerPage = document.querySelectorAll('#pdocrud_records_per_page > option') as NodeListOf<HTMLOptionElement>
	const applyConfigsAfterTimeout = () => setTimeout(() => {
		if(loadGif.style.display !== "none") {
			applyConfigsAfterTimeout()
			return
		}
		configurePage(
			elemRef,
			refModal,
			refModalId,
			refOnlineOrderNumber,
			refURLInput
		)
	}, 1000)

	searchBtn.onclick = () => applyConfigsAfterTimeout()
	pagesItems.forEach(anchor => anchor.onclick = () => applyConfigsAfterTimeout())
	totalPerPage.forEach(option => option.onclick = () => applyConfigsAfterTimeout())
}

const setValuesOnSelects = () => {
	const selects = document.querySelectorAll('.pdocrud-row-cols > select') as NodeListOf<HTMLSelectElement>

	selects.forEach(select => {
		const val = select.dataset.originalVal
		const options = Array.from(select.options)

		options.forEach(option => option.value === val ? option.selected = true : null)
	})
}

const setCurrencySymbols = () => {
	const rows = document.querySelectorAll('.pdocrud-data-row') as NodeListOf<HTMLTableCellElement>
	const headers = Array.from(
		document.querySelectorAll(".pdocrud-header-row > th") as NodeListOf<HTMLTableCellElement>
	)
	const valueIdx = headers.findIndex(header => header.outerText === "Valor")
	const sellercentralIdx = headers.findIndex(header => header.outerText === "Exportação")

	if(valueIdx === -1 || sellercentralIdx === -1) return
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
		const country = row.children[sellercentralIdx].textContent as string
		const currency = row.children[valueIdx]

		currency.textContent = `${getCurrency(country)} ${currency.textContent}`
	})
}

const setOpenModalEvent = (
	refModal: MutableRefObject<null>, 
	refModalId: MutableRefObject<null>, 
	refOnlineOrderNumber: MutableRefObject<null>, 
	refURLInput: MutableRefObject<null>
) => {
	const headers = Array.from(
		document.querySelectorAll(".pdocrud-header-row > th") as NodeListOf<HTMLTableCellElement>
	)
	const isbnIdx = headers.findIndex(header => header.outerText === "ISBN")
	const onlineNumberIdx = headers.findIndex(header => header.outerText === "ORIGEM")
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

const setTopScrollBar = (panelBody: HTMLDivElement) => {
	const toScroll = panelBody.children[2] as HTMLDivElement
	const scrollbarContainer = document.createElement("div")

	scrollbarContainer.style.height= "20px"
	panelBody.insertBefore(scrollbarContainer, toScroll)
	const scrollbarRoot = createRoot(scrollbarContainer)
	scrollbarRoot.render(<TopScrollBar/>)
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
