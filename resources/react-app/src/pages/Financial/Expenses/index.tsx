import { useEffect, useRef, useState } from "react";
import { Navbar } from "../../../components/Navbar";
import "./style.css"
import api from "../../../services/axios";
import getCompany from "../../../lib/getCompany";
import { toast, ToastContainer } from "react-toastify";
import { Invoice } from "../Match/types";
import { format } from "date-fns";

export default function ExpensesPage() {
  const [expenses, setExpenses] = useState([] as Array<Expense>)
  const [suppliers, setSuppliers] = useState([] as Array<{id: number, name: string}>)
  const [categories, setCategories] = useState({} as Record<number, string>)
  const [banks, setBanks] = useState([] as Array<{
    id_company: number,
    id_bank: number,
    name: string,
    account: string,
    agency: string,
  }>)
  const [paymentMethods, setPaymentMethods] = useState({} as Record<number, string>)
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [rows, setRows] = useState(expenses.length === 0
    ? <>Sem registros...</>
    : expenses.map((expense, key) => <ExpenseRow
      key={key}
      expense={expense}
      expenses={expenses}
      setExpenses={setExpenses}
      categories={categories}
      banks={banks}
      payment_methods={paymentMethods}
      suppliers={suppliers}
    />)
  )

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
      if(String(expense.value)?.toLocaleLowerCase().includes(search)) return true

      return false
    })

    setRows(filtered.map((expense, key) => <ExpenseRow
      key={key}
      expense={expense}
      expenses={expenses}
      setExpenses={setExpenses}
      categories={categories}
      banks={banks}
      payment_methods={paymentMethods}
      suppliers={suppliers}
    />))
  }

  useEffect(() => {
    filterSearch('')
  }, [banks, paymentMethods])

  useEffect(() => {
    api.get('/api/expense')
      .then(response => response.data)
      .then(({expenses, categories, bank, payment_methods, suppliers}) => {
        setExpenses(expenses)
        setCategories(categories)
        setBanks(bank)
        setPaymentMethods(payment_methods)
        setSuppliers(suppliers)

        setRows((expenses as Array<Expense>).map((expense, key) => <ExpenseRow
          key={key}
          expense={expense}
          expenses={expenses}
          setExpenses={setExpenses}
          categories={categories}
          banks={banks}
          payment_methods={paymentMethods}
          suppliers={suppliers}
        />))
      })
  }, [])

  return (
    <div className="expenses-page">
      <Navbar items={[]}/>
      <div className="expenses-page-container">
        <div className="expenses-page-search-container">
          Pesquisa
          <br />
          <input type="text" onChange={({target}) => filterSearch(target.value)}/>
          <button className="expense-page-add-modal" onClick={() => setIsModalOpen(true)}>+</button>
        </div>
        {isModalOpen ? <Modal
          categories={categories}
          banks={banks}
          payment_methods={paymentMethods}
          isOpen={isModalOpen}
          setIsOpen={setIsModalOpen}
          suppliers={suppliers}
        /> : <></>}
        <div className="expenses-page-table-container">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Grupo</th>
                <th>Despesa</th>
                <th>Empresa</th>
                <th>Histórico</th>
                <th>Banco</th>
                <th>Forma</th>
                <th>Vencimento</th>
                <th>Pagamento</th>
                <th>Valor</th>
                <th>Situação</th>
                <th>Match</th>
                <th>Financeiro</th>
              </tr>
            </thead>
            <tbody>{rows}</tbody>
          </table>
        </div>
      </div>
      <ToastContainer/>
      <datalist id="expenses-page-suppliers-datalist">{
        suppliers.map(({name}, key) => <option key={key} value={name}>{name}</option>)
      }</datalist>
    </div>
  )
}

