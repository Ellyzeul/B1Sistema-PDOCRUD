import { useEffect, useState } from "react";
import { ShipmentLabelProp } from "../type";
import api from "../../../services/axios";

export default function LoggiLabel({tracking_code}: ShipmentLabelProp) {
  const [link, setLink] = useState('')

  useEffect(() => {
    api.get('/api/tracking/loggi-shipment-label?tracking_code='+tracking_code)
      .then(response => response.data)
      .then(({content}) => setLink(`data:application/pdf;base64, ${content}`))
  }, [])
  return (
    <iframe src={link} width='100%' height='100%'></iframe>
  )
}
