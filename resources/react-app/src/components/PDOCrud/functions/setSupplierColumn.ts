import api from "../../../services/axios";
import getTableRows from "./getTableRows";
import setNewColumn from "./setNewColumn";

export default function setSupplierColumn() {
  const orderIds = getTableRows()
    .map(row => `order_ids[]=${row.children[0].textContent?.trim()}`)
    .join('&')

  api.get(`/api/orders/purchase-id?${orderIds}`)
    .then(response => response.data as Record<string, string>)
    .then(purchasesIds => setNewColumn('Nº Compra', row => {
      const div = document.createElement('div')

      div.textContent = purchasesIds[row.children[0].textContent?.trim() ?? ''] ?? ''
  
      return div
    }))
}