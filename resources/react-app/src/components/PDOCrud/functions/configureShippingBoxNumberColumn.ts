import getColumnFieldIndex from "./getColumnFieldIndex";
import getTableRows from "./getTableRows";

export default function configureShippingBoxNumberColumn() {
  const shippingNumberBoxId = getColumnFieldIndex('Nº da caixa')
  console.log(shippingNumberBoxId)
  if(shippingNumberBoxId === -1) return

  getTableRows().forEach(row => {
    const input = row.children[shippingNumberBoxId].children[0]

    input.className += ' shipping-box-number-input'
  })
}