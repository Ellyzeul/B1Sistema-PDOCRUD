import "./style.css"
import { OrderInfoProps } from "./types"

export const OrderInfo = (props: OrderInfoProps) => {
  const {
    online_order_number, 
    order_date, 
    expected_date, 
    delivered_date, 
    days_for_shipping,
    delivery_method,
    tracking_code,
    id_phase,
    invoice_number,
    bling_number,
    supplier_name,
    supplier_tracking_code,    
    shipping_box_number,
    shipped_by_enviadotcom, 
  } = props

  return(
  <>
    <div className="order-info">
      <div className="order-hist borders"><strong>Histórico do Pedido</strong></div>
      <div className="order-number red-text borders"><strong>{online_order_number}</strong></div>
      <div className="order-dates borders">
        <p><strong>Data do pedido: </strong>{order_date}</p>
        <p><strong>Data para envio: </strong>{delivered_date}</p>
        <p><strong>Data prevista: </strong>{expected_date}</p>
        <p><strong>Dias para a Entrega:</strong> <strong className="red-text">{days_for_shipping}</strong></p>
      </div>
      <div className="tracking-info borders">
        <p><strong>Forma de envio: </strong>{delivery_method}</p>
        <p><strong>Código de Rastreio (Cliente): </strong>{tracking_code}</p>
      </div>
      <div className="phase-info borders">
        <p><strong>Fase do Processo:</strong><strong>{id_phase}</strong></p>
        <p>NF: {invoice_number} {invoice_number && <a href="https://www.google.com" target="_blank"><img src="/icons/url_16x16.png"></img></a>}</p>
        <p>Nº Bling: {bling_number}</p>
      </div>
      <div className="supplier-info borders">
        <p><strong>Fornecedor: </strong>{supplier_name}</p>
        <p><strong>Rastreio Fornecedor: </strong>{supplier_tracking_code}</p>
      </div>
      <div className="box-info borders">
        <p><strong>Nº da caixa: </strong>{shipping_box_number}</p>
        <p><strong>Entregue na Envia.com: </strong>{shipped_by_enviadotcom}</p>
      </div>
    </div>
    {/* <div className="obs-container">
          <strong>Análise interna</strong>
          <button className="save-observation">Salvar</button>
          <textarea name="observation" cols={130} rows={20}></textarea>
    </div>   */}
  </>  
  )
}