export type CurrencyCotationProp = {
  cotation: number,
  setCotation: React.Dispatch<React.SetStateAction<number>>,
  online_order_number: string,
  observation: string,
  address_form_ref: React.MutableRefObject<null>,
  id_sellercentral: number,
}
