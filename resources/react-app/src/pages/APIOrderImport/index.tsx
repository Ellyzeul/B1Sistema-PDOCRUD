import { useRef, useState } from "react"
import { Navbar } from "../../components/Navbar"
import "./style.css"
import api from "../../services/axios"
import { ToastContainer, toast } from "react-toastify"

const APIOrderImportPage = () => {
  const [imported, setImported] = useState([] as {
    status: string;
    content: Array<{
      id_company: number;
      id_sellercentral: number;
      online_order_number: string;
      order_date: string;
      isbn: string;
      selling_price: number;
      ship_date: string;
    }>
  }[])
  const dateRef = useRef(null)

  const handleClick = () => {
    if(!dateRef.current) return

    const dateInput = dateRef.current as HTMLInputElement
    const fromDate = dateInput.value
    const loadingId = toast.loading('Importando pedidos..')
    api.post('/api/orders/import-from-date', {
      from: fromDate
    })
      .then(response => response.data)
      .then(response => {
        toast.dismiss(loadingId)
        response.forEach((element: { status: string, content: string }) => {
          if(element.status === 'error') return toast.error(element.content)
        })
        console.log(response)
        setImported(response)
      })
      .catch((err) => {
        toast.dismiss(loadingId)
        toast.error('Algum erro ocorreu')
        console.log(err)
      })
  }

  return (
    <div id="api-order-import-page-container">
      <Navbar items={[
        {label: 'Aceitar pedidos', options: [{name: 'FNAC', url: '/atendimento/importacao-api/aceitar-fnac'}]}, 
        {label: 'Cadastrar pedidos', options: [{name: 'Bling', url: '/atendimento/enviar-pedidos'}]}
      ]}/>
      <div id="api-order-import-page">
        <div id="import-options">
          <label htmlFor="from_date">Puxar a partir da data: </label>
          <input type="date" name="from_date" ref={dateRef}/>
          <div id="api-order-import-action-button" onClick={handleClick}>Importar pedidos</div>
        </div>
        <div id="imported-orders">
        {
            imported.length === 0
              ? <>Nada foi importado.</>
              : <>
                {imported.map((responseItem, index) => (
                  <div key={index}>
                    {responseItem.status === 'success' && (
                      <div className="imported-report">Novos Pedidos - Total {responseItem.content.length}</div>
                    )}
                    {responseItem.content.map((order, orderIndex) => (
                      <div className="imported-report" key={orderIndex}>
                        {`${companies[order.id_company]} ${sellercentrals[order.id_sellercentral]}: #${order.online_order_number}`}
                        <ul>
                          <li>ISBN: {order.isbn}</li>
                          <li>Valor: {order.selling_price}</li>
                        </ul>
                      </div>
                    ))}
                  </div>
              ))}
              </>
          }
        </div>
      </div>
      <ToastContainer/>
    </div>
  )
}

const companies = {
  0: 'Seline', 
  1: 'B1', 
} as {[key: number]: string}

const sellercentrals = {
  1: 'Amazon-BR', 
  2: 'Amazon-CA', 
  3: 'Amazon-UK', 
  4: 'Amazon-US', 
  5: 'Seline-BR', 
  6: 'Estante-BR', 
  7: 'Alibris-US', 
  8: 'FNAC-PT', 
  9: 'MercadoLivre-BR', 
} as {[key: number]: string}

export default APIOrderImportPage