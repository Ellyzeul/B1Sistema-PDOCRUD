import WhatsappModalProp from "./types"
import { Button, Modal } from "@mui/material"
import api from "../../../../services/axios"
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
        api.get(`/api/orders/ask-rating/whatsapp/get?order_id=${orderId}`)
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
                open={isOpen}
                onClose={() => setIsOpen(false)}
                aria-labelledby="whatsapp-modal"
            >
                <div className="container-modal">
                    <div className="modal-content">
                        <label>
                            Nº de celular do cliente: 
                            <input type="text" ref={inputRef} required></input>
                        </label>
                        <div className="button-container">
                            <button onClick={() => setIsOpen(false)}>Cancelar</button>    
                            <button onClick={onClick}>Enviar mensagem</button>
                        </div>
                    </div>
                </div>
            </Modal>
        </>
    )
}
