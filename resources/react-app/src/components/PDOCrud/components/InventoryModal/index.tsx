import { Modal } from "@mui/material"
import api from "../../../../services/axios"
import "./style.css"
import { MouseEventHandler, useState } from "react"
import { toast } from "react-toastify"
import { InventoryModalProps } from "./types"

export const InventoryModal = (props: InventoryModalProps) => {
	const [ isOpen, setIsOpen ] = useState(false)
	const { isbn } = props

    const handleOpen: MouseEventHandler = event => {
		event.preventDefault()
		setIsOpen(true)
	}

	const updateAvaliableQuantity = () => {
		api.patch('/api/inventory/avaliable-quantity', {
			isbn: isbn,         
		  })
		  .then((response) => {
			if(response.data[1] === 502) return toast.error(response.data[0])
			toast.success(`ISBN ${isbn} associado com sucesso`)
			setIsOpen(false)
		  })
		  .catch(() => toast.error(`Erro ao associar o ISBN ${isbn} com o pedido`))
	} 

    return (
        <>
			<button 
				className={"inventory-icon"}
				onClick={handleOpen}
			>
				<i className="fa-solid fa-book"></i>
			</button>   
			<Modal
				className="inventory-modal" 
				open={isOpen}
				onClose={() => setIsOpen(false)}
			>
				<div className="inventory-modal-container">
					<div className="close-inventory-modal" onClick={() => setIsOpen(false)}>
						<i className="fa-solid fa-xmark"></i>
					</div>
					<div className="inventory-container-block">
						<strong>Associar ao pedido?</strong>
						<div className="inventory-modal-btns">
							<button className="inventory-modal-btn confirm-btn" onClick={updateAvaliableQuantity}>Sim</button>
							<button className="inventory-modal-btn" onClick={() => setIsOpen(false)}>Não</button>
						</div>
					</div>
				</div>
			</Modal>                     
        </>
    )
}