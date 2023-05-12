type ShipmentAndPricePros = {
    orderId: string
} 

export type CorreiosData = Array<{
    [key: string]: {
      delivery_expected_date: number | null,
      max_date: string | null,
      shipping_error_msg: string | null,
      price: string | null,
      price_error_msg: string | null,
    }
}>

export type JadlogData = {
    price: number | null,
    max_date: number | null,
    error_msg: string | null
}
  
export default ShipmentAndPricePros