function ExpenseRow({expense, expenses, setExpenses, categories, banks, payment_methods, suppliers}: ExpenseRowProp) {
  const [isModalOpen, setIsModalOpen] = useState(false)

  return (
    <>
      <tr onClick={() => setIsModalOpen(true)}>
        <td>{expense.id}</td>
        <td>{getCompany(expense.company_id)}</td>
        <td>{categories[expense.expense_category_id]}</td>
        <td>Empresa</td>
        <td>{expense.annotations?.slice(0, 30)}{(expense.annotations?.length ?? 0) > 30 ? '...' : ''}</td>
        <td>{banks.find(({id_bank}) => id_bank === expense.bank_id)?.name ?? '--'}</td>
        <td>{payment_methods[expense.payment_method_id] ?? '--'}</td>
        <td>{new Date(expense.due_date).toLocaleDateString()}</td>
        <td>{expense.payment_date ? new Date(expense.payment_date).toLocaleDateString() : '-'}</td>
        <td style={{color: expense.type === 'payable' ? 'red' : 'green'}}>R$ {`${expense.value}`.replace('.', ',')}</td>
        <td>{STATUS[expense.status]}</td>
        <td>{expense.has_match ? 'Sim' : 'Não'}</td>
        <td>{!!expense.on_financial ? 'Sim' : 'Não'}</td>
      </tr>
      <Modal
        isOpen={isModalOpen}
        setIsOpen={setIsModalOpen}
        expense={expense}
        setExpense={(expense: Expense) => {
          expenses.splice(expenses.findIndex(({id}) => id === expense.id), 0, expense)
          setExpenses(expenses)
        }}
        categories={categories}
        banks={banks}
        payment_methods={payment_methods}
        suppliers={suppliers}
      />
    </>
  )
}
type ExpenseRowProp = {
  expense: Expense,
  expenses: Array<Expense>,
  setExpenses: (expenses: Array<Expense>) => void,
  banks: Array<{
    id_company: number,
    id_bank: number,
    name: string,
    account: string,
    agency: string,
  }>,
  categories: Record<number, string>,
  payment_methods: Record<number, string>,
  suppliers: Array<{id: number, name: string}>,
}

