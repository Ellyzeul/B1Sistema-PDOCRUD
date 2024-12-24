import { useEffect, useState } from "react"
import { Navbar } from "../../../components/Navbar"
import SupplierPurchaseModal from "../../../components/SupplierPurchaseModal"
import "./style.css"
import api from "../../../services/axios"
import getCompany from "../../../lib/getCompany"
import { BankAccount, SupplierPurchase } from "./types"
import CostBenefitIndex from "../../../components/SupplierPurchaseModal/CostBenefitIndex"
import getBRLPrice from "../../../lib/getBRLPrice"
import getCurrencyFromSellercentral from "../../../lib/getCurrencyFromSellercentral"

export default function SupplierPurchasePage() {
  const [modalOpen, setModalOpen] = useState(false)
  const [purchases, setPurchases] = useState([] as Array<SupplierPurchase>)
  const [suppliers, setSuppliers] = useState([] as Array<{id: number, name: string}>)
  const [{payment_methods, bank_accounts, delivery_addresses, supplier_delivery_methods}, setModalInfo] = useState({} as ModalInfo)

  useEffect(() => {
    api.get('/api/supplier-purchase/modal-info')
      .then(response => response.data)
      .then(({payment_methods, bank_accounts, delivery_addresses, supplier_delivery_methods, suppliers}) => {
        setModalInfo({
          payment_methods: (payment_methods as Array<{id: number, operation: string}>)
            .map(({id, operation}, key) => <option key={key+1} value={id}>{operation}</option>),
          bank_accounts,
          delivery_addresses,
          supplier_delivery_methods,
        })
        setSuppliers(suppliers)
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
                  <th>Data compra</th>
                  <th>Data pagamento</th>
                  <th>Fornecedor</th>
                  <th>Status</th>
                  <th>Itens</th>
                  <th>Entrega</th>
                  <th>Nº Pedido</th>
                  <th>Transportadora</th>
                  <th>Rastreio</th>
                  <th>Valor total</th>
                  <th>Margem</th>
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
      <datalist id="supplier-purchase-page-supplier-list">{
        suppliers.map(({name}) => <option value={name}>{name}</option>)
      }</datalist>
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
  const [sellingPrice, setSellingPrice] = useState({} as Record<number, number>)
  console.log(sellingPrice)

  useEffect(() => {(async () => {
    const sellingPrice: Record<number, number> = {}
    let id = 1

    for (const {order: {id_sellercentral, selling_price}} of purchase.items) {
      sellingPrice[id++] = await getBRLPrice(selling_price, getCurrencyFromSellercentral(id_sellercentral)?.code, false) as number
    }

    setSellingPrice(sellingPrice)
  })()}, [])

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
      <td>{new Date(`${purchase.date} 00:00:00`).toLocaleDateString()}</td>
      <td>{new Date(`${purchase.payment_date} 00:00:00`).toLocaleDateString()}</td>
      <td>{purchase.supplier}</td>
      <td>{STATUSES[purchase.status]}</td>
      <td>{purchase.items.length}</td>
      <td>{purchase.delivery_address}</td>
      <td>{purchase.order_number}</td>
      <td>{purchase.delivery_method}</td>
      <td>{purchase.tracking_code}</td>
      <td>R$ {(purchase.sales_total + purchase.freight).toFixed(2).replace('.', ',')}</td>
      <td>
        <CostBenefitIndex modalState={{
          items: purchase.items.map(({value}, id) => [id+1, Number(value)]).reduce((acc, [id, value]) => ({...acc, [id]: value}), {}) ?? {},
          freight: purchase.freight ?? 0,
          selling_price: sellingPrice,
          id_company: purchase.id_company ?? 0,
          hide_text: true
        }}/>
      </td>
    </tr>
  )
}

const STATUSES: Record<string, string> = {
  'normal': 'Normal',
  'cancelled': 'Cancelado',
  'cancelled_partial': 'Cancelado parcial',
  'multiple_delivery': 'Múltiplas entregas',
}
