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

    const mail_button = document.createElement('button')
    const orderId = (row.children[idIdx] as HTMLTableCellElement).innerText.trim()
    mail_button.className = 'order-control-ask-rating-button fa-brands fa-amazon'
    mail_button.addEventListener('click', () => {
      api.post('/api/orders/ask-rating/mail/send', {
        order_id: orderId
      })
        .then(response => response.data)
        .then(response => {
          toast.success(response.message)
          const select = (row.children[askRatingIdx] as HTMLTableCellElement).children[0] as HTMLSelectElement
          select.selectedIndex = select.selectedIndex === 3 ? 4 : 3
        })
        .catch(err => {
          toast.error(err.response.data.message)
          div.appendChild(mail_button)
        })

        div.removeChild(mail_button)
    })

    div.style.display = 'flex'
    div.style.justifyContent = 'center'
    div.appendChild(mail_button)

    const wpp_button = createWhatsappButton(row, companyIdx, parseInt(orderId))

    div.appendChild(wpp_button)

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

export default setSendEmailColumn
