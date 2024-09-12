import { useEffect, useRef, useState } from "react"
import "./style.css"
import SupplierPurchaseItemRow from "./SupplierPurchaseItemRow"
import SupplierPurchaseItemRowContext from "../../contexts/SupplierPurchaseItemRow"
import { toast, ToastContainer } from "react-toastify"
import api from "../../services/axios"
import { BankAccount, SupplierPurchase } from "../../pages/Purchases/SupplierPurchase/types"
import { CostBenefitPrices } from "./CostBenefitIndex/types"
import CostBenefitIndex from "./CostBenefitIndex"

export default function SupplierPurchaseModal({isOpen, setIsOpen, purchase, paymentMethods, bankAccounts}: Prop) {
  const [tableRows, setTableRows] = useState([<SupplierPurchaseItemRow key={0} id={0}/>])
  const [rowId, setRowId] = useState((purchase?.items.length || 0) + 1)
  const [savedPurchase, setSavedPurchase] = useState(purchase)
  const [modalState, setModalState] = useState({
    items: savedPurchase?.items.map(({value}, id) => [id+1, Number(value)]).reduce((acc, [id, value]) => ({...acc, [id]: value}), {}) ?? {},
    freight: savedPurchase?.freight ?? 0,
    selling_price: {},
    id_company: savedPurchase?.id_company ?? 0,
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
    setModalState({...modalState, freight: Number(input.value.replace(',', '.'))})

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
      const bankAccountSelect = formRef.current.querySelector<HTMLSelectElement>("select[name='bank_account']")
  
      if(paymentMethodSelect) setTimeout(() => {
        paymentMethodSelect.value = String(savedPurchase?.id_payment_method ?? '')
      }, 1000)
      
      if(bankAccountSelect) setTimeout(() => {
        bankAccountSelect.value = String(savedPurchase?.id_bank ?? '')
      }, 1000)
    }

    setTableRows(savedPurchase.items.map((item, key) => <SupplierPurchaseItemRow
      key={key}
      id={key+1}
      item={item}
    />))
  }, [savedPurchase, formRef])

  useEffect(() => {
    const form = formRef.current
    if(!form) return
    const select = form.querySelector<HTMLSelectElement>("select[name='bank_account']")
    if(!select) return

    select.value = ''
  }, [modalState.id_company])

  return (
    <div className={`supplier-purchase-modal-component ${isOpen ? '' : 'supplier-purchase-modal-is-close'}`}>
      <div className="supplier-purchase-modal-container">
        <div className="supplier-purchase-modal-close" onClick={() => setTimeout(() => setIsOpen(false), 1)}>X</div>
        <div className="supplier-purchase-modal-save" onClick={savePurchase}>Salvar</div>
        <div className="supplier-purchase-purchase-id">{savedPurchase?.id ? `Compra Nº ${savedPurchase.id}` : ''}</div>       <form ref={formRef} className="supplier-purchase-modal-form">
          <div className="supplier-purchase-modal-form-supplier">
            <label htmlFor="company">Empresa: </label>
            <select
              name="company"
              defaultValue={savedPurchase?.id_company}
              onInput={({target}) => setModalState({...modalState, id_company: Number((target as HTMLSelectElement).value)})}
            >
              <option value="0">Seline</option>
              <option value="1">B1</option>
              <option value="2">J1</option>
              <option value="3">R1</option>
            </select>
            <span> {COMPANIES[modalState.id_company] ?? ''}</span>
            <br />
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
            <CostBenefitIndex modalState={modalState}/>
            <hr />
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
            <div>Subtotal: R$ {subtotal(modalState.items)}</div>
            <label htmlFor="freight">Frete: </label>
            <input
              type="text"
              name="freight"
              onBlur={({target}) => handleFreightBlur(target)}
              defaultValue={savedPurchase?.freight.toFixed(2).replace('.', ',') || '0,00'}
            />
            <br />
            <strong>Total: R$ {total(modalState.items, modalState.freight)}</strong>
            <br />
            <label htmlFor="observation">Observações: </label>
            <textarea name="observation" defaultValue={savedPurchase?.observation} rows={4} style={{width: '95%'}}/>
            <hr />
            <label htmlFor="bank_account">Banco: </label>
            <select name="bank_account">
              <option key={0} value="">Selecione</option>
              {bankAccounts
                .filter(({id_company}) => id_company === modalState.id_company)
                .map(({id_bank, name, agency, account}, key) => <option key={key+1} value={id_bank}>
                  {`${id_bank} - ${name} - ${agency} - ${account}`}
                </option>)
              }
            </select>
            <br />
            <label htmlFor="payment_method">Forma de pagamento: </label>
            <select name="payment_method">{[
              <option key={0} value=''>Selecione</option>,
              ...paymentMethods
            ]}</select>
            <br />
            <label htmlFor="payment_date">Data do pagamento:</label>
            <input type="date" name="payment_date" defaultValue={savedPurchase?.payment_date}/>
            <br />
            <hr />
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
                <SupplierPurchaseItemRowContext.Provider value={{tableRows, setTableRows, modalState, setModalState}}>
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
  bankAccounts: Array<BankAccount>,
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
    id_company: Number(fieldValue(form, "select[name='company']")),
    date: fieldValue(form, "input[name='date']"),
    status: fieldValue(form, "select[name='status']"),
    supplier: fieldValue(form, "input[name='supplier']")?.trim(),
    purchase_method: fieldValue(form, "select[name='purchase_method']", 'select'),
    freight: Number(fieldValue(form, "input[name='freight']")?.replace(',', '.')),
    observation: fieldValue(form, "textarea[name='observation']"),
    bank_account: fieldValue(form, "select[name='bank_account']", 'select'),
    payment_method: fieldValue(form, "select[name='payment_method']", 'select'),
    payment_date: fieldValue(form, "input[name='payment_date']"),
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
        status: row.querySelector<HTMLInputElement>("input[name='item_status']:checked")?.value,
      }
      const id = fieldValue(row, "input[name='item_id']")

      return id && id !== ''
        ? {...body, id: Number(id)}
        : body
    })
}

function fieldValue(parent: HTMLElement, selector: string, element: 'input' | 'select' | 'textarea' = 'input') {
  let field: HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement | null

  if(element === 'input') field = parent.querySelector<HTMLInputElement>(selector)
  if(element === 'textarea') field = parent.querySelector<HTMLTextAreaElement>(selector)
  else field = parent.querySelector<HTMLSelectElement>(selector)

  if(!field) return null

  return field.value
}

function validateForm(form: HTMLFormElement) {
  return fieldValue(form, "input[name='supplier']") !== '' &&
    fieldValue(form, "input[name='freight']") !== ''
}

function subtotal(items: Record<number, number>) {
  return calculateSubtotal(items)
    .toFixed(2)
    .replace('.', ',')
}

function total(items: Record<number, number>, freight: number) {
  return (calculateSubtotal(items) + freight)
    .toFixed(2)
    .replace('.', ',')
}

function calculateSubtotal(items: Record<number, number>) {
  return Object.keys(items)
    .map((id) => items[Number(id)])
    .reduce((acc, cur) => acc + cur, 0)
}

const COMPANIES: Record<number, string> = {
  0: 'RV de Lima Comercio de Livros Ltda - CNPJ 26.779.333/0001-54',
  1: 'B1 Comercio de Livros e Distribuidora LTDA - CNPJ 47.317.204/0001-14',
  2: '54.315.385 Jaqueline Oliveira da Silva - CNPJ 54.315.385/0001-05',
  3: '32.853.054 Renato Vicente de Lima - CNPJ 32.853.054/0001-96',
}
