import { useEffect, useRef, useState } from "react"
import api from "../../services/axios"
import configurePage from "./functions/configurePage"
import { PDOCrudProp } from "./types"
import "./style.css"

export const PDOCrud = (props: PDOCrudProp) => {
	const { refModal, refModalId, refOnlineOrderNumber, refURLInput, refTrackingMethodModal } = props
	const [rawHTML, setRawHTML] = useState("")
	const elemRef = useRef(null)

	useEffect(() => {
		const params = window.location.search
		api.get(`/api/orders/pdocrud-table${params}`)
			.then(response => response.data)
			.then(response => setRawHTML(response.html))
	}, [setRawHTML])

	const setConfigurations = () => configurePage(
		elemRef,
		refModal,
		refModalId,
		refOnlineOrderNumber,
		refURLInput,
		refTrackingMethodModal,
	)

	useEffect(() => {
		if(!elemRef.current) return
		setTimeout(setConfigurations, 10)
	}, [elemRef, rawHTML])

	return (
		<div 
			ref={elemRef} 
			dangerouslySetInnerHTML={{__html: rawHTML}} 
		/>
	)
}
