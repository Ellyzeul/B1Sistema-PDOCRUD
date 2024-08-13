import { useCallback, useContext, useEffect, useState } from "react"
import "./style.css"
import SupplierPurchaseItemRowContext from "../../../contexts/SupplierPurchaseItemRow"
import api from "../../../services/axios"
import { toast } from "react-toastify"
import getCurrencyFromSellercentral from "../../../lib/getCurrencyFromSellercentral"
import getCurrentCurrencyCotation from "../../../lib/getCurrencyCotation"
import { SupplierPurchaseItem } from "../../../pages/Purchases/SupplierPurchase/types"

export default function SupplierPurchaseItemRow({id, item}: Prop) {
  const {tableRows, setTableRows} = useContext(SupplierPurchaseItemRowContext)
  const [orderDetails, setOrderDetails] = useState(null as OrderDetails | null)
  const [previousDetails, setPreviousDetails] = useState(null as OrderDetails | null)
  const currency = getCurrencyFromSellercentral(orderDetails?.id_sellercentral || 0)
  const fetchOrderDetails = useCallback((orderId: string) => {
    api.get('/api/supplier-purchase/order-details?id_order=' + orderId)
      .then(response => response.data as OrderDetails)
      .then(response => {
        setPreviousDetails(orderDetails)
        setOrderDetails(response)
      })
      .catch(err => {
        const errMsg = err.response.data.err_msg

        toast.error(errMsg || 'Algum erro ocorreu...')
        setPreviousDetails(orderDetails)
        setOrderDetails(null)
      })
  }, [setPreviousDetails, setOrderDetails, orderDetails])

  function deleteRow() {
    if(tableRows.length === 1) return

    setTableRows(tableRows.filter(row => Number(row.key) !== id))
  }
  
  function formatValue(input: HTMLInputElement) {
    input.value = Number(input.value.replace(',', '.'))
      .toFixed(2)
      .replace('.', ',')
  }

  useEffect(() => {
    if(!orderDetails && item) {
      fetchOrderDetails(String(item.id_order))
    }
    if(previousDetails?.id === orderDetails?.id) return

    (async() => {
      setPreviousDetails(orderDetails)
      setOrderDetails({
        ...(orderDetails as OrderDetails), 
        brlPrice: await getBRLPrice(orderDetails?.selling_price, currency?.code),
      })
    })()
  }, [orderDetails, item, previousDetails, currency, fetchOrderDetails])

  return (
    <tr className="supplier-purchase-item-row">
      <td><input type="text" name="id_order" defaultValue={item?.id_order || ''} onBlur={(ev) => fetchOrderDetails(ev.target.value)}/></td>
      <td><select name="date" defaultValue={item?.status || 'pending'}>
        <option value="pending">Pendente</option>
        <option value="delivered">Entregue</option>
        <option value="cancelled">Cancelado</option>
        <option value="failed">Fornecedor furou</option>
      </select></td>
      <td>{orderDetails?.isbn}</td>
      <td>{currency?.name}</td>
      <td>{currency?.symbol} {(orderDetails?.selling_price || '').toString().replace('.', ',')}</td>
      <td>{orderDetails?.brlPrice && `R$ ${orderDetails?.brlPrice}`}</td>
      <td>
        <input
          type="text"
          name="value"
          defaultValue={item?.value.toString().replace('.', ',') ?? ''}
          onBlur={({target}) => formatValue(target)}
        />
      </td>
      <td>
        <i
          className={`fa-solid fa-trash ${tableRows.length === 1 ? 'row-delete-disable' : ''}`}
          onClick={deleteRow}
        />
        <input type="text" name="item_id" defaultValue={item?.id} style={{display: 'none'}}/>
      </td>
    </tr>
  )
}

type Prop = {
  id: number,
  item?: SupplierPurchaseItem,
}

type OrderDetails = {
  id: number,
  isbn: string,
  id_sellercentral: number,
  selling_price: number,
  brlPrice?: string,
}

async function getBRLPrice(price?: number, currencyCode?: string) {
  if(!price || !currencyCode) return undefined
  if(currencyCode === 'BRL') return String(price).replace('.', ',')

  const cotation = await getCurrentCurrencyCotation(currencyCode)
  if(cotation === -1) return 'Erro na cotação'

  return (price * cotation).toFixed(2).replace('.', ',')
}
