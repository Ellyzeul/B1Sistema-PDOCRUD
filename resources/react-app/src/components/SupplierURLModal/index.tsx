import { FormEventHandler, useCallback, useEffect } from "react"
import { SupplierURLModalProp } from "./types"
import "./style.css"
import api from "../../services/axios"

export const SupplierURLModal = (props: SupplierURLModalProp) => {
	const { refModal, refModalId, refURLInput, refOnlineOrderNumber } = props

	const saveUrlModal: FormEventHandler = (event) => {
		event.preventDefault()
		if((!refModalId.current) || (!refURLInput.current)) return
		const modalId = refModalId.current as HTMLDivElement
		const urlInput = refURLInput.current as HTMLInputElement
		const idRaw = modalId.textContent as string
		const id = (idRaw.match(/[0-9]{1,}/) as string[])[0]
		const supplierURL = urlInput.value

		api.post('/api/supplier_url/update', {
				id: id,
				supplier_url: supplierURL
			})
		.then(() => urlModalClose())
	}

	const urlModalClose = useCallback(() => {
		if((!refModal.current) || (!refModalId.current) || (!refOnlineOrderNumber.current) || (!refURLInput.current)) return
		const modal = (refModal.current as HTMLDivElement)
		const modalId = (refModalId.current as HTMLDivElement)
		const onlineOrderNumber = (refOnlineOrderNumber.current as HTMLSpanElement)
		const urlInput = (refURLInput.current as HTMLInputElement)

		modal.style.visibility = 'hidden'
		modalId.textContent = ''
		onlineOrderNumber.textContent = ''
		urlInput.value = ''
		document.body.style.overflowY = 'auto'
	}, [refModal, refModalId, refOnlineOrderNumber, refURLInput])

	useEffect(() => {
		document.body.addEventListener('keydown', (event) => 
			event.key === "Escape" ? urlModalClose() : null
		)
	}, [urlModalClose, refModal, refModalId, refOnlineOrderNumber, refURLInput])

	return (
		<div id="purchase_link_modal" ref={refModal}>
			<form id="purchase_link_form" onSubmit={saveUrlModal}>
				<div id="purchase_link_form_close">
					<i className="fa-solid fa-xmark" onClick={() => urlModalClose()}></i>
				</div>
				<fieldset id="purchase_link_form_content">
					<strong>Link para compra</strong>
					<div id="purchase_link_form_id" ref={refModalId} style={{visibility: 'hidden'}}></div>
					<div id="purchase_link_form_online_order_number">
						<strong>ORIGEM:</strong><span ref={refOnlineOrderNumber}></span>
					</div>
					<div>
						<label htmlFor="supplier_url">URL</label>
						<input type="text" name="supplier_url" id="purchase_link_formsupplier_url" ref={refURLInput} />
					</div>
					<div id="purchase_link_form_submit"><button type="submit">Salvar</button></div>
				</fieldset>
			</form>
		</div>
	)
}
