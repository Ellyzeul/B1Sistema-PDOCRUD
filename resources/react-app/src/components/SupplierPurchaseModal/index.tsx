import { useEffect, useRef, useState } from "react"
import "./style.css"
import SupplierPurchaseItemRow from "./SupplierPurchaseItemRow"
import SupplierPurchaseItemRowContext from "../../contexts/SupplierPurchaseItemRow"
import { toast, ToastContainer } from "react-toastify"
import api from "../../services/axios"
import { SupplierPurchase } from "../../pages/Purchases/SupplierPurchase/types"

export default function SupplierPurchaseModal({isOpen, setIsOpen, purchase}: Prop) {
  const [tableRows, setTableRows] = useState([<SupplierPurchaseItemRow key={0} id={0}/>])
  const [rowId, setRowId] = useState((purchase?.items.length || 0) + 1)
  const [savedPurchase, setSavedPurchase] = useState(purchase)
  const formRef = useRef(null)

  function addRow() {
    setTableRows([...tableRows, <SupplierPurchaseItemRow
      key={rowId}
      id={rowId}
    />])
    setRowId(rowId + 1)
  }

  function savePurchase() {
    if(!formRef.current) {
      toast.error('Erro no formulário...')
      return
    }
    const form = formRef.current

    if(!savedPurchase) {
      api.post('/api/supplier-purchase', parseForm(form))
        .then(response => response.data as {purchase: SupplierPurchase})
        .then(({purchase}) => {
          toast.success('Compra salva!')
          setSavedPurchase(purchase)
        })
        .catch(() => toast.error('Algum erro ocorreu...'))
    }
    else {
      api.put('/api/supplier-purchase', parseForm(form, savedPurchase))
        .then(response => response.data)
        .then(() => toast.success('Compra atualizada!'))
        .catch(() => toast.error('Algum erro ocorreu...'))
    }
  }

  useEffect(() => {
    if(!savedPurchase) return

    setTableRows(savedPurchase.items.map((item, key) => <SupplierPurchaseItemRow
      key={key}
      id={key+1}
      item={item}
    />))
  }, [savedPurchase])

  return (
    <div className={`supplier-purchase-modal-component ${isOpen ? '' : 'supplier-purchase-modal-is-close'}`}>
      <div className="supplier-purchase-modal-container">
        <div className="supplier-purchase-modal-close" onClick={() => setTimeout(() => setIsOpen(false), 1)}>X</div>
        <div className="supplier-purchase-modal-save" onClick={savePurchase}>Salvar</div>
        <form ref={formRef} className="supplier-purchase-modal-form">
          <div className="supplier-purchase-modal-form-supplier">
            <label htmlFor="supplier">Fornecedor: </label>
            <input type="text" name="supplier" defaultValue={savedPurchase?.supplier}/>
            <br />
            <label htmlFor="purchase_method">Forma de pagamento: </label>
            <select name="purchase_method" defaultValue={savedPurchase?.purchase_method}>
              <option value="email">Email</option>
              <option value="site">Site</option>
              <option value="phone">Telefone</option>
              <option value="whatsapp">WhatsApp</option>
            </select>
          </div>
          <div>
            <label htmlFor="company">Empresa: </label>
            <select name="company" defaultValue={savedPurchase?.id_company}>
              <option value="0">Seline</option>
              <option value="1">B1</option>
              <option value="2">J1</option>
              <option value="3">R1</option>
            </select>
            <br />
            <label htmlFor="freight">Frete: </label>
            <input type="text" name="freight" defaultValue={savedPurchase?.freight || 0}/>
          </div>
          <div>
            <div className="supplier-purchase-modal-items-header">
              <div>Itens da compra:</div>
              <div
                className="supplier-purchase-modal-items-add-row"
                onClick={addRow}
              >+</div>
            </div>
            <table className="supplier-purchase-modal-items">
              <thead>
                <tr>
                  <th>ID do pedido</th>
                  <th>ISBN</th>
                  <th>Moeda</th>
                  <th>Valor</th>
                  <th>Valor em R$</th>
                  <th>Custo</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <SupplierPurchaseItemRowContext.Provider value={{tableRows, setTableRows}}>
                  {tableRows}
                </SupplierPurchaseItemRowContext.Provider>
              </tbody>
            </table>
          </div>
        </form>
        <ToastContainer/>
      </div>
    </div>
  )
}

type Prop = {
  isOpen: boolean,
  setIsOpen: (isOpen: boolean) => void,
  purchase?: SupplierPurchase,
}

function parseForm(form: HTMLFormElement, purchase?: SupplierPurchase) {
  if(!form) {
    toast.error('Erro ao processar o pedido...')
    throw new Error('Form not found...')
  }
  if(!validateForm(form)) {
    toast.warn('Preencha todos os valores...')
    throw new Error('Form not fully filled...')
  }
  const body = {
    supplier: fieldValue(form, "input[name='supplier']")?.trim(),
    purchase_method: fieldValue(form, "select[name='purchase_method']", 'select'),
    id_company: Number(fieldValue(form, "select[name='company']")),
    freight: Number(fieldValue(form, "input[name='freight']")),
    items: parseItemsTable(form),
  }

  return purchase
    ? {...body, id: purchase.id}
    : body
}

function parseItemsTable(form: HTMLFormElement) {
  const rows = form.querySelectorAll<HTMLTableRowElement>('.supplier-purchase-modal-items > tbody > tr')

  if(!rows) {
    toast.error('Erro ao processar pedido...')
    throw new Error('Items table not found...')
  }

  return Array.from(rows)
    .filter(row => 
      (row.children[0].children[0] as HTMLInputElement).value !== '' &&
      (row.children[5].children[0] as HTMLInputElement).value !== ''
    )
    .map(row => {
      const body = {
        id_order: Number((row.children[0].children[0] as HTMLInputElement).value),
        value: Number((row.children[5].children[0] as HTMLInputElement).value.replace(',', '.')),
      }
      const id = fieldValue(row, "input[name='item_id']")

      return id && id !== ''
        ? {...body, id: Number(id)}
        : body
    })
}

function fieldValue(parent: HTMLElement, selector: string, element: 'input' | 'select' = 'input') {
  let field: HTMLInputElement | HTMLSelectElement | null

  if(element === 'input') field = parent.querySelector<HTMLInputElement>(selector)
  else field = parent.querySelector<HTMLSelectElement>(selector)

  if(!field) return null

  return field.value
}

function validateForm(form: HTMLFormElement) {
  return fieldValue(form, "input[name='supplier']") !== '' &&
    fieldValue(form, "input[name='freight']") !== ''
}
