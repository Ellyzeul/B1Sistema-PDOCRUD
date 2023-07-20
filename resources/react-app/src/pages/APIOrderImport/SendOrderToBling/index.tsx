import { useRef, useState } from "react"
import { toast, ToastContainer } from "react-toastify"
import { Navbar } from "../../../components/Navbar"
import { SendOrderToBlingTable } from "../../../components/SendOrderToBlingTable";
import api from "../../../services/axios";
// import { orderControlData } from "./types.ts"
import "./style.css";

export const SendOrderToBlingPage = () => {
  const [orderControlData, setOrderControlData] = useState()
  const [TableData, setTableData] = useState()
  const orderNumberInput = useRef(null)
  
  const getOrderControlData = () => {
    if(!orderNumberInput.current) return
    
    const input = orderNumberInput.current as HTMLInputElement
    const orderNumber = /*input.value*/ '701-5357716-7789866'

    api.get(`/api/orders/get-order-control?order_number=${orderNumber}`)
      .then(response => response.data)
      .then((response) => {
        if(response.length === 0) return toast.error("Pedido já cadastrado no bling")
        toast.info("Pedido ainda não está cadastrado no Bling")
        setOrderControlData(response)

        // @ts-ignore
        const extractedData = response.map((item) => ({
          orderId: item.id,
          company: item.id_company,
          sellerChannel: item.sellercentral_name,
          origin: item.online_order_number,
          orderDate: item.order_date,
        }))
        setTableData(extractedData)

      }).catch((error) => {
        toast.error("Erro ao buscar pedido...")
        console.log(error)
      })
  }  

    return (
        <div id="send-order-to-bling-container">
          <Navbar items={[{
            label: 'Importar pedidos', 
            options: [{name: 'Importação', url: '/atendimento/importacao-api'}]
          }]}/>
          <div id="send-order-to-bling-page">
            <label htmlFor="order_number">Número do pedido</label>
            <div id="search-area">
              <input ref={orderNumberInput} type="text" name="order_number" />
              <button onClick={()=>{getOrderControlData()}}>Buscar</button>
            </div>
            {TableData && <div id="table"><SendOrderToBlingTable data={TableData} /></div>}
          </div>
          <ToastContainer/>
        </div>
      )
}