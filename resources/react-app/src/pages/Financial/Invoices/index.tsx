import { useEffect, useState } from "react";
import { Navbar } from "../../../components/Navbar";
import "./style.css"
import api from "../../../services/axios";
import { toast, ToastContainer } from "react-toastify";

export default function InvoicesPage() {
  const [invoices, setInvoices] = useState<Array<EmittedInvoice>>([])
  const [page, setPage] = useState(0)
  const [devolutionMode, setDevolutionMode] = useState(false)
  const [cancelMode, setCancelMode] = useState(false)

  useEffect(() => {
    api.get('/api/emitted-invoice')
      .then(response => response.data)
      .then(setInvoices)
  }, [])

  function toogleMode(mode: 'devolution' | 'cancel') {
    if(mode === 'devolution') {
      setCancelMode(false)
      setDevolutionMode(!devolutionMode)

      return
    }

    setDevolutionMode(false)
    setCancelMode(!cancelMode)
  }

  return (
    <div className="emitted-invoices-page">
      <Navbar items={[]}/>
      <div className="emitted-invoices-container">
        <div className="emitted-invoices-content">
          <div className="emitted-invoices-filter-bar">
            <div></div>
            <div className="emitted-invoices-mode-toogle">
              <button className={`emitted-invoices-left ${cancelMode ? "emiited-invoices-default" : "emitted-invoices-cancel"}`} onClick={() => toogleMode('cancel')}>{cancelMode ? 'Visualização de notas' : 'Cancelamento de notas'}</button>
              <button className={`emitted-invoices-right ${devolutionMode ? "emiited-invoices-default" : "emitted-invoices-devolution"}`} onClick={() => toogleMode('devolution')}>{devolutionMode ? 'Visualização de notas' : 'Devolução de notas'}</button>
            </div>
            <div>
              <div>Página</div>
              <select onChange={({target: {value}}) => setPage(Number(value))}>{getPages(invoices)}</select>
            </div>
          </div>
          <div className="emitted-invoices-list">
            <div className="emitted-invoices-row" key={0}>
              <div>Número</div>
              <div>Empresa</div>
              <div>Emitida em</div>
              <div>Status</div>
              <div>{devolutionMode || cancelMode ? 'Ação' : 'Links'}</div>
            </div>
            {Array.from(invoices).splice(ROWS_PER_PAGE * page, ROWS_PER_PAGE).map(({key, number, emitted_at, company, link_danfe, link_xml, cancelled}, index) => <div className="emitted-invoices-row" key={index+1}>
              <div>{number}</div>
              <div>{company === 'seline' ? 'S1' : 'B1'}</div>
              <div>{emitted_at ? new Date(emitted_at).toLocaleDateString() : '-'}</div>
              <div>{cancelled ? 'Cancelada' : 'Emitida'}</div>
              <ActionCell
                mode={devolutionMode ? 'devolution' : (cancelMode ? 'cancel' : 'view')}
                key={key}
                link_danfe={link_danfe}
                link_xml={link_xml}
                emitted_at={emitted_at}
                cancelled={cancelled}
              />
            </div>)}
          </div>
        </div>
      </div>
      <ToastContainer/>
    </div>
  )
}

function ActionCell({mode, key, link_danfe, link_xml, emitted_at, cancelled}: ActionCellProp) {
  if(mode === 'devolution') {
    if(!emitted_at || cancelled) return <></>

    return (
      <div>
        <button
          className="emitted-invoices-devolution emitted-invoices-action-btn"
          onClick={() => {
            api.post('/api/emitted-invoice/devolution', {key})
              .then(response => response.data)
              .then(response => {
                if(response.success) {
                  toast.success('Nota de devolução gerada')

                  return
                }

                toast.error(response.mensagem_sefaz)
              })
          }}
        >Devolução</button>
      </div>
    )
  }

  return (
    <div className="emitted-invoices-row-link-anchors">
      <a href={link_danfe ?? `https://www.fsist.com.br/usuario/api/1/100/${key}.pdf`} target="blank">DANFE</a>
      <a href={link_xml ?? `https://www.fsist.com.br/usuario/api/1/100/${key}.xml`} target="blank">XML</a>
    </div>
    )
}

type ActionCellProp = {
  mode: 'view' | 'devolution' | 'cancel',
  key: string,
  cancelled: boolean,
  emitted_at?: string,
  link_danfe?: string,
  link_xml?: string,
}

const ROWS_PER_PAGE = 20

function getPages(invoices: Array<EmittedInvoice>) {
  const pagesCount = Math.ceil(invoices.length / ROWS_PER_PAGE)
  const pages: Array<JSX.Element> = []

  for(let i = 0; i < pagesCount; i++) {
    pages.push(<option value={i} key={i}>{i+1}</option>)
  }

  return pages
}

type EmittedInvoice = {
  key: string,
  number: string,
  emitted_at: string,
  order_number?: string,
  company: 'seline' | 'b1',
  link_danfe?: string,
  link_xml?: string,
  cancelled: boolean,
  cancelment_same_day?: boolean,
}