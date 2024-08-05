export type SupplierPurchase = {
  id: number,
  id_company: number,
  supplier: string,
  purchase_method: string,
  freight: number,
  sales_total: number,
  items: Array<SupplierPurchaseItem>,
}

export type SupplierPurchaseItem = {
  id: number,
  id_order: number,
  value: number,
}
