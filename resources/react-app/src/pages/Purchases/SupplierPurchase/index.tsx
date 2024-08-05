import { useEffect, useState } from "react"
import { Navbar } from "../../../components/Navbar"
import SupplierPurchaseModal from "../../../components/SupplierPurchaseModal"
import "./style.css"
import api from "../../../services/axios"
import getCompany from "../../../lib/getCompany"
import { SupplierPurchase } from "./types"

export default function SupplierPurchasePage() {
  const [modalOpen, setModalOpen] = useState(false)
  const [purchases, setPurchases] = useState([] as Array<SupplierPurchase>)

  useEffect(() => {
    api.get('/api/supplier-purchase')
      .then(response => response.data)
      .then(setPurchases)
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
                    : purchases.map((purchase, key) => <PurchaseRow key={key} purchase={purchase}/>)
                }
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <SupplierPurchaseModal isOpen={modalOpen} setIsOpen={setModalOpen}/>
    </div>
  )
}

function PurchaseRow({purchase}: {purchase: SupplierPurchase}) {
  const [isOpen, setIsOpen] = useState(false)

  return (
    <tr className="supplier-purchase-row" onClick={() => setIsOpen(true)}>
      <td>
        {purchase.id}
        <SupplierPurchaseModal isOpen={isOpen} setIsOpen={setIsOpen} purchase={purchase}/>
      </td>
      <td>{getCompany(purchase.id_company)}</td>
      <td>{purchase.supplier}</td>
      <td>R$ {(purchase.sales_total + purchase.freight).toFixed(2).replace('.', ',')}</td>
    </tr>
  )
}
