import { useEffect, useRef, useState } from "react"
import { Navbar } from "../../../components/Navbar"
import api from "../../../services/axios"
import "./style.css"
import { Invoice } from "./types"
import { toast, ToastContainer } from "react-toastify"

export default function MatchPage() {
  const [invoices, setInvoices] = useState({
    linked: [],
    partially_linked: [],
    not_linked: [],
  } as {[key: string]: Array<Invoice>})
  const [filter, setFilter] = useState({
    match: 'not_linked',
    search: '',
  })

  useEffect(() => {
    api.get('/api/invoice')
      .then(response => response.data)
      .then(setInvoices)
  }, [])

  function filterSearch(invoice: Invoice) {
    const {search} = filter

    if(invoice.key.includes(search)) return true
    if(invoice.emitter.name?.toLowerCase().includes(search.toLowerCase())) return true
    if(invoice.value.toFixed(2).includes(search.replace(',', '.'))) return true
    if(new Date(invoice.emitted_at).toLocaleDateString().includes(search)) return true
    if(invoice.period.includes(search)) return true

    return false
  }

  return (
    <>
      <div className="page-container">
        <Navbar items={[]}/>
        <div className="content">
          <div className="match-page-container">
            <div className="filter-container">
              <div>
                Tipo de vínculo
                <br />
                <select defaultValue="not_linked" onChange={({target}) => setFilter({...filter, match: target.value})}>
                  <option value="not_linked">Sem vínculo</option>
                  <option value="partially_linked">Vínculo parcial</option>
                  <option value="linked">Vínculo completo</option>
                </select>
              </div>
              <div>
                Pesquisa
                <br />
                <input type="text" onChange={({target}) => setFilter({...filter, search: target.value})}/>
              </div>
            </div>
            <div className="table-container">
              <table>
                <thead>
                  <tr>
                    <th>Chave</th>
                    <th>Emitente</th>
                    <th>Valor</th>
                    <th>Emissão</th>
                    <th>Periodo</th>
                  </tr>
                </thead>
                <tbody>
                  {
                    invoices[filter.match]
                      .filter(filterSearch)
                      .map((invoice, key) => <MatchTableRow key={key} invoice={invoice}/>)
                  }
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <ToastContainer/>
    </>
  )
}

function MatchTableRow({invoice}: {invoice: Invoice}) {
  const {key, emitted_at, value, period, emitter: {name}} = invoice
  const [openModal, setOpenModal] = useState(false)

  return (
    <>
      <tr className="match-page-table-row" onClick={() => setOpenModal(true)}>
        <td>{key}</td>
        <td>{name}</td>
        <td>R$ {value.toFixed(2).replace('.', ',')}</td>
        <td>{new Date(emitted_at).toLocaleDateString()}</td>
        <td>{period}</td>
      </tr>
      <Modal open={openModal} setOpen={setOpenModal} invoice={invoice}/>
    </>
  )
}

