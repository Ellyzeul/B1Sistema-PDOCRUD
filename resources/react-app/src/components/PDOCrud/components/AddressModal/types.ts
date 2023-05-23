export type AddressModalProp = {
  orderNumber: string,
  orderId: string
}

export type OrderAddress = {
  sellercentral: SellercentralAddress,
  bling: BlingAddress
}

export type SellercentralAddress = {
  online_order_number: string,
  buyer_name: string,
  recipient_name: string,
  cpf_cnpj: string,
  buyer_email: string,
  address_1: string,
  address_2: string,
  address_3: string,
  county: string,
  city: string,
  state: string,
  postal_code: string,
  country: string,
  buyer_phone: string,
  ship_phone: string,
  expected_date: string,
  price: number,
  freight: number,
  item_tax: number,
  freight_tax: number,
  delivery_instructions: string, 
  id_sellercentral: number,
  id_company: number,
  delivery_method: string | null,
  tracking_code: string | null,
}

export type BlingAddress = {
  update_data: any,
  bling_number: string,
  buyer_name: string,
  recipient_name: string,
  person_type: 'F' | 'J' | 'E' | '',
  cpf_cnpj: string,
  ie: string,
  address: string,
  number: string,
  complement: string,
  city: string,
  county: string,
  email: string,
  cellphone: string,
  landline: string,
  postal_code: string,
  uf: string,
  total_items: number,
  total_value: number,
  freight: number,
  other_expenses: number,
  discount: number,
  expected_date: string,
  observation: string,
  items: {
    id: string,
    sku: string,
    title: string,
    quantity: number,
    value: number,
  }[]
}
