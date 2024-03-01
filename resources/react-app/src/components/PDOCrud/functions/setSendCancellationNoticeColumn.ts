import { toast } from "react-toastify"
import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"
import setNewColumn from "./setNewColumn"

const setSendCancellationNoticeColumn = (phase: number) => {
  if(!AVAILABLE_PHASES.includes(phase)) return
  setNewColumn.columns = {}

	setNewColumn("Enviar mensagem", row => generateData(row, phase))
}

export default setSendCancellationNoticeColumn

const AVAILABLE_PHASES = [8.1, 8.12]

const generateData = (row: HTMLTableRowElement, phase: number) => {
  const div = document.createElement('div')
  const idIdx = getColumnFieldIndex('Nº')
  const sellercentralIdx = getColumnFieldIndex('Canal de venda')
  if(idIdx === -1 || sellercentralIdx === -1) return div

  const sellercental = row.children[sellercentralIdx].textContent?.trim() as string
  if(!sellercental.includes('Amazon')) return div

  const orderId = row.children[idIdx].textContent?.trim() as string
  const button = document.createElement('button')

  div.style.display = 'grid'
  div.style.placeItems = 'center'

  button.style.padding = '8px 6px'
  button.style.border = 'none'
  button.style.borderRadius = '4px'
  button.style.backgroundColor = '#09872b'
  button.style.color = 'white'
  button.textContent = 'Enviar'

  button.onclick = ev => handleClick(ev, phase, orderId)

  div.appendChild(button)

  return div
}

const handleClick = (event: MouseEvent, phase: number, orderId: string) => {
  event.preventDefault()

  const loadingId = toast.loading('Enviando...')

  api.post(ENDPOINTS[phase], { order_id: orderId })
    .then(response => response.data)
    .then(({ success, content }) => {
      toast.dismiss(loadingId)
      console.log(success, content)

      if(!success) {
        toast.error(content)
        return
      }

      toast.success('Mensagem enviada com sucesso!')
    })
    .catch(err => {
      toast.dismiss(loadingId)
      toast.error('Algum erro interno ocorreu...')
      console.log(err)
    })
}

const ENDPOINTS: Record<number, string> = {
  8.1: '/api/orders/cancellation-notice',
  8.12: '/api/orders/pre-cancellation-notice',
}
