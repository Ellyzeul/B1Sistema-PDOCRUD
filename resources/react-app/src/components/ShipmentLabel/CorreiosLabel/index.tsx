import { useEffect, useState } from "react"
import { ShipmentLabelProp } from "../type"
import BwipJs from "bwip-js"
import "./style.css"
import SimplifiedInvoice from "../SimplifiedInvoice"
import { companyLogos } from "../constants"

const CorreiosLabel = (props: ShipmentLabelProp) => {
  const { order_id, company, bling_data } = props
  const [datamatrixURI, setDatamatrixURI] = useState('')
  const [trackingCodeBarcodeURI, setTrackingCodeBarcodeURI] = useState('')
  const [postalCodeBarcodeURI, setPostalCodeBarcodeURI] = useState('')

  useEffect(() => {
    const trackingList = bling_data.transporte?.volumes
    if(!trackingList) return
    const trackingCode = trackingList[0].volume?.codigoRastreamento as string
    const trackingService = trackingList[0].volume?.codigoServico as string
    const postalCode = bling_data?.cliente?.cep
    const clientNumber = bling_data?.cliente?.numero as string
    const clientComplement = bling_data?.cliente?.complemento as string
    if(!trackingCode || !postalCode) return

    const datamatrix = BwipJs.toCanvas(document.createElement('canvas'), {
      bcid: 'datamatrix', 
      text: getDatamatrixCode(postalCode, trackingCode, trackingService, clientNumber, clientComplement), 
      width: 32, 
      height: 32, 
    })
    const trackingCodeBarcode = BwipJs.toCanvas(document.createElement('canvas'), {
      bcid: 'code128', 
      text: trackingCode, 
      width: 293.283, 
      height: 73.95, 
    })
    const postalCodeBarcode = BwipJs.toCanvas(document.createElement('canvas'), {
      bcid: 'code128', 
      text: postalCode, 
      width: 293.283, 
      height: 73.95, 
    })

    setDatamatrixURI(datamatrix.toDataURL())
    setTrackingCodeBarcodeURI(trackingCodeBarcode.toDataURL())
    setPostalCodeBarcodeURI(postalCodeBarcode.toDataURL())
  }, [])
  
  const trackingList = bling_data.transporte?.volumes
  const invoiceNumber = bling_data.nota?.numero
  const onlineOrderNumber = bling_data.numeroPedidoLoja
  const client = bling_data.cliente
  const deliveryAddress = bling_data.transporte?.enderecoEntrega
  if(!trackingList || !invoiceNumber || !onlineOrderNumber || !client || !deliveryAddress) return <>Dados insuficientes do Bling...</>
  const trackingCode = trackingList[0].volume?.codigoRastreamento as string
  const trackingService = trackingList[0].volume?.codigoServico as string
  const pack = trackingList[0].volume?.remessa as {numero: string, dataCriacao: string}

  const { fone, celular } = client
  const { nome, endereco, numero, complemento, cidade, bairro, cep, uf } = deliveryAddress
  const { name: deliveryName, icon: deliveryIcon } = correiosIcons[trackingService]
  const { numero: plp } = pack

  return (
    <div id="correios-label">
      <div id="correios-label-header">
        <div className="correios-label-header-inner-container">
          <img src={companyLogos[company.id]} style={{
            width: '140px', 
            height: '87px', 
            objectFit: 'contain', 
          }} alt="" />
          <img src={datamatrixURI} style={{
            width: '80px', 
            height: '80px'
          }} alt="" />
          <img src={deliveryIcon} style={{
            width: '1.7037037037037cm', 
            height: '2.1428571428571cm', 
          }} alt="" />
        </div>
        <div className="correios-label-header-inner-container">
          <div style={{width: '140px'}}>
            NF: {invoiceNumber}
            <br />
            Nº Loja Virtual:
            <br />
            {onlineOrderNumber}
          </div>
          <div style={{textAlign: 'center'}}>
            Contrato
            <br />
            <strong>9912449300</strong>
            <br />
            <strong>{deliveryName}</strong>
          </div>
          <div>
            Volume: 1/1
            {
              plp
              ? <>
                <br />
                PLP: 
                <br />
                <strong>{plp}</strong>
              </>
              : null
            }
          </div>
        </div>
        <div id="correios-label-header-tracking-code">
          <strong>{trackingCode}</strong>
        </div>
        <div className="correios-label-header-inner-container" style={{
          height: '73.95px', 
          paddingBottom: '8px'
        }}>
          <img src={trackingCodeBarcodeURI} style={{width: '293.283px'}} alt="" />
          <div style={{
            height: '100%', 
            display: 'flex', 
            justifyContent: 'center', 
            alignItems: 'center',
            fontSize: '0.33cm'
          }}>
            <strong>VD</strong>
          </div>
        </div>
        <div style={{
          width: '100%', 
          display: 'flex', 
          flexDirection: 'column', 
        }}>
          <div className="correios-signature-container">
            Recebedor: <span></span>
          </div>
          <div className="correios-signature-container">
            Assinatura: <span style={{width: '145%'}}></span> Documento: <span></span>
          </div>
        </div>
      </div>
      <div id="correios-label-recipient">
        <div id="correios-label-recipient-header">
          <strong style={{
            padding: '2px 8px', 
            backgroundColor: 'black', 
            color: 'white', 
          }}>DESTINATÁRIO</strong>
          <img src="/label-logos/correios.webp" alt="" />
        </div>
        <div id="correios-label-recipient-info">
          {nome && <>{nome}</>}{fone && <> - {fone}</>}{celular && <>/{celular}</>}{nome || fone ? <br /> : null}
          {endereco && <>{endereco}</>}{numero && <>, {numero}</>}{endereco || numero ? <br /> : null}
          {complemento && <>{complemento}</>} {bairro && <>{bairro}</>}{complemento || bairro ? <br /> : null}
          {cep && <strong>{cep.replace('.', '')}</strong>} {cidade && <>{cidade}</>}{uf && <>/{uf}</>}{cep || cidade || uf ? <br /> : null}
          <img src={postalCodeBarcodeURI} style={{
            width: '148.55px', 
            height: '73.95px', 
          }} alt="" />
        </div>
      </div>
      <div id="correios-label-sender-info">
        {company.name}
        <br />
        Rua Jose Luís da Silva Gomes, 102
        <br />
        Freguesia do Ó
        <br />
        02965-050 / São Paulo - SP
      </div>
      <SimplifiedInvoice include_company_icon={false} label_data={props} />
    </div>
  )
}