const MODAL_SELECTS: Array<{id: string, key: keyof Expense}> = [
  {id: 'company', key: 'company_id'},
  {id: 'expense_category', key: 'expense_category_id'},
  {id: 'bank_account', key: 'bank_id'},
  {id: 'payment_method', key: 'payment_method_id'},
]
function Modal({isOpen, setIsOpen, expense, setExpense, banks, categories, payment_methods, suppliers}: ModalProp) {
  const [modalState, setModalState] = useState({
    id_company: 0,
  })
  const formRef = useRef(null as HTMLFormElement | null)
  const [receipts, setReceipts] = useState<Array<ExpenseDocumentForm>>([])
  const [documents, setDocuments] = useState<Array<ExpenseDocumentForm>>([])
  const [isReceiptModalOpen, setIsReceiptModalOpen] = useState(false)
  const [isDocumentModalOpen, setIsDocumentModalOpen] = useState(false)
  const [invoiceMatches, setInvoiceMatches] = useState<Array<{key: string, match: boolean}>>([])
  const [fetchedInvoice, setFetchedInvoice] = useState<Invoice|null>(null)

  function save() {
    if(!formRef.current) return
    const form = formRef.current
    const toSave = parseForm(form, expense)
    const body = {
      expense: toSave,
      receipts: receipts.map(({file, ...receipt}) => ({...receipt, file: file?.split(',')[1] ?? null})),
      documents: documents.map(({file, ...document}) => ({...document, file: file?.split(',')[1] ?? null})),
      matches: fetchedInvoice
        ? [...invoiceMatches, {key: fetchedInvoice.key, match: true}]
        : invoiceMatches
    }

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

    setTimeout(() => MODAL_SELECTS.forEach(({id, key}) => {
      const select = form.querySelector(`select[name="${id}"]`) as HTMLSelectElement

      select.value = (expense[key] ?? '') as string
    }), 1)
  }, [])

  return (
    <div className={`expense-page-modal ${isOpen ? '' : 'expense-page-modal-is-close'}`}>
      <div className="expense-page-modal-is-close-container">
        <div className="expense-page-modal-is-close-close" onClick={() => setTimeout(() => setIsOpen(false), 1)}>X</div>
        <div className="expense-page-modal-save" onClick={save}>Salvar</div>
        <div className="supplier-purchase-purchase-id">{expense?.id ? `Conta Nº ${expense.id}` : ''}</div>
        <form ref={formRef} className="expense-page-modal-form">
          <div className="expense-page-modal-is-close-form-supplier">
            <label htmlFor="company">Grupo: </label>
            <select
              name="company"
              defaultValue={expense?.company_id}
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
                <input type="date" name="due_date" defaultValue={expense?.due_date ?? ''}/>
                <br />
                <label htmlFor="status">Status do pedido:</label>
                <select name="status" defaultValue={expense?.status ?? 'pending'}>
                  <option value="pending">Pendente</option>
                  <option value="paid">Pago</option>
                  <option value="late">Atrasado</option>
                </select>
              </div>
              <div>
                <label htmlFor="expense_type">Tipo</label>
                <select name="expense_type" defaultValue={expense?.type}>
                  <option value="payable">A pagar</option>
                  <option value="receivable">A receber</option>
                </select>
                <br/>
                <label htmlFor="expense_category">Despesa: </label>
                <select name="expense_category" defaultValue={expense?.expense_category_id ?? ''}>
                  <option value="" key={0}>Selecione</option>
                  {Object.keys(categories).map((key, index) => 
                    <option key={index+1} value={key}>{categories[Number(key)]}</option>
                  )}
                </select>
                <br />
                <label htmlFor="supplier">Empresa: </label>
                <input type="text" name="supplier" list="expenses-page-suppliers-datalist" defaultValue={expense?.supplier ?? ''}/>
              </div>
            </div>
            <hr />
            <br />
            <div className="supplier-purchase-split-container">
              <div>
                <label htmlFor="value">Valor: </label>
                <input name="value" defaultValue={expense ? String(expense.value).replace('.', ',') : 0}/>
                <br />
                <label htmlFor="annotations">Histórico: </label>
                <textarea name="annotations" defaultValue={expense?.annotations ?? ''} rows={6} style={{width: '95%'}}/>
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
                <input type="date" name="payment_date" defaultValue={expense?.payment_date ?? ''}/>
              </div>
            </div>
            {expense && expense?.invoices.length > 0
              ? <>
                <hr />
                <p>Análise das despesas</p>
                <div className="supplier-purchase-full-container">
                  <div>Despesa: R$ {expense.value.toFixed(2).replace('.', ',')}</div>
                  <div>Nota(s): R$ {expense.invoices.map(({value}) => value).reduce((acc, cur) => acc + cur, 0).toFixed(2).replace('.', ',')}</div>
                  <div>Diferença: R$ {(expense.invoices.map(({value}) => value).reduce((acc, cur) => acc + cur, 0) - expense.value).toFixed(2).replace('.', ',')}</div>
                </div>
                <hr />
              </>
              : <></>}
            {expense?.supplier_purchase_id
              ? <>
                <p>Notas associadas</p>
                <table>
                  <thead>
                    <tr>
                      <th>Chave</th>
                      <th>Emitente</th>
                      <th>Valor</th>
                      <th>Arquivos</th>
                      <th>Emissão</th>
                      <th>Ações</th>
                    </tr>
                  </thead>
                  <tbody>{
                    ([...expense.invoices, fetchedInvoice]
                      .filter(invoice => !!invoice && invoiceMatches.findIndex(({key, match}) => !match && invoice.key === key) === -1) as Array<Invoice>)
                      .map(({key, value, emitted_at, emitter: {name}}, index) => <tr key={index}>
                        <td>{key}</td>
                        <td title={name}>{name?.substring(0, 30) + ((name?.length ?? 0) > 30 ? '...' : '')}</td>
                        <td>R$ {value.toFixed(2).replace('.', ',')}</td>
                        <td className="supplier-purchase-invoice-buttons-container">
                          <div onClick={() => window.open(`https://www.fsist.com.br/usuario/api/1/100/${key}.pdf`, 'blank')}>DANFE</div>
                          <div onClick={() => window.open(`https://www.fsist.com.br/usuario/api/1/100/${key}.xml`, 'blank')}>XML</div>
                        </td>
                        <td>{new Date(emitted_at).toLocaleDateString()}</td>
                        <td className="supplier-purchase-invoice-buttons-container">
                          <div
                            onClick={() => setInvoiceMatches([...invoiceMatches, ...expense.invoices.filter(({key: invoiceKey}) => invoiceKey !== key).map(({key}) => ({
                              key,
                              match: false,
                            }))])}
                          >Sobrescrever</div>
                          <div
                            onClick={() => setInvoiceMatches([...invoiceMatches, {
                              key,
                              match: false,
                            }])}
                          >Apagar</div>
                        </td>
                      </tr>
                    )
                  }</tbody>
                </table>
                <hr />
                <div className="expense-page-modal-match-container">
                  <div>Sobrescrever match de nota fiscal (insira abaixo a chave de acesso)</div>
                  <div>
                    <input type="text" />
                    <div
                      onClick={({target}) => {
                        const {value} = (target as HTMLDivElement).previousSibling as HTMLInputElement
                        const toastId = toast.loading('Pesquisando nota...')
                        
                        api.get('/api/invoice?key=' + value)
                          .then(response => {
                            toast.dismiss(toastId)
                            return response.data as Invoice|null
                          })
                          .then(invoice => {
                            if(!invoice) {
                              toast.error('Nota não encontrada...')
                              return
                            }
                            const invoiceMatched = invoiceMatches.find(({key}) => key === invoice.key)
                            if(invoiceMatched) {
                              if(!invoiceMatched.match) {
                                setInvoiceMatches(invoiceMatches.filter(({key}) => key !== invoice.key))
                              }
                              
                              return
                            }

                            setFetchedInvoice(invoice)
                          })
                      }}
                    >Sobrescrever</div>
                  </div>
                </div>
                <hr />
              </>
              : <></>}
            <div className="supplier-purchase-split-container">
              <div className="supplier-purchase-documents-container">
                <div>
                  <span>Recibos</span>
                  <button onClick={event => {
                    event.preventDefault()
                    setIsReceiptModalOpen(true)
                    formRef.current?.parentElement?.scrollTo(0, 0)
                  }}>+</button>
                  <DocumentModal
                    isOpen={isReceiptModalOpen}
                    setIsOpen={setIsReceiptModalOpen}
                    pushDocument={receipt => setReceipts([...receipts, receipt])}
                    receiptModal={true}
                  />
                </div>
                <table>
                  <thead>
                    <tr>
                      <th>Data</th>
                      <th>Recibo de pagamento</th>
                      <th>Valor</th>
                      <th>Ações</th>
                    </tr>
                  </thead>
                  <tbody>{
                    <>
                      {(expense && expense.receipts.length > 0)
                        ? expense.receipts.map(({created_at, key, value, filename}, index) => <tr key={index}>
                          <td>{new Date(created_at).toLocaleDateString()}</td>
                          <td>{key}</td>
                          <td>R$ {Number(value).toFixed(2).replace('.', ',')}</td>
                          <td>
                            {filename && <button
                                onClick={(event) => {
                                  event.preventDefault()
                                  window.open(`/storage/documents/${filename}`, 'blank')
                                }}
                              >Abrir</button>}
                              <button
                                onClick={event => {
                                  event.preventDefault()
                                  const toDelete = expense.receipts.splice(index, 1)[0]
                                  receipts.push({
                                    key: toDelete.key,
                                    type: '',
                                    value: 0,
                                    created_at: '',
                                    delete: true,
                                  })
                                  setExpense && setExpense(expense)
                                  setReceipts(receipts)
                                }}
                              >Apagar</button>
                          </td>
                        </tr>)
                        : <></>}
                      {(receipts.length > 0)
                        ? receipts.map(({created_at, key, value, file, delete: del}, index) => del
                          ? <></>
                          : <tr key={index}>
                            <td>{new Date(created_at).toLocaleDateString()}</td>
                            <td>{key}</td>
                            <td>R$ {value.toFixed(2).replace('.', ',')}</td>
                            <td>
                              {file && <button
                                onClick={(event) => {
                                  event.preventDefault()
                                  window.open(file, 'blank')
                                }}
                              >Abrir</button>}
                              <button
                                onClick={event => {
                                  event.preventDefault()
                                  setReceipts(receipts.filter((_, i) => i !== index))
                                }}
                              >Apagar</button>
                            </td>
                          </tr>
                        )
                        : <></>}
                      {expense?.receipts.length === 0 && receipts.length === 0
                        ? <tr><td></td><td>Sem recibos</td></tr>
                        : <></>}
                    </>
                  }</tbody>
                </table>
              </div>
              <div className="supplier-purchase-documents-container">
                <div>
                  <span>Recibos</span>
                  <button onClick={event => {
                    event.preventDefault()
                    setIsDocumentModalOpen(true)
                    formRef.current?.parentElement?.scrollTo(0, 0)
                  }}>+</button>
                  <DocumentModal
                    isOpen={isDocumentModalOpen}
                    setIsOpen={setIsDocumentModalOpen}
                    pushDocument={document => setDocuments([...documents, document])}
                    receiptModal={false}
                  />
                </div>
                <table>
                  <thead>
                    <tr>
                      <th>Data</th>
                      <th>Tipo de documento</th>
                      <th>Chave/Identificador</th>
                      <th>Emissor</th>
                      <th>Valor</th>
                      <th>Ações</th>
                    </tr>
                  </thead>
                  <tbody>{
                    <>
                      {(expense && expense.documents.length > 0)
                        ? expense.documents.map(({created_at, key, value, filename}, index) => <tr key={index}>
                          <td>{new Date(created_at).toLocaleDateString()}</td>
                          <td>{key}</td>
                          <td>R$ {value.toFixed(2).replace('.', ',')}</td>
                          <td>
                            {filename && <button
                                onClick={(event) => {
                                  event.preventDefault()
                                  window.open(`/storage/documents/${filename}`, 'blank')
                                }}
                              >Abrir</button>}
                              <button
                                onClick={event => {
                                  event.preventDefault()
                                  const toDelete = expense.documents.splice(index, 1)[0]
                                  documents.push({
                                    key: toDelete.key,
                                    type: '',
                                    value: 0,
                                    created_at: '',
                                    delete: true,
                                  })
                                  setExpense && setExpense(expense)
                                  setDocuments(documents)
                                }}
                              >Apagar</button>
                          </td>
                        </tr>)
                        : <></>}
                      {(documents.length > 0)
                        ? documents.map(({created_at, type, key, issuer, value, file, delete: del}, index) => del
                          ? <></>
                          : <tr key={index}>
                            <td>{new Date(created_at).toLocaleDateString()}</td>
                            <td>{type}</td>
                            <td>{key}</td>
                            <td>{issuer}</td>
                            <td>R$ {value.toFixed(2).replace('.', ',')}</td>
                            <td>
                              {file && <button
                                onClick={(event) => {
                                  event.preventDefault()
                                  window.open(file, 'blank')
                                }}
                              >Abrir</button>}
                              <button
                                onClick={event => {
                                  event.preventDefault()
                                  setDocuments(documents.filter((_, i) => i !== index))
                                }}
                              >Apagar</button>
                            </td>
                          </tr>
                        )
                        : <></>}
                      {expense?.documents.length === 0 && documents.length === 0
                        ? <tr><td></td><td>Sem documentos</td></tr>
                        : <></>}
                    </>
                  }</tbody>
                </table>
              </div>
            </div>
          </div>
        </form>
        <ToastContainer/>
      </div>
    </div>
  )
}

