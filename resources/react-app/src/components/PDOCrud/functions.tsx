import api from "../../services/axios"
import { MutableRefObject } from "react";
import { TopScrollBar } from "../TopScrollBar";
import { createRoot } from "react-dom/client";
import { intervalToDuration } from "date-fns";

export const configurePage = (elemRef: MutableRefObject<null>, refModal: MutableRefObject<null>, refModalId: MutableRefObject<null>, refOnlineOrderNumber: MutableRefObject<null>, refURLInput: MutableRefObject<null>) => {
	if(!elemRef.current) return
	const elem = elemRef.current as HTMLDivElement

	if(elem.children.length === 0) return
	if(!document.querySelectorAll('.pdocrud-data-row')[0].children[1]) return

	const phase = Number(window.location.search.split(/=/)[1]) || 0
	console.log(phase)

	const h1 = document.querySelector(".panel-title") as HTMLHeadingElement
	h1.textContent = "Controle de fases"
	setValuesOnSelects()
	setCurrencySymbols()
	setOpenModalEvent(refModal, refModalId, refOnlineOrderNumber, refURLInput)
	setTopScrollBar(document.querySelector(".panel-body") as HTMLDivElement)
	setSearchWorkaround(elemRef, refModal, refModalId, refOnlineOrderNumber, refURLInput)
	if(phase < 7) setDeadlineColumn()
	if(phase === 2.1) setURLColumn()
}

const getColumnFieldIndex: {(fieldName: string): number, headers?: HTMLTableCellElement[]} = (fieldName: string) => {
	if(!getColumnFieldIndex.headers) getColumnFieldIndex.headers = Array.from(
		document.querySelectorAll(".pdocrud-header-row > th") as NodeListOf<HTMLTableCellElement>
	)
	return getColumnFieldIndex.headers.findIndex(header => header.outerText === fieldName)
}

const setSearchWorkaround = (elemRef: MutableRefObject<null>, refModal: MutableRefObject<null>, refModalId: MutableRefObject<null>, refOnlineOrderNumber: MutableRefObject<null>, refURLInput: MutableRefObject<null>) => {
	const searchBtn = document.querySelector("#pdocrud_search_btn") as HTMLAnchorElement
	const loadGif = document.querySelector("#pdocrud-ajax-loader") as HTMLDivElement
	const pagesItems = document.querySelectorAll(".page-item > a") as NodeListOf<HTMLAnchorElement>
	const totalPerPage = document.querySelectorAll('#pdocrud_records_per_page > option') as NodeListOf<HTMLOptionElement>
	const applyConfigsAfterTimeout = () => setTimeout(() => {
		if(loadGif.style.display !== "none") {
			applyConfigsAfterTimeout()
			return
		}
		configurePage(elemRef, refModal, refModalId, refOnlineOrderNumber, refURLInput)
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
	const valueIdx = getColumnFieldIndex("Valor")
	const sellercentralIdx = getColumnFieldIndex("Exportação")

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
		return `${interval.days} dias restantes`
	}
	setNewColumn("Dias para entrega", generateData)
}

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
