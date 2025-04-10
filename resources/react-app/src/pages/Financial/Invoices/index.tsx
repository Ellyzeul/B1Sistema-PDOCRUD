import { Navbar } from "../../../components/Navbar";
import "./style.css"

export default function InvoicesPage() {
  return (
    <div className="emitted-invoices-page">
      <Navbar items={[]}/>
      <div className="emitted-invoices-container">
        <div className="emitted-invoices-content"></div>
      </div>
    </div>
  )
}