import { toast } from "react-toastify"
import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"
import setNewColumn from "./setNewColumn"
import { WhatsappModal } from "../components/WhatsappModal"
import { createRoot } from "react-dom/client"

const setSendEmailColumn = (phase: number) => {
  setNewColumn.columns = {}
	const generateData = (row: HTMLTableRowElement) => {
	const askRatingIdx = getColumnFieldIndex("Pedir avaliação")
	const idIdx = getColumnFieldIndex("Nº")
  const companyIdx = getColumnFieldIndex("Empresa")
  const sellerIdx = getColumnFieldIndex("Canal de venda")
  const seller_central = (row.children[sellerIdx] as HTMLTableCellElement).innerText.trim()
  const companyId = Number((row.cells[companyIdx].textContent as string).trim())

	const selectedOption = askRatingIdx !== -1
		? ((row.children[askRatingIdx] as HTMLTableCellElement)
			.children[0] as HTMLSelectElement)
			.selectedIndex
		: 0
	const isAskable = 
		(selectedOption === 1 && phase === 6.2) || 
		((selectedOption === 1 || selectedOption === 3) && phase === 6.21)
	const div = document.createElement('div')
  div.className = "order-control-ask-rating-div"

	if(!isAskable) return div

  const orderId = (row.children[idIdx] as HTMLTableCellElement).innerText.trim()


  div.style.display = 'flex'
  div.style.justifyContent = 'center'
    
  if(seller_central === "Amazon-BR" 
      || seller_central === "Amazon-CA" 
      || seller_central === "Amazon-UK"
      || seller_central === "Amazon-US"
  ){
    const amazon_button = createAmazonButton(row, companyId, parseInt(orderId), seller_central, div)
    div.appendChild(amazon_button)
  }


  // const wpp_button = createWhatsappButton(row, companyIdx, parseInt(orderId))
  // div.appendChild(wpp_button)

  if(seller_central === "Estante-BR"){
    const estante_button = createEstanteVirtualButton(row, companyId, parseInt(orderId), seller_central, div)
    div.appendChild(estante_button)
  }

  const mercado_button = createMercadoLivreButton(row, companyId, parseInt(orderId), seller_central, div)
  div.appendChild(mercado_button)

		return div
	}
	setNewColumn("Enviar avaliação", generateData)
}

const createWhatsappButton = (row: HTMLTableRowElement, companyIdx: number, orderId: number) => {
  const companyId = Number((row.cells[companyIdx].textContent as string).trim())
  const modalContainer = document.createElement('div')
  const modalRoot = createRoot(modalContainer)

  modalRoot.render(<WhatsappModal companyId={companyId} orderId={orderId}/>)

  return modalContainer
}

const createAmazonButton = (row: HTMLTableRowElement, companyId: number, orderId: number, sellerCentral: string, div: HTMLDivElement) => {
  const button = document.createElement('button')
  const askRatingIdx = getColumnFieldIndex("Pedir avaliação")

  button.className = 'order-control-ask-rating-button fa-brands fa-amazon'
  button.addEventListener('click', () => {
    api.post('/api/orders/ask-rating', {
      order_id: orderId,
      company_id:companyId,
      seller_central: sellerCentral
    })
      .then(response => response.data)
      .then(response => {
        toast.success(response.message)
        const select = (row.children[askRatingIdx] as HTMLTableCellElement).children[0] as HTMLSelectElement
        select.selectedIndex = select.selectedIndex === 3 ? 4 : 3
      })
      .catch(err => {
        toast.error(err.response.data.message)
        div.appendChild(button)
      })

      div.removeChild(button)
  })

  return button
}

const createEstanteVirtualButton = (row: HTMLTableRowElement, companyId: number, orderId: number, sellerCentral: string, div: HTMLDivElement) => {
  const askRatingIdx = getColumnFieldIndex("Pedir avaliação")
  const estante_button = document.createElement('button')
  const icon = document.createElement('img')
  estante_button.className = 'order-control-ask-rating-button'

  icon.src = "/icons/sellercentrals/estante.png"; 
  icon.style.height = "20px";

  estante_button.appendChild(icon)
  estante_button.classList.add('hover-invert')

  estante_button.addEventListener('click', () => {
    api.post('/api/orders/ask-rating', {
      order_id: orderId,
      company_id:companyId,
      seller_central: sellerCentral
    })
      .then(response => response.data)
      .then(response => {
        toast.success(response.message)
        const select = (row.children[askRatingIdx] as HTMLTableCellElement).children[0] as HTMLSelectElement
        select.selectedIndex = select.selectedIndex === 3 ? 4 : 3
      })
      .catch(err => {
        toast.error(err.response.data.message)
        div.appendChild(estante_button)
      })

      div.removeChild(estante_button)
  })  

  return estante_button
}

const createMercadoLivreButton =  (row: HTMLTableRowElement, companyId: number, orderId: number, sellerCentral: string, div: HTMLDivElement) => {
  const askRatingIdx = getColumnFieldIndex("Pedir avaliação")
  const estante_button = document.createElement('button')
  const icon = document.createElement('img')
  estante_button.className = 'order-control-ask-rating-button'

  icon.src = "/icons/sellercentrals/mercado-livre-bk.png"; 
  icon.style.height = "20px";

  estante_button.appendChild(icon)
  estante_button.classList.add('hover-invert')

  estante_button.addEventListener('click', () => {
    api.post('/api/orders/ask-rating', {
      order_id: orderId,
      company_id:companyId,
      seller_central: sellerCentral
    })
      .then(response => response.data)
      .then(response => {
        toast.success(response.message)
        const select = (row.children[askRatingIdx] as HTMLTableCellElement).children[0] as HTMLSelectElement
        select.selectedIndex = select.selectedIndex === 3 ? 4 : 3
      })
      .catch(err => {
        toast.error(err.response.data.message)
        div.appendChild(estante_button)
      })

      div.removeChild(estante_button)
  })  

  return estante_button
}

export default setSendEmailColumn
