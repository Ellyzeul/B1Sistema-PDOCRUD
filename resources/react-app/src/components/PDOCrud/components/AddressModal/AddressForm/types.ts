import { BlingAddress, SellercentralAddress } from "../types"

export type AddressFormProp = {
  sellercentral: SellercentralAddress, 
  bling: BlingAddress, 
  orderId: string
}