import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableRows from "./getTableRows"

const configureSellercentralColumn = () => {
  const rows = getTableRows()
  const sellercentralIdx = getColumnFieldIndex("Canal de venda")
  const orderNumberIdx = getColumnFieldIndex("ORIGEM")
  const isbnIdx = getColumnFieldIndex("ISBN")

  if(sellercentralIdx === -1 || orderNumberIdx === -1 || isbnIdx === -1) return

  const orderNumbersList = rows.map(row => row.children[orderNumberIdx].textContent?.trim() || '')
  api.get(`/api/orders/order-number-total?order_numbers_list=${JSON.stringify(orderNumbersList)}`)
    .then(response => response.data)
    .then(orderNumbersTotals => rows.forEach(row => {
      const { children } = row
      const cell = children[sellercentralIdx] as HTMLTableCellElement
      const sellercentral = cell.textContent?.trim()
      const orderNumber = children[orderNumberIdx].textContent?.trim() || ""
      const isbn = children[isbnIdx].textContent?.trim() || ""
  
      const upperContainer = document.createElement("div")
      
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
  
      upperContainer.appendChild(sellPageAnchor)
      upperContainer.appendChild(productPageAnchor)
      upperContainer.style.display = "flex"
      upperContainer.style.justifyContent = "space-evenly"
  
      const div = document.createElement('div')
  
      div.style.display = 'flex'
      div.style.flexDirection = 'column'
  
      const totalText = orderNumbersTotals[orderNumber] > 1
        ? `Pedido com ${orderNumbersTotals[orderNumber]} livros`
        : ''

      div.appendChild(upperContainer)

      if(totalText.length > 0) {
        const total = document.createElement('a')

        total.innerText = totalText
        total.href = totalText.length > 0 ? `/pedidos?origem=${orderNumber}` : ''
        total.target = 'blank'
        total.style.color = 'black'

        div.appendChild(total)
      }
  
      cell.textContent = ""
      cell.appendChild(div)
    }))
}

const sellercentrals = {
  "Amazon-BR": {
    sell_page: (onlineOrderNumber) => `https://sellercentral.amazon.com.br/orders-v3/order/${onlineOrderNumber}`,
    product_page: (isbn) => `https://www.amazon.com.br/s?k=${isbn}&__mk_pt_BR=%C3%85M%C3%85%C5%BD%C3%95%C3%91&ref=nb_sb_noss`
  },
  "Amazon-CA": {
    sell_page: (onlineOrderNumber) => `https://sellercentral.amazon.ca/orders-v3/order/${onlineOrderNumber}`,
    product_page: (isbn) => `https://www.amazon.ca/s?k=${isbn}&__mk_pt_BR=%C3%85M%C3%85%C5%BD%C3%95%C3%91&ref=nb_sb_noss`
  },
  "Amazon-UK": {
    sell_page: (onlineOrderNumber) => `https://sellercentral.amazon.co.uk/orders-v3/order/${onlineOrderNumber}`,
    product_page: (isbn) => `https://www.amazon.co.uk/s?k=${isbn}&__mk_pt_BR=%C3%85M%C3%85%C5%BD%C3%95%C3%91&ref=nb_sb_noss`
  },
  "Amazon-US": {
    sell_page: (onlineOrderNumber) => `https://sellercentral.amazon.com/orders-v3/order/${onlineOrderNumber}`,
    product_page: (isbn) => `https://www.amazon.com/s?k=${isbn}&__mk_pt_BR=%C3%85M%C3%85%C5%BD%C3%95%C3%91&ref=nb_sb_noss`
  },
  "Amazon-ES": {
    sell_page: (onlineOrderNumber) => `https://sellercentral.amazon.es/orders-v3/order/${onlineOrderNumber}`,
    product_page: (isbn) => `https://www.amazon.es/s?k=${isbn}&__mk_pt_BR=%C3%85M%C3%85%C5%BD%C3%95%C3%91&ref=nb_sb_noss`
  },
  "Seline-BR": {
    sell_page: (onlineOrderNumber) => `https://livrariaseline.lojavirtualnuvem.com.br/admin/v2/orders?page=1&q=%23${onlineOrderNumber}&status=all`,
    product_page: (isbn) => `https://www.livrariaseline.com.br/search/?q=${isbn}`
  },
  "Estante-BR": {
    sell_page: (onlineOrderNumber) => `https://livreiro.estantevirtual.com.br/v2/vendas/${onlineOrderNumber}`,
    product_page: (_) => `#`
  },
  "Alibris-US": {
    sell_page: (onlineOrderNumber) => `https://sellers.alibris.com/ops/ordersearch.cfm?Order_Nbr=${onlineOrderNumber}`,
    product_page: (isbn) => `https://www.alibris.com/booksearch?mtype=B&keyword=${isbn}&hs.x=0&hs.y=0`
  },
  "FNAC-PT": {
    sell_page: (onlineOrderNumber) => `https://seller.fnac.pt/compte/vendeur/commande/${onlineOrderNumber}`,
    product_page: (isbn) => `https://www.fnac.pt/SearchResult/ResultList.aspx?Search=${isbn}&sft=1&sa=0`
  },
  "FNAC-ES": {
    sell_page: (onlineOrderNumber) => `https://seller.fnac.es/compte/vendeur/commande/${onlineOrderNumber}`,
    product_page: (isbn) => `https://www.fnac.es/SearchResult/ResultList.aspx?Search=${isbn}&sft=1&sa=0`
  },
  "MercadoLivre-BR": {
    sell_page: (onlineOrderNumber) => `https://www.mercadolivre.com.br/vendas/${onlineOrderNumber}/detalhe?callbackUrl=https%3A%2F%2Fwww.mercadolivre.com.br%2Fvendas%2Fomni%2Flista%3Fplatform.id%3DML%26channel%3Dmarketplace%26filters%3D%26sort%3DDATE_CLOSED_DESC%26page%3D1%26search%3D%26startPeriod%3DWITH_DATE_CLOSED_6M_OLD%26toCurrent%3D%26fromCurrent%3D`,
    product_page: (_) => `#`
  },
  "AbeBooks-US": {
    sell_page: (onlineOrderNumber) => `https://www.abebooks.com/servlet/OrderUpdate?abepoid=${onlineOrderNumber}`,
    product_page: (isbn) => `https://www.abebooks.com/servlet/SearchResults?cm_sp=SearchF-_-topnav-_-Results&kn=${isbn}`
  }
} as {
  [key: string]: {
    sell_page: (onlineOrderNumber: string) => string, 
    product_page: (isbn: string) => string
  }
}

export default configureSellercentralColumn
