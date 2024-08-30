import { useEffect, useState } from "react"
import { Navbar } from "../../../components/Navbar"
import SupplierPurchaseModal from "../../../components/SupplierPurchaseModal"
import "./style.css"
import api from "../../../services/axios"
import getCompany from "../../../lib/getCompany"
import { BankAccount, SupplierPurchase } from "./types"

export default function SupplierPurchasePage() {
  const [modalOpen, setModalOpen] = useState(false)
  const [purchases, setPurchases] = useState([] as Array<SupplierPurchase>)
  const [paymentMethods, setPaymentMethods] = useState([] as Array<JSX.Element>)
  const [bankAccounts, setBankAccounts] = useState([] as Array<BankAccount>)

  useEffect(() => {
    api.get('/api/supplier-purchase')
      .then(response => response.data)
      .then(setPurchases)
    
    api.get('/api/payment-method')
      .then(response => response.data as Array<{id: number, operation: string}>)
      .then(paymentMethods => setPaymentMethods(paymentMethods
        .map(({id, operation}, key) => <option key={key+1} value={id}>{operation}</option>)
      ))
    
    api.get('/api/company/bank-accounts')
      .then(response => response.data)
      .then(setBankAccounts)
  }, [])

  return (
    <div className="supplier-purchase-page">
      <Navbar items={[]}/>
      <div className="supplier-purchase-page-container">
        <div className="supplier-purchase-table">
          <div className="supplier-purchase-control">
            <div className="supplier-purchase-search-box">
              <input type="text" />
              <button>Pesquisar</button>
            </div>
            <div className="supplier-purchase-buttons">
              <div onClick={() => setModalOpen(true)}>+</div>
            </div>
          </div>
          <div className="supplier-purchase-table-element-container">
            <table className="supplier-purchase-table-element">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Empresa</th>
                  <th>Fornecedor</th>
                  <th>Valor total</th>
                </tr>
              </thead>
              <tbody>
                {
                  purchases.length === 0
                    ? <tr><td>Sem pedidos</td></tr>
                    : purchases.map((purchase, key) => <PurchaseRow
                      key={key}
                      purchase={purchase}
                      paymentMethods={paymentMethods}
                      bankAccounts={bankAccounts}
                    />)
                }
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <SupplierPurchaseModal
        isOpen={modalOpen}
        setIsOpen={setModalOpen}
        paymentMethods={paymentMethods}
        bankAccounts={bankAccounts}
      />
    </div>
  )
}

function PurchaseRow({purchase, paymentMethods, bankAccounts}: {purchase: SupplierPurchase, paymentMethods: Array<JSX.Element>, bankAccounts: Array<BankAccount>}) {
  const [isOpen, setIsOpen] = useState(false)

  return (
    <tr className="supplier-purchase-row" onClick={() => setIsOpen(true)}>
      <td>
        {purchase.id}
        <SupplierPurchaseModal
          isOpen={isOpen}
          setIsOpen={setIsOpen}
          purchase={purchase}
          paymentMethods={paymentMethods}
          bankAccounts={bankAccounts}
        />
      </td>
      <td>{getCompany(purchase.id_company)}</td>
      <td>{purchase.supplier}</td>
      <td>R$ {(purchase.sales_total + purchase.freight).toFixed(2).replace('.', ',')}</td>
    </tr>
  )
}
