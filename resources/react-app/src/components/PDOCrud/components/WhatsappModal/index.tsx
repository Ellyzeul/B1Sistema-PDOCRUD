import WhatsappModalProp from "./types"
import { Modal } from "@mui/material"
import api from "../../../../services/axios"
import "./style.css"
import { MouseEventHandler, useEffect, useRef, useState } from "react"

export const WhatsappModal = (props: WhatsappModalProp) => {
	const { orderId, companyId } = props
	const inputRef = useRef(null)
	const [ isOpen, setIsOpen ] = useState(false)
	const [ messageData, setMessageData ] = useState({} as {
		formatted_message: string | null,
		cellphone: string | null
	})
	
	const handleOpen: MouseEventHandler = event => {
		event.preventDefault()
		setIsOpen(true)

		setInputValue()
	}

	const setInputValue = () => {
		const idInterval = setInterval(() => {
			if(!inputRef.current) return
			const input = inputRef.current as HTMLInputElement

			clearInterval(idInterval)
			if(!messageData.cellphone) return

			input.value = messageData.cellphone
		}, 1)
	}

	const preventDefault: MouseEventHandler = event => event.preventDefault()

	const onClick: MouseEventHandler = event => {
		if(!messageData.cellphone || !messageData.formatted_message) return
		if(!inputRef.current) return

		const input = inputRef.current as HTMLInputElement
		const text = encodeURIComponent(messageData.formatted_message)
		const phone = input.value
		const url = `https://web.whatsapp.com/send?phone=${phone}&text=${text}`

		window.open(url, '_blank')?.focus()
	}

	useEffect(() => {
		api.get(`/api/orders/ask-rating/whatsapp?order_id=${orderId}`)
			.then(response => response.data)
			.then(setMessageData)
	}, [])

	return (
		<>
			<button 
				className={`order-control-ask-rating-button ${companyId === 0
					? 'order-control-ask-rating-button-wpp'
					: 'order-control-ask-rating-desactivated-button-wpp'
				}`}
				onClick={companyId === 0 ? handleOpen : preventDefault}
			>
				<i className="fa-brands fa-whatsapp"></i>
			</button>
			<Modal
				className="whatsapp-modal" 
				open={isOpen}
				onClose={() => setIsOpen(false)}
			>
				<div className="whatsapp-modal-container">
					<div className="close-whatsapp-modal" onClick={() => setIsOpen(false)}>
						<i className="fa-solid fa-xmark"></i>
					</div>
					<div className="whatsapp-container-block">
						<label>Nº de celular do cliente: </label>
						<input type="text" className="whatsapp-modal-input" ref={inputRef} required></input>
						<div className="whatsapp-modal-example">
							<span>Formatar celular como: DDI + DDD + Número</span>
							<span>Ex.: 5511912341234</span>
						</div>
						<button id="whatsapp-modal-btn" className="whatsapp-modal-btn" onClick={onClick}>Enviar mensagem</button>
					</div>
				</div>
			</Modal>
		</>
	)
}
