import { useEffect, useState } from "react";
import { Navbar } from "../../../components/Navbar";
import "./style.css"
import api from "../../../services/axios";

export default function InvoicesPage() {
  const [invoices, setInvoices] = useState<Array<EmittedInvoice>>([])
  const [page, setPage] = useState(0)

  useEffect(() => {
    api.get('/api/emitted-invoice')
      .then(response => response.data)
      .then(setInvoices)
  }, [])

  return (
    <div className="emitted-invoices-page">
      <Navbar items={[]}/>
      <div className="emitted-invoices-container">
        <div className="emitted-invoices-content">
          <div className="emitted-invoices-filter-bar">
            <div></div>
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
              <div>Links</div>
            </div>
            {Array.from(invoices).splice(ROWS_PER_PAGE * page, ROWS_PER_PAGE).map(({number, emitted_at, company, link_danfe, link_xml, cancelled}, key) => <div className="emitted-invoices-row" key={key+1}>
              <div>{number}</div>
              <div>{company === 'seline' ? 'S1' : 'B1'}</div>
              <div>{emitted_at ? new Date(emitted_at).toLocaleDateString() : '-'}</div>
              <div>{cancelled ? 'Cancelada' : 'Emitida'}</div>
              <div className="emitted-invoices-row-link-anchors">
                <a href={link_danfe} target="blank">DANFE</a>
                <a href={link_xml} target="blank">XML</a>
              </div>
            </div>)}
          </div>
        </div>
      </div>
    </div>
  )
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
  order_number: number,
  company: 'seline' | 'b1',
  link_danfe?: string,
  link_xml?: string,
  cancelled: boolean,
  cancelment_same_day?: boolean,
}