function DocumentModal({isOpen, setIsOpen, pushDocument, receiptModal}: DocumentModalProp) {
  const modalFormRef = useRef<HTMLDivElement>(null)

  return (
    <div className={`expense-document-modal ${isOpen ? '' : 'expense-document-modal-is-close'}`}>
      <div ref={modalFormRef} className="expense-document-modal-container">
        <div className="expense-document-modal-close-container">
          <span
            onClick={() => {
              setIsOpen(false)
              modalFormRef.current && clearModalForm(modalFormRef.current)
            }}
          >X</span>
        </div>
        <div className="expense-document-modal-content">
          <div className="expense-document-modal-title">Adicionar {receiptModal ? 'recibo' : 'documento'}</div>
          <div className="expense-document-modal-input">
            <label htmlFor="expense-document-date">Data: </label>
            <input type="date" name="expense-document-date" defaultValue={format(new Date(), 'yyyy-MM-dd')}/>
          </div>
          {!receiptModal && <div className="expense-document-modal-input">
            <label htmlFor="expense-document-type">Tipo: </label>
            <select name="expense-document-type">
              <option value="fatura">Fatura</option>
              <option value="boleto">Boleto</option>
              <option value="nfs">NFS</option>
              <option value="cupom">Cupom Fiscal</option>
              <option value="outros">Outros</option>
            </select>
          </div>}
          <div className="expense-document-modal-input">
            <label htmlFor="expense-document-key">Identificador: </label>
            <input type="text" name="expense-document-key"/>
          </div>
          {!receiptModal && <div className="expense-document-modal-input">
            <label htmlFor="expense-document-issuer">Emissor: </label>
            <input type="text" name="expense-document-issuer"/>
          </div>}
          <div className="expense-document-modal-input">
            <label htmlFor="expense-document-value">Valor: R$</label>
            <input type="number" name="expense-document-value" defaultValue={0}/>
          </div>
          <div className="expense-document-modal-input">
            <label htmlFor="expense-document-file">Arquivo: </label>
            <input type="file" name="expense-document-file"/>
          </div>
        </div>
        <div className="expense-document-modal-save-container">
          <button
            onClick={async(event) => {
              event.preventDefault()
              if(!modalFormRef.current) return

              pushDocument(await parseDocumentModalForm(modalFormRef.current, receiptModal))
              setIsOpen(false)
              clearModalForm(modalFormRef.current)
            }}
          >Adicionar</button>
        </div>
      </div>
    </div>
  )
}

