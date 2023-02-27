import SellercentralAddressProp from "./types"

const SellercentralAddress = (props: SellercentralAddressProp) => {
  const { orderNumber, address } = props

  return (
    <div>
      <strong>Endereço do canal de venda</strong>
      <p className="address-modal-amazon-address"><strong>Nº do pedido: </strong>{orderNumber}</p>
      {address.buyer_name && <p><strong>Cliente: </strong>{address.buyer_name}</p>}
      {address.recipient_name && <p><strong>Destinatário: </strong>{address.recipient_name}</p>}
      {address.address_1 && <p><strong>Endereço 1: </strong>{address.address_1}</p>}
      {address.address_2 && <p><strong>Endereço 2: </strong>{address.address_2}</p>}
      {address.address_3 && <p><strong>Endereço 3: </strong>{address.address_3}</p>}
      {address.county && <p><strong>Bairro: </strong>{address.county}</p>}
      <p>{address.online_order_number && <strong>Cidade: </strong>}
        {address.city}
        {address.state && `, ${address.state}`}
        {address.postal_code && `, ${address.postal_code}`}
        {address.country && `, ${address.country}`}
      </p>
      {address.buyer_phone && <p><strong>Celular do cliente: </strong>{address.buyer_phone}</p>}
      {address.ship_phone && <p><strong>Celular do destinatário: </strong>{address.ship_phone}</p>}
      {address.buyer_email && <p><strong>E-mail: </strong>{address.buyer_email}</p>}
      {address.expected_date && <p><strong>Data prevista: </strong>{(new Date(`${address.expected_date} 00:00`)).toLocaleDateString('pt-BR')}</p>}
      {address.price && <p><strong>Valor: </strong>{address.price}</p>}
      {address.price && <p><strong>Frete: </strong>{address.freight}</p>}
      {
        address.item_tax || address.freight_tax 
          ? <p><strong>Imposto: </strong>{Number(address.item_tax) + Number(address.freight_tax)}</p> 
          : null
      }
      <p><strong>Total: </strong>{Number(address.price) + Number(address.freight) + Number(address.item_tax) + Number(address.freight_tax)}</p>
    </div>
  )
}

export default SellercentralAddress
