import { useRef } from "react"
import { Navbar } from "../../../components/Navbar"
import "./style.css"
import api from "../../../services/axios"
import { ToastContainer, toast } from "react-toastify"

const AcceptFNACPage = () => {
  const orderNumberInput = useRef(null)

  const handleClick = () => {
    if(!orderNumberInput.current) return

    const input = orderNumberInput.current as HTMLInputElement
    const orderNumber = input.value

    const loadingId = toast.loading('Processando...')
    api.patch('/api/orders/accept-fnac', {
      order_number: orderNumber
    })
      .then(response => response.data)
      .then(({success, current_status}) => {
        toast.dismiss(loadingId)
        const status = mapStatus[current_status]

        if(success === 'OK') {
          toast.success(`Pedido atualizado com sucesso! Estado atual do pedido é: ${status}`)
          return
        }

        toast.error(`Erro ao atualizar pedido... Atualmente ele está com o estado: ${status}`)
      })
      .catch(() => {
        toast.dismiss(loadingId)
        toast.error('Erro ao processar pedido...')
      })
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
          <input ref={orderNumberInput} type="text" name="order_number" />
          <button onClick={handleClick}>Aceitar</button>
        </div>
      </div>
      <ToastContainer/>
    </div>
  )
}

const mapStatus = {
  'Created': 'Criado', 
  'Accepted': 'Aceito e esperando pagamento do cliente', 
  'Refused': 'Rejeitado', 
  'ToShip': 'Expedição pendente', 
  'Shipped': 'Expedido', 
  'NotReceived': 'Não recebido pelo cliente', 
  'Received': 'Recebido pelo cliente', 
  'Cancelled': 'Cancelado pelo cliente', 
  'Refunded': 'Reembolsado', 
  'Error': 'O pedido está com algum erro e não foi possível atualizar', 
} as {[key: string]: string}

export default AcceptFNACPage