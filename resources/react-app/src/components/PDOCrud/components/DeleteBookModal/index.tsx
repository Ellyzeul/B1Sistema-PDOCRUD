import { Modal } from "@mui/material"
import { MouseEventHandler, useRef, useState } from "react"
import { DeleteBookButtonProps } from "./types"
import api from "../../../../services/axios"
import { toast } from "react-toastify"
import "./style.css"

export const DeleteBookModal = (props: DeleteBookButtonProps) => {
	const [ isOpen, setIsOpen ] = useState(false)
  const sellercentralRef = useRef(null)
	const { isbn } = props

  const handleOpen: MouseEventHandler = event => {
		event.preventDefault()
		setIsOpen(true)
	}

  const deleteFromSellercentral = () => {
    if(!sellercentralRef.current) return
    const sellercentral = (sellercentralRef.current as HTMLSelectElement).value

    api.delete("http://servicos.b1sistema.com.br:6500/api/book/delete", {
      data: {
        isbn: isbn,
        sellercentral: sellercentral
      }
    })
    .then((response) => {
      if(response.data.status === 200) return toast.success(response.data.message)
        toast.error(response.data.message)
    }).catch(() => toast.error(`Erro ao deletar ISBN ${isbn} do canal de venda: ${sellercentral}`))
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
        <div className="sellercentrals">
        	<label><strong>Canais de venda:</strong></label>
					<div className="info-sellercentrals">
						<p className="status-sellercentral">Nuvemshop (Seline):</p>
						<p className="status-sellercentral">Mercado Livre (Seline):</p>
						<p className="status-sellercentral">Fnac PT (Seline):</p>
						<p className="status-sellercentral">Fnac ES (Seline):</p>
						<p className="status-sellercentral">Fnac FR (Seline):</p>
						<p className="status-sellercentral">Mercado Livre (B1):</p>
					</div>
        </div>
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