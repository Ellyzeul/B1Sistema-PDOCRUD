import { useState } from "react"
import { Navbar } from "../../components/Navbar"
import "./style.css"
import api from "../../services/axios"
import { ToastContainer, toast } from "react-toastify"

const APIOrderImportPage = () => {
  const [imported, setImported] = useState([] as {
    id_company: number, 
		id_sellercentral: number, 
		online_order_number: string, 
		order_date: string, 
		isbn: string, 
		selling_price: number, 
		ship_date: string, 
  }[])

  const handleClick = () => {
    const loadingId = toast.loading('Importando pedidos..')
    api.post('/api/orders/import-via-api')
      .then(response => response.data)
      .then(response => {
        toast.dismiss(loadingId)
        toast.success('Pedidos importados!')
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
      <Navbar items={[]} />
      <div id="api-order-import-page">
        <div id="api-order-import-action-button" onClick={handleClick}>Importar pedidos</div>
        <div id="imported-orders">
          {
            imported.length === 0
              ? <>Nada foi importado.</>
              : <>
                <div className="imported-report">Novos Pedidos - Total {imported.length}</div>
                <div className="imported-report">{
                  imported
                    .map(({ online_order_number, selling_price, isbn, id_company, id_sellercentral }) => 
                      `${companies[id_company]} ${sellercentrals[id_sellercentral]}: #${online_order_number}\n\t- ISBN: ${isbn}\n\t- Valor: ${selling_price}`
                    )
                    .join('\n')
                }</div>
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