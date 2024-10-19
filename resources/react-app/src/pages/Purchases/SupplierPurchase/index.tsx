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
  const [{payment_methods, bank_accounts, delivery_addresses, supplier_delivery_methods}, setModalInfo] = useState({} as ModalInfo)

  useEffect(() => {
    api.get('/api/supplier-purchase/modal-info')
      .then(response => response.data)
      .then(({payment_methods, bank_accounts, delivery_addresses, supplier_delivery_methods}) => {
        setModalInfo({
          payment_methods: (payment_methods as Array<{id: number, operation: string}>)
            .map(({id, operation}, key) => <option key={key+1} value={id}>{operation}</option>),
          bank_accounts,
          delivery_addresses,
          supplier_delivery_methods,
        })
      })

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
                  purchases.length === 0 || !bank_accounts
                    ? <tr><td>Sem pedidos</td></tr>
                    : purchases.map((purchase, key) => <PurchaseRow
                      key={key}
                      purchase={purchase}
                      paymentMethods={payment_methods}
                      bankAccounts={bank_accounts}
                      supplierDeliveryMethods={supplier_delivery_methods}
                      deliveryAddresses={delivery_addresses}
                    />)
                }
              </tbody>
            </table>
          </div>
        </div>
      </div>
      {
        !bank_accounts
          ? <></>
          : <SupplierPurchaseModal
            isOpen={modalOpen}
            setIsOpen={setModalOpen}
            paymentMethods={payment_methods}
            bankAccounts={bank_accounts}
            supplierDeliveryMethods={supplier_delivery_methods}
            deliveryAddresses={delivery_addresses}
          />
      }
    </div>
  )
}

type ModalInfo = {
  payment_methods: Array<JSX.Element>,
  bank_accounts: Array<BankAccount>,
  delivery_addresses: Array<{id: number, name: string}>,
  supplier_delivery_methods: Array<{id:number, name: string}>,
}

function PurchaseRow({purchase, paymentMethods, bankAccounts, supplierDeliveryMethods, deliveryAddresses}: {purchase: SupplierPurchase, paymentMethods: Array<JSX.Element>, bankAccounts: Array<BankAccount>, supplierDeliveryMethods: Array<{id: number, name: string}>, deliveryAddresses: Array<{id: number, name: string}>}) {
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
          supplierDeliveryMethods={supplierDeliveryMethods}
          deliveryAddresses={deliveryAddresses}
        />
      </td>
      <td>{getCompany(purchase.id_company)}</td>
      <td>{purchase.supplier}</td>
      <td>R$ {(purchase.sales_total + purchase.freight).toFixed(2).replace('.', ',')}</td>
    </tr>
  )
}