const correiosIcons = {
  '03298': {
    name: 'PAC', 
    icon: '/label-logos/pac.png', 
  }, 
  '04227': {
    name: 'CORREIOS MINI', 
    icon: '/label-logos/correios_mini.png', 
  }, 
  '03220': {
    name: 'SEDEX', 
    icon: '/label-logos/sedex.png', 
  }, 
  '03204': {
    name: 'SEDEX', 
    icon: '/label-logos/sedex_hoje_10_12.png', 
  }, 
} as {[key: string]: {
  name: string, 
  icon: string, 
}}

const getDatamatrixCode = (postalCode: string, trackingCode: string, trackingService: string, clientNumber: string, complement: string) => {
  const checksum = getPostalCodeChecksum(postalCode)
  const num = padAddressNumber(clientNumber)
  const compl = padAndTruncateComplement(complement)

  return `${postalCode}000000296505000000${checksum}51${trackingCode}2519000000000074407562${trackingService}00${num}${compl}00000-00.000000-00.000000`
}

const getPostalCodeChecksum = (postalCode: string) => {
  const sum = postalCode.split('')
    .map(char => Number(char))
    .reduce((acc, digit) => acc + digit)

  if(sum >= 10) {
    const willIncrement = sum % 10 === 0 ? 0 : 1
    const tenMultipleAbove = (Math.floor(sum/10) + willIncrement) * 10

    return tenMultipleAbove - sum
  }

  return sum
}

const padAddressNumber = (num: string) => `${Array(5 - num.length).fill(0).join('')}${num}`
const padAndTruncateComplement = (complement: string) => {
  const truncatted = complement.slice(0, 20)
  const pad = Array(20 - truncatted.length).fill(0).join('')

  return `${truncatted}${pad}`
}

export default CorreiosLabel
