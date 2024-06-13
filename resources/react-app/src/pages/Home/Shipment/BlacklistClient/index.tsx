import { FormEvent, FormEventHandler, useState } from "react"
import { Navbar } from "../../../../components/Navbar"
import "./style.css"
import api from "../../../../services/axios"
import { ToastContainer, toast } from "react-toastify"

export default function BlacklistClientPage() {
  const [modalOpen, setModalOpen] = useState('hidden' as 'hidden' | 'visible')

  function openModal() {
    setModalOpen('visible')
  }
  function closeModal() {
    setModalOpen('hidden')
  }

  function handleSubmit(event: FormEvent) {
    event.preventDefault()
    const form = (event.target as HTMLInputElement).parentElement as HTMLFormElement

    if(form['key'].value === '') return
    const loadingId = toast.loading('Processando...')

    api.post('/api/client-blacklist', {
      key: form['key']?.value.replaceAll(/([^\d])/g, ''),
      type: form['type']?.value,
    })
      .then(response => response.data)
      .then(({key, type}) => {
        toast.dismiss(loadingId)
        toast.success(`${type.toUpperCase()} ${key} gravado!`)
      })
      .catch(({response: {data: {err_msg}}}) => {
        toast.dismiss(loadingId)
        toast.error(err_msg)
      })
  }

  return (
    <div id="blacklist-client-container">
      <Navbar items={[]}/>
      <div id="content">
        <div id="table-container">
          <div id="container-head">
            <div>
              <input type="text" />
              <button>Buscar</button>
            </div>
            <div>
              <button id="add-item-button" onClick={openModal}>+</button>
            </div>
          </div>
          <div id="container-body"></div>
        </div>
      </div>
      <div id="modal-container" className={modalOpen} onClick={e => (e.target as HTMLElement).className === 'visible' && closeModal()}>
        <div id="modal-content">
          <form>
            <p>Adição de registro</p>
            <div>
              <input name="key" type="text"/>
              <select name="type">
                <option value="cpf">CPF/CNPJ</option>
                <option value="cep">CEP</option>
              </select>
            </div>
            <input id="modal-submit" onClick={handleSubmit as FormEventHandler} type="submit" value="Adicionar"/>
          </form>
          <span onClick={closeModal}>X</span>
        </div>
      </div>
      <ToastContainer/>
    </div>
  )
}
