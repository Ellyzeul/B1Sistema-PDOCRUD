import JadlogLabel from "./JadlogLabel"
import { ShipmentLabelProp } from "./type"
import "./style.css"
import CorreiosLabel from "./CorreiosLabel"
import KanguLabel from "./KanguLabel"
import LoggiLabel from "./LoggiLabel"

const ShipmentLabel = (props: ShipmentLabelProp) => {
  return (
    <div id="shipment-label">{getDeliveryMethodLabel(props)}</div>
  )
}

const getDeliveryMethodLabel = (props: ShipmentLabelProp) => {
  const { id_delivery_method } = props

  if(id_delivery_method === 2) return <CorreiosLabel {...props} />
  if(id_delivery_method === 5) return <JadlogLabel {...props} />
  if(id_delivery_method === 11) return <KanguLabel {...props} />
  if(id_delivery_method === 12) return <LoggiLabel {...props} />

  return <></>
}

export default ShipmentLabel
