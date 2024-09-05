import { useEffect } from "react";
import { ShipmentLabelProp } from "../type";
import api from "../../../services/axios";

export default function LoggiLabel({tracking_code}: ShipmentLabelProp) {
  useEffect(() => {
    api.get('/api/tracking/loggi-shipment-label?tracking_code='+tracking_code)
      .then(response => response.data)
      .then(({content}) => window.location.href = `data:application/pdf;base64, ${content}`)
  })
  return (
    <></>
  )
}
