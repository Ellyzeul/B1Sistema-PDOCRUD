import { toast } from "react-toastify"
import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"
import setNewColumn from "./setNewColumn"

const setSendEmailColumn = (phase: number) => {
  setNewColumn.columns = {}
	const generateData = (row: HTMLTableRowElement) => {
		const askRatingIdx = getColumnFieldIndex("Pedir avaliação")
		const idIdx = getColumnFieldIndex("Nº")
		const selectedOption = askRatingIdx !== -1
			? ((row.children[askRatingIdx] as HTMLTableCellElement)
				.children[0] as HTMLSelectElement)
				.selectedIndex
			: 0
		const isAskable = 
			(selectedOption === 1 && phase === 6.2) || 
			((selectedOption === 1 || selectedOption === 3) && phase === 6.21)
		const div = document.createElement('div')

		if(!isAskable) return div

    const button = document.createElement('button')
    const orderId = (row.children[idIdx] as HTMLTableCellElement).innerText.trim()
    button.innerText = 'Enviar mensagem'
    button.className = 'order-control-ask-rating-button'
    button.addEventListener('click', () => {
      api.post('/api/orders/ask-rating/send', {
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
          div.appendChild(button)
        })

        div.removeChild(button)
    })

    div.style.display = 'flex'
    div.style.flexDirection = 'column'
    div.appendChild(button)

		return div
	}
	setNewColumn("Enviar avaliação", generateData)
}

export default setSendEmailColumn