async function parseDocumentModalForm(modalForm: HTMLDivElement, receiptModal: boolean): Promise<ExpenseDocumentForm> {
  const [file, extension] = await getFileAndExtension(modalForm.querySelector<HTMLInputElement>('input[name="expense-document-file"]'))
  const dateInput = modalForm.querySelector<HTMLInputElement>('input[name="expense-document-date"]')
  const body = {
    created_at: format(new Date(dateInput ? `${dateInput.value} 00:00:00` : new Date()), 'yyyy-MM-dd'),
    key: modalForm.querySelector<HTMLInputElement>('input[name="expense-document-key"]')?.value ?? '',
    value: Number(modalForm.querySelector<HTMLInputElement>('input[name="expense-document-value"]')?.value.replace(',', '.') ?? 0),
    file,
    extension,
  }

  return receiptModal
    ? {
      ...body,
      type: 'recibo',
    }
    : {
      ...body,
      type: modalForm.querySelector<HTMLInputElement>('select[name="expense-document-type"]')?.value ?? '',
      issuer: modalForm.querySelector<HTMLInputElement>('input[name="expense-document-issuer"]')?.value ?? '',
    }
}

async function getFileAndExtension(fileInput: HTMLInputElement | null) {
  if(!fileInput || !fileInput.files || fileInput.files.length === 0) return [null, null]
  const file = fileInput.files[0]

  return [await toBase64(file), /(?:\.([^.]+))?$/.exec(file.name)?.[1]]
}

