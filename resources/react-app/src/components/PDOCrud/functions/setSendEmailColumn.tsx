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

    const orderId = parseInt((row.children[idIdx] as HTMLTableCellElement).innerText.trim())

    div.style.display = 'flex'
    div.style.justifyContent = 'center'
      
    if(seller_central in createButtonOfSellercentral) {
      const button = createButtonOfSellercentral[seller_central](
        row, 
        companyId, 
        orderId, 
        seller_central, 
        div
      )

      div.appendChild(button)
    }

		return div
	}

	setNewColumn("Enviar avaliação", generateData)
}

export default setSendEmailColumn

const createWhatsappButton = (row: HTMLTableRowElement, companyIdx: number, orderId: number) => {
  const companyId = Number((row.cells[companyIdx].textContent as string).trim())
  const modalContainer = document.createElement('div')
  const modalRoot = createRoot(modalContainer)

  modalRoot.render(<WhatsappModal companyId={companyId} orderId={orderId}/>)

  return modalContainer
}

const createAmazonButton = (row: HTMLTableRowElement, companyId: number, orderId: number, sellerCentral: string, div: HTMLDivElement) => {
  const icon = document.createElement('i')
  icon.className = 'fa-brands fa-amazon'
  icon.style.width = "16px"

  return createButton(row, companyId, orderId, sellerCentral, div, icon)
}

const createEstanteVirtualButton = (row: HTMLTableRowElement, companyId: number, orderId: number, sellerCentral: string, div: HTMLDivElement) => {
  const icon = document.createElement('img')
  icon.src = "/icons/sellercentrals/estante.png"
  icon.style.height = "20px"

  return createButton(row, companyId, orderId, sellerCentral, div, icon)
}

const createMercadoLivreButton = (row: HTMLTableRowElement, companyId: number, orderId: number, sellerCentral: string, div: HTMLDivElement) => {
  const icon = document.createElement('img')
  icon.src = "/icons/sellercentrals/mercado-livre-bk.png"
  icon.style.height = "20px"

  return createButton(row, companyId, orderId, sellerCentral, div, icon, false)
}

const createFNACButton = (row: HTMLTableRowElement, companyId: number, orderId: number, sellerCentral: string, div: HTMLDivElement) => {
  const icon = document.createElement('img')
  icon.src = "/icons/sellercentrals/fnac_black.png"
  icon.style.height = "20px"

  return createButton(row, companyId, orderId, sellerCentral, div, icon)
}

const createButtonOfSellercentral = {
  'Amazon-BR': createAmazonButton, 
  'Amazon-CA': createAmazonButton, 
  'Amazon-UK': createAmazonButton, 
  'Amazon-US': createAmazonButton, 
  'Estante-BR': createEstanteVirtualButton, 
  'MercadoLivre-BR': createMercadoLivreButton, 
  'FNAC-PT': createFNACButton, 
  'FNAC-ES': createFNACButton, 
} as { [key: string]: (
  row: HTMLTableRowElement, 
  companyId: number, 
  orderId: number, 
  sellerCentral: string, 
  div: HTMLDivElement
) => HTMLButtonElement }

const getClickHandler = (row: HTMLTableRowElement, companyId: number, orderId: number, sellerCentral: string, div: HTMLDivElement, askRatingIdx: number, button: HTMLButtonElement) => () => {
  api.post('/api/orders/ask-rating', {
    order_id: orderId,
    company_id:companyId,
    seller_central: sellerCentral
  })
    .then(response => response.data)
    .then(response => {
      console.log(response)
      if(!response.success) return toast.error(`Pedido: ${orderId} - Erro ao enviar mensagem de avaliação`) 
      toast.success(`Pedido: ${orderId} - Sucesso ao enviar mensagem de avaliação`)
      const select = (row.children[askRatingIdx] as HTMLTableCellElement).children[0] as HTMLSelectElement
      select.selectedIndex = select.selectedIndex === 3 ? 4 : 3
    })
    .catch(err => {
      toast.error(err.response.data.message)
      div.appendChild(button)
    })

    div.removeChild(button)
}

const createButton = (row: HTMLTableRowElement, companyId: number, orderId: number, sellerCentral: string, div: HTMLDivElement, icon: HTMLElement, hoverInvert: boolean = true) => {
  const askRatingIdx = getColumnFieldIndex("Pedir avaliação")
  const button = document.createElement('button')
  button.className = 'order-control-ask-rating-button'

  button.appendChild(icon)
  if(hoverInvert) button.classList.add('hover-invert')

  button.addEventListener('click', getClickHandler(
    row, 
    companyId, 
    orderId, 
    sellerCentral, 
    div, 
    askRatingIdx, 
    button
  ))  

  return button
}
