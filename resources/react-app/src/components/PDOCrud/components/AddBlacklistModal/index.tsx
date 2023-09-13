import { Modal } from "@mui/material"
import api from "../../../../services/axios"
import "./style.css"
import { MouseEventHandler, useState } from "react"
import { toast } from "react-toastify"
import { BlacklistModalProps } from "./types"

export const AddBlacklistModal = (props: BlacklistModalProps) => {
	const [ isOpen, setIsOpen ] = useState(false)
	const { isbn } = props

    const handleOpen: MouseEventHandler = event => {
		event.preventDefault()
		setIsOpen(true)
	}

    const insertBlacklist = () => {
        api.post('/api/blacklist/insert-or-update', {
            blacklist_type: 1,
            content: isbn,
            observation: 'Esgotado'           
          })
          .then(() => {
            toast.success(`${isbn} adicionado com sucesso`)
            setIsOpen(false)
          })
          .catch(() => toast.error(`Erro ao adicionar ${isbn}`));
    }

    return (
        <>
			<button 
				className={"add-blacklist-icon"}
				onClick={handleOpen}
			>
				<img src="/icons/add_blacklist.png"></img>
			</button>   
			<Modal
				className="add-blacklist-modal" 
				open={isOpen}
				onClose={() => setIsOpen(false)}
			>
				<div className="add-blacklist-modal-container">
					<div className="close-add-blacklist-modal" onClick={() => setIsOpen(false)}>
						<i className="fa-solid fa-xmark"></i>
					</div>
					<div className="add-blacklist-container-block">
						<strong>Adicionar ISBN {isbn} a Lista Negra?</strong>
						<div className="add-blacklist-modal-btns">
							<button className="add-blacklist-modal-btn confirm-btn" onClick={insertBlacklist}>Sim</button>
							<button className="add-blacklist-modal-btn" onClick={() => setIsOpen(false)}>Não</button>
						</div>
					</div>
				</div>
			</Modal>                     
        </>
    )
}