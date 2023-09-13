import { Modal } from "@mui/material"
import api from "../../../../services/axios"
import "./style.css"
import { MouseEventHandler, useState } from "react"
import { toast } from "react-toastify"
import { BookData, InventoryModalProps } from "./types"

export const InventoryModal = (props: InventoryModalProps) => {
	const [ isOpen, setIsOpen ] = useState(false)
	const [ bookData, setBookData ] = useState({} as BookData) 
	const { isbn } = props

    const handleOpen: MouseEventHandler = event => {
		getAvaliableQuantity()
		event.preventDefault()
		setIsOpen(true)
	}

	const getAvaliableQuantity = () => {
		api.get(`/api/inventory/search?isbn=${isbn}`)
			.then((response) => setBookData(response.data[0]))
			.catch((err) => console.log(err))
	}

	const updateAvaliableQuantity = () => {
		api.patch('/api/inventory/avaliable-quantity', {
			isbn: isbn,         
		  })
		  .then((response) => {
			if(response.data[1] === 502) return toast.error(response.data[0])
			setBookData(response.data[0])
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
						<div className="book-info">
							<p>
								{`ISBN: ${bookData.isbn}\n 
								/ Quantidade:${bookData.quantity}\n
								/ Condição: ${bookData.condition}\n
								/ Localização: ${bookData.location}\n
								/ Prateleira: ${bookData.bookshelf === undefined ? "" : bookData.bookshelf}\n
								/ Observação: ${bookData.obsertation === undefined ? "" : bookData.obsertation}\n`}
							</p>
						</div>
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