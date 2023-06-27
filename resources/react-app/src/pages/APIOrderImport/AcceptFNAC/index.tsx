import { useRef } from "react"
import { Navbar } from "../../../components/Navbar"
import "./style.css"
import api from "../../../services/axios"

const AcceptFNACPage = () => {
  const orderNumberInput = useRef(null)

  const handleClick = () => {
    if(!orderNumberInput.current) return

    const input = orderNumberInput.current as HTMLInputElement
    const orderNumber = input.value

    api.patch('/api/orders/accept-fnac', {
      order_number: orderNumber
    })
      .then(response => response.data)
      .then(console.log)
  }

  return (
    <div id="accept-fnac-container">
      <Navbar items={[{
        label: 'Importar pedidos', 
        options: [{name: 'Importação', url: '/atendimento/importacao-api'}]
      }]}/>
      <div id="accept-fnac-page">
        <label htmlFor="order_number">Número do pedido</label>
        <div>
          <input type="text" name="order_number" />
          <button>Aceitar</button>
        </div>
      </div>
    </div>
  )
}


export default AcceptFNACPage