async function toBase64(file: File): Promise<string> {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()

    reader.readAsDataURL(file)
    reader.onload = () => resolve(reader.result as string)
    reader.onerror = reject
  })
}

function clearModalForm(modalForm: HTMLDivElement) {
  modalForm.querySelectorAll('input').forEach(input => {
    if(input.name.includes('date')) {
      input.value = format(new Date(), 'yyyy-MM-dd')
      return
    }
    if(input.name.includes('value')) {
      input.value = '0'
      return
    }

    input.value = ''
  })
}

type DocumentModalProp = {
  isOpen: boolean,
  setIsOpen: (isOpen: boolean) => void,
  pushDocument: (document: ExpenseDocumentForm) => void,
  receiptModal: boolean,
}

type ModalProp = {
  isOpen: boolean,
  setIsOpen: (isOpen: boolean) => void,
  expense?: Expense,
  setExpense?: (expense: Expense) => void,
  banks: Array<{
    id_company: number,
    id_bank: number,
    name: string,
    account: string,
    agency: string,
  }>,
  categories: Record<number, string>,
  payment_methods: Record<number, string>,
  suppliers: Array<{id: number, name: string}>,
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
    type: form.querySelector<HTMLSelectElement>('select[name="expense_type"]')?.value,
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
  has_match: boolean,
  payment_date: string|null,
  payment_method_id: number,
  status: 'paid' | 'late' | 'pending'
  value: number,
  type: 'payable' | 'receivable',
  supplier_purchase_id?: number,
  invoices: Array<Invoice>,
  receipts: Array<ExpenseDocument>,
  documents: Array<ExpenseDocument>,
}

type ExpenseDocument = {
  key: string,
  created_at: string,
  type: string,
  issuer?: string,
  value: number,
  filename?: string,
  expense_id?: number,
}

type ExpenseDocumentForm = {
  key: string,
  created_at: string,
  type: string,
  issuer?: string,
  value: number,
  file?: string | null,
  extension?: string | null,
  delete?: boolean,
}

const STATUS: Record<string, string> = {
  'pending': 'Pendente',
  'paid': 'Pago',
  'late': 'Vencido',
}
