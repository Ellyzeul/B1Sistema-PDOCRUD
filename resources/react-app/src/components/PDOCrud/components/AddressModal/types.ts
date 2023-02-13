export type AddressModalProp = {
  orderNumber: string
}

export type OrderAddress = {
  online_order_number: string,
  buyer_name: string | null,
  recipient_name: string | null,
  buyer_email: string | null,
  address_1: string | null,
  address_2: string | null,
  address_3: string | null,
  county: string | null,
  city: string | null,
  state: string | null,
  postal_code: string | null,
  country: string | null,
  buyer_phone: string | null,
  ship_phone: string | null,
  expected_date: string | null,
  price: number | null,
  freight: number | null,
  item_tax: number | null,
  freight_tax: number | null,
}
