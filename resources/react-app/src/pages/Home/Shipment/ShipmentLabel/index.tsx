import { useEffect, useState } from "react"
import { useParams } from "react-router-dom"
import api from "../../../../services/axios"
import { ShipmentLabelProp } from "../../../../components/ShipmentLabel/type"
import ShipmentLabel from "../../../../components/ShipmentLabel"

const ShipmentLabelPage = () => {
  const { order_id } = useParams()
  const [labelParams, setLabelParams] = useState({} as ShipmentLabelProp)

  useEffect(() => {
    if(!order_id) return

    api.get(`/api/orders/shipment-label-data?order_id=${order_id}`)
      .then(response => response.data as ShipmentLabelProp)
      .then(setLabelParams)
  }, [])

  if(!order_id) return <>Sem ID do pedido...</>

  return (
    <>{
      (labelParams.id_delivery_method && labelParams.bling_data)
        ? <ShipmentLabel {...labelParams}/>
        : null
    }</>
  )
}

export default ShipmentLabelPage
