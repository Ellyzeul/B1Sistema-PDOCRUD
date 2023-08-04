import { Modal } from "@mui/material"
import { MouseEventHandler, useRef, useState } from "react"
import { toast } from "react-toastify";
import { AddRowModalProps, blacklist_type } from "./types";
import api from "../../../services/axios";
import "./style.css";

export const AddRowModal = (props: AddRowModalProps) => {
    const { name } = props
	const [ isOpen, setIsOpen ] = useState(false)
    const inputNameRef = useRef(null)
    const inputObservationRef = useRef(null)

    const handleOpen: MouseEventHandler = event => {
		event.preventDefault()
		setIsOpen(true)
	}

    const addRow = () => {
        if(!inputNameRef.current || !inputObservationRef.current) return
        const inputName = inputNameRef.current as HTMLInputElement
        const inputObservation = inputObservationRef.current as HTMLInputElement

        if(!inputName.value) return toast.error(`Campo ${name} deve ser preenchido`)
        console.log(inputName.value, inputObservation.value)

        api.post('/api/blacklist/insert-or-update', {
            blacklist_type: blacklist_type[name as keyof typeof blacklist_type],
            content: inputName.value,
            observation: inputObservation.value           
          })
          .then(() => {
            toast.success(`${inputName.value} adicionado/atualizado com sucesso`)
            setIsOpen(false)
          })
          .catch(() => toast.error(`Erro ao adicionar/atualizar ${inputName.value}`));
    }

    return(
        <div>
        <button className="add-row-button" onClick={handleOpen}>+</button>
        <Modal
				className="add-row-modal" 
				open={isOpen}
				onClose={() => setIsOpen(false)}
			>
				<div className="add-row-modal-container">
					<div className="close-add-row-modal" onClick={() => setIsOpen(false)}>
						<i className="fa-solid fa-xmark"></i>
					</div>
					<div className="add-row-container-block">
						<label>{name}</label>
						<input type="text" className="add-row-modal-input" ref={inputNameRef} required></input>
						<label>Observação</label>
						<input type="text" className="add-row-modal-input" ref={inputObservationRef}></input>
						<button className="add-row-modal-btn" onClick={addRow}>Adicionar ou Atualizar</button>
					</div>
				</div>
			</Modal>
        </div>
    )
}