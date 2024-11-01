import { Invoice } from "../../Financial/Match/types"

export type SupplierPurchase = {
  id: number,
  id_company: number,
  supplier: string,
  purchase_method: string,
  id_bank?: number,
  id_payment_method?: number,
  freight: number,
  sales_total: number,
  date: string,
  status: string,
  observation: string,
  payment_date: string,
  items: Array<SupplierPurchaseItem>,
  delivery_address: string,
  delivery_method: string,
  order_number: string,
  reference: string,
  tracking_code: string,
  id_delivery_address: number,
  id_delivery_method: number,
}

export type SupplierPurchaseItem = {
  id: number,
  id_purchase: number,
  id_order: number,
  value: number,
  order: {
    id_sellercentral: number,
    selling_price: number,
  },
  status: 'pending' | 'delivered' | 'cancelled' | 'failed',
  invoice_key: string,
  supplier?: {
    id: number,
    name: string,
  },
  items_on_purchase: number,
  invoice?: Invoice,
}

export type BankAccount = {
  id_company:	number,
  id_bank:	number,
  name:	string,
  account: string,
  agency: string,
}