function Modal({open, setOpen, invoice}: ModalProp) {
  const [purchaseItems, setPurchaseItems] = useState({
    linked: [], 
    not_linked: []
  } as {linked: Array<SupplierPurchaseItem>, not_linked: Array<SupplierPurchaseItem>})
  const [filter, setFilter] = useState({
    id_purchase: '',
    search: '',
  })
  const matchSelectRef = useRef(null as HTMLSelectElement | null)

  useEffect(() => {
    if(matchSelectRef.current) {
      matchSelectRef.current.value = invoice.match
    }
    if(!open || purchaseItems.not_linked.length > 0 || purchaseItems.linked.length > 0) return

    api.get(`/api/invoice/purchase-items?access_key=${invoice.key}`)
      .then(response => response.data)
      .then(setPurchaseItems)
  }, [open, matchSelectRef])

  function filterSearch(item: SupplierPurchaseItem) {
    const {search} = filter

    if(item.supplier?.name.toLowerCase().includes(search.toLowerCase())) return true
    if(item.value.toFixed(2).includes(search.replace(',', '.'))) return true
    if(String(item.items_on_purchase).includes(search)) return true

    return false
  }

  return (
    <div className={`match-page-modal ${!open && 'match-page-modal-closed'}`}>
      <div className="match-page-modal-container">
        <div className="match-page-modal-header">
          <span>{invoice.emitter.name} - {new Date(invoice.emitted_at).toLocaleDateString()}</span>
          <div>
            Vínculo atual:
            <select ref={matchSelectRef}>
              <option value="not_linked">Sem vínculo</option>
              <option value="partially_linked">Vínculo parcial</option>
              <option value="linked">Vínculo completo</option>
            </select>
          </div>
        </div>
        <div>
          <p>Itens associados:</p>
          <table>
            <thead>
              <tr>
                <th>Nº</th>
                <th>Fornecedor</th>
                <th>Valor de compra</th>
                <th>Ação</th>
              </tr>
            </thead>
            <tbody>
              {purchaseItems.linked.length === 0
              ? <tr><td>Lista vazia</td></tr>
              : purchaseItems.linked.map((item, key) => <tr key={key}>
                <td>{item.id_purchase}</td>
                <td>{item.supplier?.name ?? '---'}</td>
                <td>R$ {item.value.toFixed(2).replace('.', ',')}</td>
                <td>
                  <button
                    className="match-page-modal-remove-button"
                    onClick={() => setPurchaseItems({
                      linked: purchaseItems.linked.filter(lited => lited.id !== item.id),
                      not_linked: [...purchaseItems.not_linked, item].sort((a, b) => a.id < b.id ? -1 : 1)
                    })}
                  >
                    Remover
                  </button>
                </td>
              </tr>)}
            </tbody>
          </table>
        </div>
        <div>
          <p>Itens não associados:</p>
          <div className="match-page-modal-not-linked-filter">
            <div>
              Nº compra
              <br />
              <input type="text" style={{width: '5rem'}} onChange={({target}) => setFilter({...filter, id_purchase: target.value})}/>
            </div>
            <div>
              Pesquisa
              <br />
              <input type="text" onChange={({target}) => setFilter({...filter, search: target.value})}/>
            </div>
          </div>
          <table>
            <thead>
              <tr>
                <th>Nº</th>
                <th>Fornecedor</th>
                <th>Valor de compra</th>
                <th>Itens na compra</th>
              </tr>
            </thead>
            <tbody>
              {purchaseItems.not_linked
                .filter(({id_purchase}) => String(id_purchase).includes(filter.id_purchase))
                .filter(filterSearch)
                .map((item, key) => <tr
                  key={key}
                  className="match-page-modal-not-linked-table-row"
                  onClick={() => setPurchaseItems({
                    linked: [...purchaseItems.linked, item].sort((a, b) => a.id < b.id ? -1 : 1),
                    not_linked: purchaseItems.not_linked.filter(listed => listed.id !== item.id)
                  })}
                >
                  <td>{item.id_purchase}</td>
                  <td>{item.supplier?.name ?? '---'}</td>
                  <td>R$ {item.value.toFixed(2).replace('.', ',')}</td>
                  <td>{item.items_on_purchase}</td>
                </tr>)
              }
            </tbody>
          </table>
        </div>
        <button
          className="match-page-modal-save-button"
          onClick={() => {
            if(!matchSelectRef.current) return
            const matchSelect = matchSelectRef.current
            const loadingId = toast.loading('Processando...')

            api.put('/api/invoice/purchase-items', {
              linked: purchaseItems.linked,
              access_key: invoice.key,
              match: matchSelect.value,
            })
              .then(response => response.data)
              .then(response => {
                toast.dismiss(loadingId)
                if(response.success) toast.success('Vínculo atualizado')
              })
              .catch(() => {
                toast.dismiss(loadingId)
                toast.error('Erro ao processar vínculo...')
              })
          }}
        >Salvar</button>
        <button className="match-page-modal-close-button" onClick={() => setOpen(false)}>Fechar</button>
      </div>
    </div>
  )
}
type ModalProp = {
  open: boolean,
  setOpen: (open: boolean) => void,
  invoice: Invoice,
}
type SupplierPurchaseItem = {
  id: number,
  id_purchase: number,
  id_order: number,
  value: number,
  status: 'pending' | 'delivered' | 'cancelled' | 'failed',
  invoice_key: string,
  supplier?: {
    id: number,
    name: string,
  },
  items_on_purchase: number,
}
