import { FormEvent, FormEventHandler, useEffect, useRef, useState } from "react"
import { Navbar } from "../../../../components/Navbar"
import "./style.css"
import api from "../../../../services/axios"
import { ToastContainer, toast } from "react-toastify"

const PLACEHOLDER = <tr key={0}>
  <td><p id="table-placeholder">Sem registros...</p></td>
  <td></td>
</tr>

export default function BlacklistClientPage() {
  const [modalOpen, setModalOpen] = useState('hidden' as 'hidden' | 'visible')
  const [list, setList] = useState([PLACEHOLDER])
  const inputRef = useRef(null as HTMLInputElement | null)

  async function requestList(search?: string): Promise<Array<JSX.Element>> {
    return api.get('/api/client-blacklist' + (search ? `/${search}` : ''))
      .then(response => response.data)
      .then((list: Array<{key: string, type: string}>) => list.map(({key, type}, id) => <tr key={id}>
        <td>{key}</td>
        <td>{type.toUpperCase()}</td>
      </tr>))
      .then(list => list.length > 0 ? list : [PLACEHOLDER])
  }

  useEffect(() => {(async() => {
    setList(await requestList())
  })()}, [])

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
              <input type="text" ref={inputRef}/>
              <button
                onClick={async() => inputRef.current && setList(await requestList(inputRef.current.value))}
              >
                Buscar
              </button>
            </div>
            <div>
              <button id="add-item-button" onClick={openModal}>+</button>
            </div>
          </div>
          <div id="container-body">
            <table id="blacklist-table">
              <thead>
                <tr>
                  <th>Valor</th>
                  <th>Tipo</th>
                </tr>
              </thead>
              <tbody>{list}</tbody>
            </table>
          </div>
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
