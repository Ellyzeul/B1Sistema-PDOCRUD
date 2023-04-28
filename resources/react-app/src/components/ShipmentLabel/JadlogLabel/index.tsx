import { useEffect, useState } from "react"
import { ShipmentLabelProp } from "../type"
import BwipJS from "bwip-js"
import "./style.css"
import SimplifiedInvoice from "../SimplifiedInvoice"

const JadlogLabel = (props: ShipmentLabelProp) => {
  const { order_id, company, bling_data } = props
  const [ code128URI, setCode128URI ] = useState('')
  const { 
    numeroPedidoLoja: online_order_number, 
    nota: invoice_data,
    transporte: tracking_data
  } = bling_data

  console.log(bling_data)
  useEffect(() => {
    const trackingList = tracking_data?.volumes
    if(!trackingList) return
    const pack = trackingList[0].volume?.remessa
    if(!pack) return
    const { numero: tracking_code } = pack
    const canvas = BwipJS.toCanvas(document.createElement('canvas'), {
      bcid: 'code128', 
      text: tracking_code as string, 
      width: 2016, 
      height: 703, 
    })

    setCode128URI(canvas.toDataURL())
  }, [])

  if(!invoice_data) return <>Sem número de nota fiscal...</>
  if(!tracking_data) return <>Sem código de rastreio...</>
  const { numero: invoice_number } = invoice_data
  const { volumes, enderecoEntrega: delivery_address } = tracking_data
  if(!volumes || !delivery_address) return <></>
  const { volume } = volumes[0]
  if(!volume) return <>Sem dados de rastreamento...</>
  const { remessa: pack } = volume
  if(!pack) return <>Sem dados de rastreamento...</>
  const { numero: tracking_code } = pack
  const { 
    nome: name, 
    endereco: address, 
    numero: number, 
    complemento: complement, 
    cidade: city, 
    bairro: county, 
    cep: postal_code, 
    uf
  } = delivery_address

  return (
    <div id="jadlog-label">
      <div id="label-header">
        <img src="/label-logos/jadlog.webp" alt="" />
        <div id="label-header-info">
          <span>Nº Loja Virtual: {online_order_number}</span>
          <span>Nota Fiscal: <strong>{invoice_number}</strong></span>
          <span>ShipmentID: <strong>{tracking_code}</strong></span>
          <span>Volume: <strong>1/1</strong></span>
        </div>
      </div>
      <div id="label-recipient-info">
        <div className="label-recipient-info-inner-container">
          <strong className="label-recipient-info-header">DESTINATÁRIO</strong>
          <strong className="label-recipient-info-header">SHIPMENT ID</strong>
        </div>
        <div className="label-recipient-info-inner-container">
          <div id="label-recipient-info-text">
            {name}
            <br />
            {`${address}, ${number}, ${complement}. ${county}, ${city}/${uf} - ${postal_code}`}
          </div>
          <div>
            <img src={code128URI} alt="" style={{width: "201.6px"}} />
          </div>
        </div>
      </div>
      <div id="label-sender-info">
        {company.name}
        <br />
        Rua Jose Luís da Silva Gomes, 102
        <br />
        Freguesia do Ó
        <br />
        02965-050 / São Paulo - SP
      </div>
      <SimplifiedInvoice include_company_icon={true} label_data={props} />
    </div>
  )
}

export default JadlogLabel
