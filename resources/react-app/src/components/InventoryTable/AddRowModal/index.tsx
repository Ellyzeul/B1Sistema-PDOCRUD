import { Modal } from "@mui/material"
import { MouseEventHandler, useRef, useState } from "react"
import { toast } from "react-toastify";
import api from "../../../services/axios";
import "./style.css";

export const AddRowModal = () => {
  const [ isOpen, setIsOpen ] = useState(false)
  const addRowContainerRef = useRef(null)
  
  const handleOpen: MouseEventHandler = event => {
		event.preventDefault()
		setIsOpen(true)
	}

  const addRow = () => {
    if(!addRowContainerRef.current) return
    const div = addRowContainerRef.current as HTMLDivElement
    const isbn = (div.querySelector('input[name="inputISBN"]') as HTMLInputElement).value
    const quantity = (div.querySelector('input[name="inputQuantity"]') as HTMLInputElement).value
    const condition = (div.querySelector('select[name="selectCondition"]') as HTMLSelectElement).value 
    const location = (div.querySelector('select[name="selectLocation"]') as HTMLSelectElement).value
    const bookshelf = (div.querySelector('input[name="inputBookshelf"]') as HTMLInputElement).value
    const observation = (div.querySelector('input[name="inputObservation"]') as HTMLInputElement).value

    console.log(isbn, quantity, condition, location, bookshelf, observation)
    if(!isbn) return toast.error(`Campo ISBN deve ser preenchido`)
    if(!quantity) return toast.error(`Campo Quantidade deve ser preenchido`)

    api.post('/api/inventory/insert-or-update', {
      isbn: isbn,
      quantity: quantity,
      id_condition: condition,
      id_location: location,
      bookshelf: bookshelf,
      observation: observation           
    })
    .then(() => {
      toast.success(`${isbn} adicionado/atualizado com sucesso`)
      setIsOpen(false)
    })
    .catch(() => toast.error(`Erro ao adicionar/atualizar ${isbn}`));
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
            <div className="add-row-container-block" ref={addRowContainerRef}>
              <label>ISBN</label>
                <input
                  type="text"
                  className="add-row-modal-input"
                  name="inputISBN"
                  required
                />
              <label>Quantidade</label>
                <input 
                  type="text" 
                  className="add-row-modal-input" 
                  name="inputQuantity" 
                  required></input>
              <div >

                <div className="selects-container">
                  <label>Condição:</label>
                  <select name="selectCondition">
                    <option value="1">Novo</option>
                    <option value="2">Usado</option>
                  </select>
                </div>

                  <div className="selects-container">
                    <label>Localização:</label>
                    <select name="selectLocation">
                      <option value="1">São Paulo</option>
                      <option value="2">Sorocaba</option>
                      <option value="3">Natal</option>
                    </select>                        
                  </div>

              </div>

              <label>Prateleira</label>	
                <input 
                  type="text" 
                  className="add-row-modal-input" 
                  name="inputBookshelf" 
                  required></input>
              <label>Observação</label>
                <input 
                  type="text" 
                  className="add-row-modal-input" 
                  name="inputObservation"></input>

              <button className="add-row-modal-btn" onClick={addRow}>Adicionar ou Atualizar</button>
              </div>
            </div>
          </Modal>
        </div>
    )
}