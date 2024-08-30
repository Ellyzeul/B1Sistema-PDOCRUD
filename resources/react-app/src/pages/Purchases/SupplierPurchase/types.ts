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
  items: Array<SupplierPurchaseItem>,
}

export type SupplierPurchaseItem = {
  id: number,
  id_order: number,
  id_purchase: number,
  value: number,
  status: string,
}

export type BankAccount = {
  id_company:	number,
  id_bank:	number,
  name:	string,
  account: string,
  agency: string,
}
