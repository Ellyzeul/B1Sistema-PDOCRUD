import { useEffect, useRef, useState } from "react";
import { Navbar } from "../../../components/Navbar";
import "./style.css"
import api from "../../../services/axios";
import getCompany from "../../../lib/getCompany";
import { toast, ToastContainer } from "react-toastify";

export default function BanksPage() {
  const [expenses, setExpenses] = useState([] as Array<Expense>)
  const [categories, setCategories] = useState({} as Record<number, string>)
  const [banks, setBanks] = useState([] as Array<{
    id_company: number,
    id_bank: number,
    name: string,
    account: string,
    agency: string,
  }>)
  const [paymentMethods, setPaymentMethods] = useState({} as Record<number, string>)
  const [rows, setRows] = useState(expenses.length === 0
    ? <>Sem registros...</>
    : expenses.map(expense => <ExpenseRow
      expense={expense}
      categories={categories}
      banks={banks}
      payment_methods={paymentMethods}
    />)
  )
  const [filter, setFilter] = useState({
    company: null,
    bank: null
  } as {company: number|null, bank: number|null})

  function filterSearch(searchMixedCase: string) {
    const search = searchMixedCase.toLocaleLowerCase()

    const filtered = expenses.filter(expense => {
      if(expense.annotations?.toLocaleLowerCase().includes(search)) return true
      if(expense.supplier?.toLocaleLowerCase().includes(search)) return true
      if(banks.find(({id_bank}) => id_bank === expense.bank_id)?.name?.toLocaleLowerCase().includes(search)) return true
      if(getCompany(expense.company_id)?.toLocaleLowerCase().includes(search)) return true
      if((new Date(expense.due_date ?? '').toLocaleDateString())?.toLocaleLowerCase().includes(search)) return true
      if(categories[expense.expense_category_id]?.toLocaleLowerCase().includes(search)) return true
      if((new Date(expense.payment_date ?? '')).toLocaleDateString()?.toLocaleLowerCase().includes(search)) return true
      if(paymentMethods[expense.payment_method_id]?.toLocaleLowerCase().includes(search)) return true
      if(expense.status?.toLocaleLowerCase().includes(search)) return true
      if(expense.value?.toLocaleLowerCase().includes(search)) return true

      return false
    })

    setRows(filtered.map(expense => <ExpenseRow
      expense={expense}
      categories={categories}
      banks={banks}
      payment_methods={paymentMethods}
    />))
  }

  useEffect(() => {
    api.get('/api/expense')
      .then(response => response.data)
      .then(({expenses, categories, bank, payment_methods}) => {
        setExpenses(expenses)
        setCategories(categories)
        setBanks(bank)
        setPaymentMethods(payment_methods)

        setRows((expenses as Array<Expense>).map(expense => <ExpenseRow
          expense={expense}
          categories={categories}
          banks={banks}
          payment_methods={paymentMethods}
        />))
      })
  }, [])

  return (
    <div className="expenses-page">
      <Navbar items={[]}/>
      <div className="expenses-page-container">
        <div className="expenses-page-search-container">
          Pesquisa: <input type="text" onChange={({target}) => filterSearch(target.value)}/>
          Grupo: <select name="company" onChange={({target}) => setFilter({...filter, company: Number(target.value)})}>
            <option value=''>Selecione</option>
            <option value="0">Seline</option>
            <option value="1">B1</option>
            <option value="2">J1</option>
            <option value="3">R1</option>
            <option value="5">Livrux</option>
          </select>
          Banco: <select name="bank">
            <option value=''>Selecione</option>
            {
              banks
                .filter(({id_company}) => id_company === filter.company)
                .map(({name, id_bank}) => <option key={id_bank}>{name}</option>)
            }
          </select>
        </div>
        <div className="expenses-page-table-container">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Grupo</th>
                <th>Despesa</th>
                <th>Empresa</th>
                <th>Banco</th>
                <th>Forma</th>
                <th>Data</th>
                <th>Valor</th>
              </tr>
            </thead>
            <tbody>{rows}</tbody>
          </table>
        </div>
      </div>
      <ToastContainer/>
    </div>
  )
}

