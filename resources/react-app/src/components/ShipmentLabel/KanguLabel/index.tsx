import { useState } from "react"
import { ShipmentLabelProp } from "../type"
import api from "../../../services/axios"

const KanguLabel = (props: ShipmentLabelProp) => {
  const { company: { id }, tracking_code } = props
  const [pdfUrl, setPdfUrl] = useState('')
  const [placeholder, setPlaceholder] = useState('Carregando...')
  const companyName = companiesNames[id]

  api.get(`/api/tracking/kangu-shipment-label?tracking_code=${tracking_code}&company=${companyName}`)
    .then(response => response.data)
    .then(response => {
      console.log(response)
      if(!response.success) return setPlaceholder(response.error_msg)
      const { content } = response

      setPdfUrl(content)
    })

  return (
    <>{
      pdfUrl
        ? <iframe src={pdfUrl} title="Etiqueta da Kangu" style={{
          width: '100vw',
          height: '100vh',
          padding: 0,
          margin: 0,
          border: 0,
        }}/>
        : placeholder
    }</>
  )
}

export default KanguLabel

const companiesNames = {
  0: 'seline',
  1: 'b1',
} as {[id: number]: string}
