import { useEffect, useRef, useState } from "react"
import "./style.css"
import SupplierPurchaseItemRow from "./SupplierPurchaseItemRow"
import SupplierPurchaseItemRowContext from "../../contexts/SupplierPurchaseItemRow"
import { toast, ToastContainer } from "react-toastify"
import api from "../../services/axios"
import { SupplierPurchase } from "../../pages/Purchases/SupplierPurchase/types"
import { CostBenefitPrices } from "./CostBenefitIndex/types"
import CostBenefitIndex from "./CostBenefitIndex"

export default function SupplierPurchaseModal({isOpen, setIsOpen, purchase, paymentMethods}: Prop) {
  const [tableRows, setTableRows] = useState([<SupplierPurchaseItemRow key={0} id={0}/>])
  const [rowId, setRowId] = useState((purchase?.items.length || 0) + 1)
  const [savedPurchase, setSavedPurchase] = useState(purchase)
  const [prices, setPrices] = useState({
    items: savedPurchase?.items.map(({value}, id) => [id+1, Number(value)]).reduce((acc, [id, value]) => ({...acc, [id]: value}), {}) ?? {},
    freight: savedPurchase?.freight ?? 0,
    selling_price: {},
  } as CostBenefitPrices)
  const formRef = useRef(null as HTMLFormElement | null)

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

  function handleFreightBlur(input: HTMLInputElement) {
    setPrices({...prices, freight: Number(input.value.replace(',', '.'))})

    input.value = Number(input.value.replace(',', '.'))
      .toFixed(2)
      .replace('.', ',')
  }

  useEffect(() => {
    if(!savedPurchase) {
      return
    }
    if(formRef.current) {
      const paymentMethodSelect = formRef.current.querySelector<HTMLSelectElement>("select[name='payment_method']")
  
      if(!paymentMethodSelect) return
      setTimeout(() => {
        paymentMethodSelect.value = String(savedPurchase?.id_payment_method ?? '')
      }, 1000)
    }

    setTableRows(savedPurchase.items.map((item, key) => <SupplierPurchaseItemRow
      key={key}
      id={key+1}
      item={item}
    />))
  }, [savedPurchase, formRef])

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
            <label htmlFor="purchase_method">Forma de compra: </label>
            <select name="purchase_method" defaultValue={savedPurchase?.purchase_method}>
              <option value="email">Email</option>
              <option value="site">Site</option>
              <option value="phone">Telefone</option>
              <option value="whatsapp">WhatsApp</option>
            </select>
            <br />
            <label htmlFor="payment_method">Forma de pagamento: </label>
            <select name="payment_method">{[
              <option key={0} value=''>Selecione</option>,
              ...paymentMethods
            ]}</select>
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
            <input
              type="text"
              name="freight"
              onBlur={({target}) => handleFreightBlur(target)}
              defaultValue={savedPurchase?.freight || 0}
            />
            <br />
            <label htmlFor="date">Data do pedido:</label>
            <input type="date" name="date" defaultValue={savedPurchase?.date || (new Date().toISOString().split('T')[0])}/>
            <br />
            <label htmlFor="status">Status do pedido:</label>
            <select name="status" defaultValue={savedPurchase?.status || 'normal'}>
              <option value="normal">Normal</option>
              <option value="cancelled">Cancelado integral</option>
              <option value="cancelled_partial">Cancelado parcial</option>
              <option value="multiple_delivery">Múltiplas entregas do fornecedor</option>
            </select>
            <br />
            <CostBenefitIndex prices={prices}/>
            <br />
            <div>Subtotal: R$ {subtotal(prices.items)}</div>
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
                  <th>Status</th>
                  <th>ISBN</th>
                  <th>Moeda</th>
                  <th>Valor</th>
                  <th>Valor em R$</th>
                  <th>Custo</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <SupplierPurchaseItemRowContext.Provider value={{tableRows, setTableRows, prices, setPrices}}>
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
  paymentMethods: Array<JSX.Element>,
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
    payment_method: fieldValue(form, "select[name='payment_method']", 'select'),
    id_company: Number(fieldValue(form, "select[name='company']")),
    freight: Number(fieldValue(form, "input[name='freight']")?.replace(',', '.')),
    status: fieldValue(form, "select[name='status']"),
    date: fieldValue(form, "input[name='date']"),
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
      row.querySelector<HTMLInputElement>("input[name='id_order']")?.value !== '' &&
      row.querySelector<HTMLInputElement>("input[name='value']")?.value !== ''
    )
    .map(row => {
      const body = {
        id_order: Number(row.querySelector<HTMLInputElement>("input[name='id_order']")?.value),
        value: Number(row.querySelector<HTMLInputElement>("input[name='value']")?.value.replace(',', '.')),
        status: row.querySelector<HTMLSelectElement>("select[name='status']")?.value,
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

function subtotal(items: Record<number, number>) {
  return Object.keys(items)
    .map((id) => items[Number(id)])
    .reduce((acc, cur) => acc + cur, 0)
    .toFixed(2)
    .replace('.', ',')
}
