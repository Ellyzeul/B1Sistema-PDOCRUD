import "./style.css"
import { ShipmentLabelProp } from "../type"
import { companyLogos } from "../constants"
import { useEffect, useState } from "react"
import BwipJs from "bwip-js"
import axios from "axios"
import { SimplifiedInvoiceProp } from "./types"

const SimplifiedInvoice = (props: SimplifiedInvoiceProp) => {
  const { include_company_icon, label_data: { company, bling_data }} = props
  const { id, company_name, cnpj, state_registration } = company
  const [accessKeyURI, setAccessKeyURI] = useState('')

  useEffect(() => {
    const { nota } = bling_data
    if(!nota) return
    const { chaveAcesso: access_key } = nota

    const accessKeyCanvas = BwipJs.toCanvas(document.createElement('canvas'), {
      bcid: 'code128', 
      text: access_key as string, 
      width: 277, 
      height: 40, 
    })

    setAccessKeyURI(accessKeyCanvas.toDataURL())
  }, [])
  
  const { nota, itens, valorfrete, observacoes } = bling_data
  if(!nota || !itens || !valorfrete || !observacoes) return <></>
  const { chaveAcesso: access_key, numero: invoice_number, serie: series, dataEmissao: emission_date } = nota
  const freight = Number(valorfrete)
  const itemsList = itens.map(({item: {codigo, descricao, quantidade, valorunidade}}, key) => (
    <div className="simplified-invoice-bought-item" key={key}>
      <div>{codigo} - {descricao} - {quantidade} UN X {Number(valorunidade).toFixed(2)}</div>
      <div className="simplified-invoice-bought-item-cost">{(Number(valorunidade) * Number(quantidade)).toFixed(2)}</div>
    </div>
  ))
  const totalItems = itens
    .map(({item: {quantidade}}) => Number(quantidade))
    .reduce((acc, qnt) => qnt + acc)
  const totalCost = itens
    .map(({item: {valorunidade, quantidade}}) => Number(valorunidade) * Number(quantidade))
    .reduce((acc, cost) => acc + cost)

  return (
    <div id="simplified-invoice">
      <div id="simplified-invoice-title">DANFE Simplificado</div>
      <div id="simplified-invoice-header">
        {
          include_company_icon
          ? <img src={companyLogos[id]} style={{
            width: '100%', 
            height: '80px', 
            objectFit: 'contain', 
          }} alt="" />
          : <div />
        }
        <div>
          CNPJ: {cnpj}
          <br />
          IE: {state_registration}
        </div>
      </div>
      <div id="simplified-invoice-access-key">
        <img src={accessKeyURI} style={{
          width: '100%', 
          height: '40px', 
        }} alt="" />
        <span>{access_key}</span>
        <span>TIPO: 1 - Saída | Nº NFe: {invoice_number} | SERIE: {series}</span>
        <span>Data de emissão: {(new Date(emission_date as string)).toLocaleDateString('pt-BR')}</span>
      </div>
      <div id="simplified-invoice-bought-item-list">
        <div className="simplified-invoice-bought-item">
          <div><strong>ITEM</strong></div>
          <div className="simplified-invoice-bought-item-cost"><strong>VL. ITEM</strong></div>
        </div>
        {itemsList}
      </div>
      <div id="simplified-invoice-bought-item-list">
        <div className="simplified-invoice-bought-item">
          <div>QTS. TOTAL DE ITENS</div>
          <div className="simplified-invoice-bought-item-cost">{totalItems}</div>
        </div>
        <div className="simplified-invoice-bought-item">
          <div>FRETE E OUTRAS DESPESAS</div>
          <div className="simplified-invoice-bought-item-cost">{freight}</div>
        </div>
        <div className="simplified-invoice-bought-item">
          <div><strong>VALOR TOTAL</strong></div>
          <div className="simplified-invoice-bought-item-cost">{(totalCost + freight).toFixed(2)}</div>
        </div>
      </div>
      <div id="simplified-invoice-observation-field">
        <strong>INFORMAÇÕES ADICIONAIS DE INTERESSE DO CONTRIBUINTE</strong>
        <br />
        <div id="simplified-invoice-observation">{observacoes}</div>
      </div>
    </div>
  )
}

export default SimplifiedInvoice