function ExpenseRow({expense, categories, banks, payment_methods}: ExpenseRowProp) {
  const [isModalOpen, setIsModalOpen] = useState(false)

  return (
    <>
      <tr onClick={() => setIsModalOpen(true)}>
        <td>{expense.id}</td>
        <td>{getCompany(expense.company_id)}</td>
        <td>{categories[expense.expense_category_id]}</td>
        <td>Empresa</td>
        <td>{banks.find(({id_bank}) => id_bank === expense.bank_id)?.name}</td>
        <td>{payment_methods[expense.payment_method_id]}</td>
        <td>{expense.payment_date ? new Date(expense.payment_date).toLocaleDateString() : '-'}</td>
        <td style={{color: expense.type === 'payable' ? 'red' : 'green'}}>R$ {`${expense.value}`.replace('.', ',')}</td>
      </tr>
      <Modal
        isOpen={isModalOpen}
        setIsOpen={setIsModalOpen}
        expense={expense}
        categories={categories}
        banks={banks}
        payment_methods={payment_methods}
      />
    </>
  )
}
type ExpenseRowProp = {
  expense: Expense,
  banks: Array<{
    id_company: number,
    id_bank: number,
    name: string,
    account: string,
    agency: string,
  }>,
  categories: Record<number, string>,
  payment_methods: Record<number, string>,
}

const MODAL_SELECTS: Array<{id: string, key: keyof Expense}> = [
  {id: 'company', key: 'company_id'},
  {id: 'expense_category', key: 'expense_category_id'},
  {id: 'bank_account', key: 'bank_id'},
  {id: 'payment_method', key: 'payment_method_id'},
]
function Modal({isOpen, setIsOpen, expense, banks, categories, payment_methods}: Prop) {
  const [modalState, setModalState] = useState({
    id_company: 0,
  })
  const [savedExpense, setSavedExpense] = useState(expense)
  const formRef = useRef(null as HTMLFormElement | null)

  function save() {
    if(!formRef.current) return
    const form = formRef.current
    const body = parseForm(form, expense)

    if('id' in body) {
      api.patch('/api/expense', body)
        .then(response => response.data)
        .then(response => toast.success('Conta atualizada'))
    }
    else {
      api.post('/api/expense', body)
        .then(response => response.data)
        .then(response => {
          toast.success('Conta inserida')
          setIsOpen(false)
        })
    }
  }

  useEffect(() => {
    if(!formRef.current || !expense) return
    const form = formRef.current

    MODAL_SELECTS.forEach(({id, key}) => {
      const select = form.querySelector(`select[name="${id}"]`) as HTMLSelectElement

      select.value = (expense[key] ?? '') as string
    })
  }, [])

  return (
    <div className={`expense-page-modal ${isOpen ? '' : 'expense-page-modal-is-close'}`}>
      <div className="expense-page-modal-is-close-container">
        <div className="expense-page-modal-is-close-close" onClick={() => setTimeout(() => setIsOpen(false), 1)}>X</div>
        <div className="expense-page-modal-save" onClick={save}>Salvar</div>
        <div className="supplier-purchase-purchase-id">{savedExpense?.id ? `Compra Nº ${savedExpense.id}` : ''}</div>
        <form ref={formRef} className="expense-page-modal-form">
          <div className="expense-page-modal-is-close-form-supplier">
            <label htmlFor="company">Grupo: </label>
            <select
              name="company"
              defaultValue={savedExpense?.company_id}
              onInput={({target}) => setModalState({...modalState, id_company: Number((target as HTMLSelectElement).value)})}
            >
              <option value="0">Seline</option>
              <option value="1">B1</option>
              <option value="2">J1</option>
              <option value="3">R1</option>
              <option value="5">Livrux</option>
              <option value="4">teste</option>
            </select>
            <br />
            <br />
            <div className="supplier-purchase-split-container">
              <div>
                <label htmlFor="due_date">Data de vencimento:</label>
                <input type="date" name="due_date" defaultValue={savedExpense?.due_date ?? ''}/>
                <br />
                <label htmlFor="status">Status do pedido:</label>
                <select name="status" defaultValue={savedExpense?.status ?? 'pending'}>
                  <option value="pending">Pendente</option>
                  <option value="paid">Pago</option>
                  <option value="late">Atrasado</option>
                </select>
              </div>
              <div>
                <label htmlFor="expense_category">Despesa: </label>
                <select name="expense_category" defaultValue={savedExpense?.expense_category_id ?? ''}>
                  <option value="" key={0}>Selecione</option>
                  {Object.keys(categories).map((key, index) => 
                    <option key={index+1} value={key}>{categories[Number(key)]}</option>
                  )}
                </select>
                <br />
                <label htmlFor="supplier">Empresa: </label>
                <input type="text" name="supplier" defaultValue={savedExpense?.supplier ?? ''}/>
              </div>
            </div>
            <hr />
            <br />
            <div className="supplier-purchase-split-container">
              <div>
                <label htmlFor="value">Valor: </label>
                <input name="value" defaultValue={savedExpense?.value.replace('.', ',') ?? 0}/>
                <br />
                <label htmlFor="annotations">Histórico: </label>
                <textarea name="annotations" defaultValue={savedExpense?.annotations ?? ''} rows={6} style={{width: '95%'}}/>
              </div>
              <div>
                <label htmlFor="bank_account">Banco: </label>
                <select name="bank_account">
                  <option key={0} value="">Selecione</option>
                  {banks
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
                  ...(Object.keys(payment_methods).map((key, index) => (
                    <option value={key} key={index+1}>{payment_methods[Number(key)]}</option>
                  )))
                ]}</select>
                <br />
                <label htmlFor="payment_date">Data do pagamento:</label>
                <input type="date" name="payment_date" defaultValue={savedExpense?.payment_date ?? ''}/>
              </div>
            </div>
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
  expense?: Expense,
  banks: Array<{
    id_company: number,
    id_bank: number,
    name: string,
    account: string,
    agency: string,
  }>,
  categories: Record<number, string>,
  payment_methods: Record<number, string>,
}

