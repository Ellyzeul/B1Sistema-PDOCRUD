export type AddressModalProp = {
  orderNumber: string
}

export type OrderAddress = {
  online_order_number: string,
  recipient_name: string | null,
  address_1: string | null,
  address_2: string | null,
  address_3: string | null,
  county: string | null,
  city: string | null,
  state: string | null,
  postal_code: string | null,
  country: string | null,
  cellphone: string | null,
}
