import { useEffect, useRef, useState } from "react"
import api from "../../services/axios"
import { configurePage } from "./functions"
import { PDOCrudProp } from "./types"

export const PDOCrud = (props: PDOCrudProp) => {
	const { refModal, refModalId, refOnlineOrderNumber, refURLInput } = props
	const [rawHTML, setRawHTML] = useState("")
	const [pdocrud, setPDOCrud] = useState(null as HTMLDivElement|null)
	const elemRef = useRef(null)

	useEffect(() => {
		const params = window.location.search
		api.get(`/api/orders/read${params}`)
			.then(response => response.data)
			.then(response => setRawHTML(response.html))
	}, [setRawHTML])

	useEffect(() => {
		if(!elemRef.current) return
		setPDOCrud(elemRef.current as HTMLDivElement)
		if(!pdocrud) return
		configurePage(
			elemRef,
			refModal,
			refModalId,
			refOnlineOrderNumber,
			refURLInput
		)
		console.log(pdocrud)
	}, [elemRef, pdocrud, rawHTML])

	return (
		<div 
			ref={elemRef} 
			dangerouslySetInnerHTML={{__html: rawHTML}} 
		/>
	)
}