function parseForm(form: HTMLFormElement, expense?: Expense) {
  if(!form) {
    toast.error('Erro ao processar a conta...')
    throw new Error('Form not found...')
  }

  const body = {
    annotations: form.querySelector<HTMLTextAreaElement>('textarea[name="annotations"]')?.value,
    supplier: form.querySelector<HTMLInputElement>('input[name="supplier"]')?.value,
    bank_id: Number(form.querySelector<HTMLSelectElement>('select[name="bank_account"]')?.value),
    company_id: Number(form.querySelector<HTMLSelectElement>('select[name="company"]')?.value),
    due_date: form.querySelector<HTMLInputElement>('input[name="due_date"]')?.value,
    expense_category_id: Number(form.querySelector<HTMLSelectElement>('select[name="expense_category"]')?.value),
    payment_date: form.querySelector<HTMLInputElement>('input[name="payment_date"]')?.value,
    payment_method_id: Number(form.querySelector<HTMLSelectElement>('select[name="payment_method"]')?.value),
    status: form.querySelector<HTMLSelectElement>('select[name="status"]')?.value,
    value: Number(form.querySelector<HTMLInputElement>('input[name="value"]')?.value.replace(',', '.')),
  }

  return expense
    ? {...body, id: Number(expense.id)}
    : body
}

type Expense = {
  annotations: string|null,
  supplier: string|null,
  bank_id: number,
  company_id: number,
  due_date: string,
  expense_category_id: number,
  id: number,
  on_financial: boolean,
  payment_date: string|null,
  payment_method_id: number,
  status: 'paid' | 'late' | 'pending'
  value: "109.90",
  type: 'payable' | 'receivable',
}

const STATUS: Record<string, string> = {
  'pending': 'Pendente',
  'paid': 'Pago',
  'late': 'Vencido',
}
