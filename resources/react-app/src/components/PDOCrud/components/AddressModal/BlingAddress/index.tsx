import BlingAddressProp from "./types"

const BlingAddress = (props: BlingAddressProp) => {
  const { address } = props

  return (
    <div>
      <strong>Endereço do Bling</strong>
      <p className="address-modal-amazon-address"><strong>Nº Bling: </strong>{address.bling_number}</p>
      {address.buyer_name && <p><strong>Nome do Cliente: </strong>{address.buyer_name}</p>}
      {address.address && <p><strong>Endereço: </strong>{address.address}</p>}
      {address.number && <p><strong>Número: </strong>{address.number}</p>}
      {address.complement && <p><strong>Complemento: </strong>{address.complement}</p>}
      {address.county && <p><strong>Bairro: </strong>{address.county}</p>}
      {address.city && <p><strong>Cidade: </strong>{address.city}</p>}
      {address.postal_code && <p><strong>CEP: </strong>{address.postal_code}</p>}
      {address.uf && <p><strong>UF: </strong>{address.uf}</p>}
      {address.landline_phone && <p><strong>Telefone: </strong>{address.landline_phone}</p>}
      {address.cellphone && <p><strong>Celular: </strong>{address.cellphone}</p>}
      {address.cpf_cnpj && <p><strong>CPF/CNPJ: </strong>{address.cpf_cnpj}</p>}
      {address.ie && <p><strong>Inscrição Estadual: </strong>{address.ie}</p>}
      {address.email && <p><strong>E-mail: </strong>{address.email}</p>}
      {address.total_items && <p><strong>Livros no pedido: </strong>{address.total_items}</p>}
      {address.total_value && <p><strong>Valor total: </strong>{address.total_value}</p>}
    </div>
  )
}

export default BlingAddress
