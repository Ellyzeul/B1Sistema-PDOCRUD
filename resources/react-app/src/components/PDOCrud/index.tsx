import { useEffect, useRef, useState } from "react"
import api from "../../services/axios"
import { setOpenModalEvent, setCurrencySymbols, setValuesOnSelects } from "./functions"
import { PDOCrudProp } from "./types"

export const PDOCrud = (props: PDOCrudProp) => {
	const { refModal, refModalId, refOnlineOrderNumber, refURLInput } = props
	const [rawHTML, setRawHTML] = useState("")
	const elemRef = useRef(null)

	useEffect(() => {
		const params = window.location.search
		api.get(`/api/orders/read${params}`)
			.then(response => response.data)
			.then(response => setRawHTML(response.html))
	}, [setRawHTML])

	useEffect(() => {
		if(!elemRef.current) return
		const elem = elemRef.current as HTMLDivElement

		if(elem.children.length === 0) return
		if(!document.querySelectorAll('.pdocrud-data-row')[0].children[1]) return

		(document.querySelector(".panel-title") as HTMLHeadingElement).textContent = "Controle de fases"
		setValuesOnSelects()
		setCurrencySymbols()
		setOpenModalEvent(refModal, refModalId, refOnlineOrderNumber, refURLInput)
	}, [elemRef, rawHTML])

	return (
		<div 
			ref={elemRef} 
			dangerouslySetInnerHTML={{__html: rawHTML}} 
		/>
	)
}
