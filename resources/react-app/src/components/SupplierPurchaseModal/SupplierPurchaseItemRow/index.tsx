import { useCallback, useContext, useEffect, useRef, useState } from "react"
import "./style.css"
import SupplierPurchaseItemRowContext from "../../../contexts/SupplierPurchaseItemRow"
import api from "../../../services/axios"
import { toast } from "react-toastify"
import getCurrencyFromSellercentral from "../../../lib/getCurrencyFromSellercentral"
import getCurrentCurrencyCotation from "../../../lib/getCurrencyCotation"
import { SupplierPurchaseItem } from "../../../pages/Purchases/SupplierPurchase/types"
import getBRLPrice from "../../../lib/getBRLPrice"

export default function SupplierPurchaseItemRow({id, item}: Prop) {
  const {tableRows, setTableRows, modalState, setModalState} = useContext(SupplierPurchaseItemRowContext)
  const [orderDetails, setOrderDetails] = useState(null as OrderDetails | null)
  const [previousDetails, setPreviousDetails] = useState(null as OrderDetails | null)
  const currency = getCurrencyFromSellercentral(orderDetails?.id_sellercentral || 0)
  const rowRef = useRef(null as HTMLTableRowElement | null)
  const fetchOrderDetails = useCallback((orderId: string, purchaseId?: number) => {
    api.get(`/api/supplier-purchase/order-details?id_order=${orderId}&id_purchase=${purchaseId ?? ''}`)
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
  
  function handleBlur(input: HTMLInputElement) {
    setModalState({...modalState, items: {...modalState.items, [id]: Number(input.value.replace(',', '.'))}})
    input.value = Number(input.value.replace(',', '.'))
      .toFixed(2)
      .replace('.', ',')
  }

  useEffect(() => {
    if(!orderDetails && item) {
      fetchOrderDetails(String(item.id_order), item.id_purchase)
    }
    if(previousDetails?.id === orderDetails?.id) return

    (async() => {
      const brlPrice = await getBRLPrice(orderDetails?.selling_price, currency?.code) as string

      setModalState({
        ...modalState,
        selling_price: {
          ...modalState.selling_price,
          [id]: Number(brlPrice?.replace(',', '.') ?? 0)
        }
      })
      setPreviousDetails(orderDetails)
      setOrderDetails({
        ...(orderDetails as OrderDetails), 
        brlPrice,
      })

      const radio = rowRef.current?.querySelector<HTMLInputElement>(
        `input[name='item_status'][value='${item?.status ?? 'pending'}']`
      )
      if(radio) radio.checked = true
    })()
  }, [orderDetails, item, previousDetails, currency, fetchOrderDetails])

  return (
    <tr className="supplier-purchase-item-row" ref={rowRef}>
      <td><input type="text" name="id_order" defaultValue={item?.id_order || ''} onBlur={(ev) => fetchOrderDetails(ev.target.value, item?.id_purchase)}/></td>
      <td>
      <form>
        <input className="supplier-purchase-item-status" defaultChecked={item?.status === 'pending'} title="Pendente" type="radio" name="item_status" value='pending'/>
        <input className="supplier-purchase-item-status" defaultChecked={item?.status === 'delivered'} title="Entregue" type="radio" name="item_status" value='delivered'/>
        <input className="supplier-purchase-item-status" defaultChecked={item?.status === 'failed'} title="Não entregue" type="radio" name="item_status" value='failed'/>
        <input className="supplier-purchase-item-status" defaultChecked={item?.status === 'cancelled'} title="Cancelado" type="radio" name="item_status" value='cancelled'/>
      </form>
      </td>
      <td>{orderDetails?.isbn}</td>
      <td>{currency?.name}</td>
      <td>{currency?.symbol} {(orderDetails?.selling_price || '').toString().replace('.', ',')}</td>
      <td>{orderDetails?.brlPrice && `R$ ${orderDetails?.brlPrice}`}</td>
      <td>
        <input
          type="text"
          name="value"
          defaultValue={item?.value.toString().replace('.', ',') ?? ''}
          onBlur={({target}) => handleBlur(target)}
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


