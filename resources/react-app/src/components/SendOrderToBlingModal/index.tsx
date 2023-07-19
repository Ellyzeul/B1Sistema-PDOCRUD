import { Modal } from "@mui/material"
import { MouseEventHandler, useState } from "react"
import { Form } from "./Form"
import "./style.css"
import { SendOrderToBlingModalProps } from "./types"

export const SendOrderToBlingModal = (props: SendOrderToBlingModalProps) => {
    const { orderNumber } = props
    const [isOpen, setIsOpen] = useState(false)

    const handleOpen: MouseEventHandler = (event) => {
        const { detail } = event
        event.preventDefault()
        if(detail === 0) return
    
        setIsOpen(true)
    }
    const handleClose = () => setIsOpen(false)

    return (
    <>
        <button
            className="send-order-to-bling-modal"
            onClick={handleOpen}
        >
            <i className="fa-solid fa-pen-to-square edit-button"></i>
        </button>

        <Modal
            className='send-order-to-bling-modal' 
            open={isOpen} 
            onClose={() => {handleClose()}} 
        >
            <div className="send-order-to-bling-modal-container">
                <div className="close-address-modal" onClick={handleClose}>
                    <i className="fa-solid fa-xmark"></i>
                </div>
                <Form orderNumber={orderNumber}/>
            </div>
        </Modal>

    </>
    )
}