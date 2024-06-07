import { toast } from "react-toastify";
import api from "../../../services/axios";
import getColumnFieldIndex from "./getColumnFieldIndex";
import getTableRows from "./getTableRows";

export default function configureInternalTrackingCodeColumn() {
  const internalTrackingIdx = getColumnFieldIndex('Rastreio interno')
  const orderNumberIdx = getColumnFieldIndex('ORIGEM')
  const rows = getTableRows()

  rows.forEach(({children: row}) => {
    if(row[internalTrackingIdx].textContent?.trim() !== '') return

    const orderNumber = row[orderNumberIdx].textContent?.trim() as string
    const cell = row[internalTrackingIdx] as HTMLTableCellElement

    cell.appendChild(button(orderNumber, cell))
  })
}

function button(orderNumber: string, parent: HTMLTableCellElement) {
  const btn = document.createElement('button')

  btn.style.borderRadius = '4px'
  btn.style.backgroundColor = 'inherit'
  btn.style.textDecoration = 'underline'
  btn.style.border = 'none'
  btn.style.padding = '4px 6px'

  btn.textContent = 'Gerar'

  btn.onclick = async(event) => {
    event.preventDefault()

    const loadingId = toast.loading('Gerando...')
    await api.post('/api/tracking/create-internal', {
      order_number: orderNumber,
    })
      .then(response => response.data)
      .then(({tracking: {id}}) => {
        toast.dismiss(loadingId)
        setInternalTrackingText(id, btn, parent)
      })
      .catch(response => {
        toast.dismiss(loadingId)

        if(!response.tracking.id) {
          toast.error(response.error_message)
          return
        }

        setInternalTrackingText(response.tracking.id, btn, parent)
      })
  }

  return btn
}

function setInternalTrackingText(internalTracking: string, btn: HTMLButtonElement, parent: HTMLTableCellElement) {
  const p = document.createElement('p')

  p.textContent = internalTracking
  parent.appendChild(p)
  parent.removeChild(btn)

  toast.success('Rastreio gerado!')
}
