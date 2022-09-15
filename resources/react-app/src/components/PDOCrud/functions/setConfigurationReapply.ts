import { MutableRefObject } from "react"
import configurePage from "./configurePage"
import getTableHeaders from "./getTableHeaders"

const setConfigurationReapply = (elemRef: MutableRefObject<null>, refModal: MutableRefObject<null>, refModalId: MutableRefObject<null>, refOnlineOrderNumber: MutableRefObject<null>, refURLInput: MutableRefObject<null>) => {
	const saveBtn = document.querySelector(".pdocrud-button-save") as HTMLAnchorElement
	const searchBtn = document.querySelector("#pdocrud_search_btn") as HTMLAnchorElement
	const loadGif = document.querySelector("#pdocrud-ajax-loader") as HTMLDivElement
	const pagesItems = document.querySelectorAll(".page-item > a") as NodeListOf<HTMLAnchorElement>
	const totalPerPage = document.querySelectorAll('#pdocrud_records_per_page > option') as NodeListOf<HTMLOptionElement>
	const headers = Array.from(getTableHeaders().cells)

	const applyConfigsAfterTimeout = () => setTimeout(() => {
		if(loadGif.style.display !== "none") {
			applyConfigsAfterTimeout()
			return
		}
		configurePage(elemRef, refModal, refModalId, refOnlineOrderNumber, refURLInput)
	}, 100)

	saveBtn.onclick = () => applyConfigsAfterTimeout()
	searchBtn.onclick = () => applyConfigsAfterTimeout()
	pagesItems.forEach(anchor => anchor.onclick = () => applyConfigsAfterTimeout())
	totalPerPage.forEach(option => option.onclick = () => applyConfigsAfterTimeout())
	headers.forEach(header => header.onclick = () => applyConfigsAfterTimeout())
}

export default setConfigurationReapply
