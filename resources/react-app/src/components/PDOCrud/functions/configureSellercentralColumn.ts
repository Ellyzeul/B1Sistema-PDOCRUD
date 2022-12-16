import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableRows from "./getTableRows"

const configureSellercentralColumn = () => {
  const rows = getTableRows()
  const sellercentralIdx = getColumnFieldIndex("Canal de venda")
  const orderNumberIdx = getColumnFieldIndex("ORIGEM")
  const isbnIdx = getColumnFieldIndex("ISBN")

  if(sellercentralIdx === -1 || orderNumberIdx === -1 || isbnIdx === -1) return

  rows.forEach(row => {
    const { children } = row
    const cell = children[sellercentralIdx] as HTMLTableCellElement
    const sellercentral = cell.textContent?.trim()
    const orderNumber = children[orderNumberIdx].textContent?.trim() || ""
    const isbn = children[isbnIdx].textContent?.trim() || ""

    const div = document.createElement("div")
    
    const sellPageAnchor = document.createElement('a')
    sellPageAnchor.href = sellercentral ? sellercentrals[sellercentral].sell_page(orderNumber) : ""
    sellPageAnchor.target = "_blank"
    sellPageAnchor.text = sellercentral || ""

    const productPageAnchor = document.createElement('a')
    productPageAnchor.href = sellercentral ? sellercentrals[sellercentral].product_page(isbn) : ""
    productPageAnchor.target = "_blank"
    const icon = document.createElement('img')
    icon.src = '/icons/url_16x16.png'
    productPageAnchor.appendChild(icon)

    div.appendChild(sellPageAnchor)
    div.appendChild(productPageAnchor)
    div.style.display = "flex"
    div.style.justifyContent = "space-evenly"

    cell.textContent = ""
    cell.appendChild(div)
  })
}

const sellercentrals = {
  "Amazon-BR": {
    sell_page: (onlineOrderNumber: string) => `https://sellercentral.amazon.com.br/orders-v3/order/${onlineOrderNumber}`,
    product_page: (isbn: string) => `https://www.amazon.com.br/s?k=${isbn}&__mk_pt_BR=%C3%85M%C3%85%C5%BD%C3%95%C3%91&ref=nb_sb_noss`
  },
  "Amazon-CA": {
    sell_page: (onlineOrderNumber: string) => `https://sellercentral.amazon.ca/orders-v3/order/${onlineOrderNumber}`,
    product_page: (isbn: string) => `https://www.amazon.ca/s?k=${isbn}&__mk_pt_BR=%C3%85M%C3%85%C5%BD%C3%95%C3%91&ref=nb_sb_noss`
  },
  "Amazon-UK": {
    sell_page: (onlineOrderNumber: string) => `https://sellercentral.amazon.co.uk/orders-v3/order/${onlineOrderNumber}`,
    product_page: (isbn: string) => `https://www.amazon.co.uk/s?k=${isbn}&__mk_pt_BR=%C3%85M%C3%85%C5%BD%C3%95%C3%91&ref=nb_sb_noss`
  },
  "Amazon-US": {
    sell_page: (onlineOrderNumber: string) => `https://sellercentral.amazon.com/orders-v3/order/${onlineOrderNumber}`,
    product_page: (isbn: string) => `https://www.amazon.com/s?k=${isbn}&__mk_pt_BR=%C3%85M%C3%85%C5%BD%C3%95%C3%91&ref=nb_sb_noss`
  },
  "Seline-BR": {
    sell_page: (onlineOrderNumber: string) => `https://livrariaseline.lojavirtualnuvem.com.br/admin/v2/orders?page=1&q=%23${onlineOrderNumber}&status=all`,
    product_page: (isbn: string) => `https://www.livrariaseline.com.br/search/?q=${isbn}`
  }
} as {
  [key: string]: {
    sell_page: (onlineOrderNumber: string) => string, 
    product_page: (isbn: string) => string
  }
}

export default configureSellercentralColumn
