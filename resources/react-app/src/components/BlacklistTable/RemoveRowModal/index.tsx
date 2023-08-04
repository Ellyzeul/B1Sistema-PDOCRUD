import api from "../../../services/axios"
import { Modal } from "@mui/material"
import { MouseEventHandler, useState } from "react"
import { toast } from "react-toastify"
import { blacklist_type, RemoveRowModalProps } from "./types"
import "./style.css"

export const RemoveRowModal = (props: RemoveRowModalProps) => {
	const { name, content } = props
	const [ isOpen, setIsOpen ] = useState(false)

    const handleOpen: MouseEventHandler = event => {
		event.preventDefault()
		setIsOpen(true)
	}

    const deleteRow = () => {
		console.log(name, content)
        api.delete('/api/blacklist/delete', {
			data : {		
            blacklist_type: blacklist_type[name as keyof typeof blacklist_type],
            content: content,          
          }})
          .then(() => {
            toast.success(`${content} deletado com sucesso`)
            setIsOpen(false)
          })
          .catch(() => toast.error(`Erro ao deletar ${content}`));
    }

    return(
        <>
        <button className="remove-row-button" onClick={handleOpen}><i className="fa-solid fa-trash garbage-icon"></i></button>
        <Modal
				className="remove-row-modal" 
				open={isOpen}
				onClose={() => setIsOpen(false)}
			>
				<div className="remove-row-modal-container">
					<div className="close-remove-row-modal" onClick={() => setIsOpen(false)}>
						<i className="fa-solid fa-xmark"></i>
					</div>
					<div className="remove-row-container-block">
						<label>Tem certeza de que deseja excluir permanentemente essa linha?</label>
            <div>
						  <button className="remove-row-modal-btn delete-btn" onClick={() => deleteRow()}>Excluir linha</button>
						  <button className="remove-row-modal-btn" onClick={() => setIsOpen(false)}>Cancelar</button>
            </div>
					</div>
				</div>
			</Modal>
        </>
    )
}