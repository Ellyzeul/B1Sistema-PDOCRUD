import { MutableRefObject } from "react"
import configurePage from "./configurePage"

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

export default setSearchWorkaround
