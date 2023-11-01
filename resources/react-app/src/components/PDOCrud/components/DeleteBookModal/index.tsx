import { Modal } from "@mui/material"
import { MouseEventHandler, useRef, useState } from "react"
import { DeleteBookButtonProps } from "./types"
import api from "../../../../services/axios"
import { toast } from "react-toastify"
import "./style.css"
import apiServices from "../../../../services/apiServices"

export const DeleteBookModal = (props: DeleteBookButtonProps) => {
	const [ isOpen, setIsOpen ] = useState(false)
	const { isbn } = props

  const handleOpen: MouseEventHandler = event => {
		event.preventDefault()
		setIsOpen(true)
	}

  const deleteFromSellercentral = () => {
    apiServices.delete('/offer', {data: {isbn: isbn}})
    	.then(response => response.data as {status: string, sellercentral: string}[])
			.then(response => {
				const toastTimer = { autoClose: 10000 }
				response.forEach(({ status, sellercentral }) => {
					if(status === 'not_found') return toast.success(`Anúncio não existente em: ${sellercentral}`, toastTimer)
					if(status === 'deleted') return toast.success(`Exclusão feita com sucesso em: ${sellercentral}`, toastTimer)

					toast.error(`Erro ao exlcuir em: ${sellercentral}`, toastTimer)
				})
			})
			.catch((err) => {
				console.log(err)
				toast.error(`Erro ao excluir o anúncio de ISBN: ${isbn}. Em todos os canais de venda`)
			})
  }

  return (
	<>
	<button 
		className={"delete-book-icon"}
		onClick={handleOpen}
	>
		<img src="/icons/delete_sellercentral.png"></img>
	</button>   
	<Modal
		className="delete-sellercentral-modal" 
		open={isOpen}
		onClose={() => setIsOpen(false)}
	>
		<div className="delete-sellercentral-modal-container">
			<div className="close-delete-sellercentral-modal" onClick={() => setIsOpen(false)}>
				<i className="fa-solid fa-xmark"></i>
			</div>
			<div className="delete-sellercentral-container-block">
				<strong>Deletar permanentemente o ISBN {isbn.trim()}?</strong>
				<div className="delete-sellercentral-modal-btns">
					<button className="delete-sellercentral-modal-btn delete-sellercentral-btn" onClick={deleteFromSellercentral}>Sim</button>
					<button className="delete-sellercentral-modal-btn" onClick={() => setIsOpen(false)}>Não</button>
				</div>                        
			</div>
		</div>
	</Modal>                     
  </>
  )
}