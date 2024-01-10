import { MouseEventHandler, useRef } from "react"
import "./style.css"
import { TrackingMethodModalProp } from "./types"
import api from "../../../../services/axios"
import { toast } from "react-toastify"

const TrackingMethodModal = (props: TrackingMethodModalProp) => {
  const { refOnlineOrderNumber, refTrackingMethodModal } = props
  const refInput = useRef(null)

  const handleClick: MouseEventHandler = async(event) => {
    event.preventDefault()
    if(!refInput.current || !refTrackingMethodModal.current || !refOnlineOrderNumber.current) return
    const trackingMethod = (refInput.current as HTMLInputElement).value
    const modal = refTrackingMethodModal.current as HTMLDivElement
    const { orderNumber, sellercentral, company, trackingNumber, shipDate } = JSON.parse(
      (refOnlineOrderNumber.current as HTMLSpanElement).textContent?.trim() as string
    )

    const loadingId = toast.loading('Processando...')
    
    const { success, reason, errorPayload }: { success: boolean, reason?: string, errorPayload?: {} } = await api.post('/api/orders/tracking-code/on-sellercentral', {
			orderNumber,
			sellercentral,
			company,
			trackingNumber,
			shipDate,
      service: trackingMethod,
		}).then(response => response.data)

    toast.dismiss(loadingId)

		success
		  ? toast.success('Rastreio atualizado!')
      : toast.error(`Erro ao atualizar o rastreio: ${reason}. ${!!errorPayload ? JSON.stringify(errorPayload) : ''}`)

    modal.classList.add('close')
  }

  const handleClose = () => {
    if(!refTrackingMethodModal.current) return
    const modal = refTrackingMethodModal.current as HTMLDivElement

    modal.classList.add('close')
  }

  return (
    <div ref={refTrackingMethodModal} id="tracking-method-modal" className="close">
      <div id="tracking-method-form">
        <p>Serviço de entrega</p>
        <input type="text" ref={refInput} />
        <button onClick={handleClick}>Enviar</button>
        <i className="fa-solid fa-xmark tracking-method-close" onClick={handleClose}/>
      </div>
    </div>
  )
}

export default TrackingMethodModal
