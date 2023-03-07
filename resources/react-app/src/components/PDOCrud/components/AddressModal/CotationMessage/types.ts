import { SellercentralAddress } from "../types"

type CotationMessageProp = {
  cotation: number,
  cotation_date: string,
  sellercentral: SellercentralAddress,
  currency: {
    currency: string | null,
    prefix: string | null,
    name: string | null,
    amazon_link: string | null,
  }
}

export default CotationMessageProp
