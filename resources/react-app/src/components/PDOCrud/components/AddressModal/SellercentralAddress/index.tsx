import SellercentralAddressProp from "./types"

const SellercentralAddress = (props: SellercentralAddressProp) => {
  const { orderNumber, cotation, address } = props
  const truncateNumber = (num: number) => Math.floor(num * 100) / 100

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
      {address.delivery_instructions && <p><strong>Instruções de entrega: </strong>{address.delivery_instructions}</p>}
      {address.buyer_phone && <p><strong>Celular do cliente: </strong>{address.buyer_phone}</p>}
      {address.ship_phone && <p><strong>Celular do destinatário: </strong>{address.ship_phone}</p>}
      {address.buyer_email && <p><strong>E-mail: </strong>{address.buyer_email}</p>}
      {address.expected_date && <p><strong>Data prevista: </strong>{(new Date(`${address.expected_date} 00:00`)).toLocaleDateString('pt-BR')}</p>}
      {address.price && <p><strong>Valor: </strong>{cotation !== 1 ? 'R$': ''}{truncateNumber(address.price * cotation)}</p>}
      {address.freight && <p><strong>Frete: </strong>{cotation !== 1 ? 'R$': ''}{truncateNumber(address.freight * cotation)}</p>}
      {
        address.item_tax || address.freight_tax 
          ? <p><strong>Imposto: </strong>{cotation !== 1 ? 'R$': ''}{truncateNumber(truncateNumber(Number(address.item_tax) + Number(address.freight_tax)) * cotation)}</p> 
          : null
      }
      <p><strong>Total: </strong>{cotation !== 1 ? 'R$': ''}{truncateNumber(truncateNumber(Number(address.price) * cotation) + truncateNumber(Number(address.freight) * cotation) + truncateNumber((Number(address.item_tax) + Number(address.freight_tax)) * cotation))}</p>
    </div>
  )
}

export default SellercentralAddress
