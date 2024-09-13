export const generateSellerCentralUrl = (sellercentral: string, onlineOrderNumber: string) => {
  return sellercentrals[sellercentral] ? sellercentrals[sellercentral].sell_page(onlineOrderNumber) : ""
}

export const generateProductPageUrl = (sellercentral: string, isbn: string) => {
  console.log(`'${sellercentral}'`)
  return sellercentrals[sellercentral] ? sellercentrals[sellercentral].product_page(isbn) : ""
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
    "MercadoLivre-BR": {
      sell_page: (onlineOrderNumber) => `https://www.mercadolivre.com.br/vendas/${onlineOrderNumber}/detalhe?callbackUrl=https%3A%2F%2Fwww.mercadolivre.com.br%2Fvendas%2Fomni%2Flista%3Fplatform.id%3DML%26channel%3Dmarketplace%26filters%3D%26sort%3DDATE_CLOSED_DESC%26page%3D1%26search%3D%26startPeriod%3DWITH_DATE_CLOSED_6M_OLD%26toCurrent%3D%26fromCurrent%3D`,
      product_page: (_) => `#`
    }, 
    "MagazineLuiza-BR": {
      sell_page: (_) => `#`, 
      product_page: (_) => `#`
    }, 
    "FNAC-ES": {
      sell_page: (onlineOrderNumber) => `https://seller.fnac.es/compte/vendeur/commande/${onlineOrderNumber}`, 
      product_page: (isbn) => `https://www.fnac.es/SearchResult/ResultList.aspx?Search=${isbn}&sft=1&sa=0`
    },
    "Amazon-ES": {
      sell_page: (onlineOrderNumber) => `https://sellercentral.amazon.es/orders-v3/order/${onlineOrderNumber}`,
      product_page: (isbn) => `https://www.amazon.es/s?k=${isbn}&__mk_pt_BR=%C3%85M%C3%85%C5%BD%C3%95%C3%91&ref=nb_sb_noss`
    }
  } as {
    [key: string]: {
      sell_page: (onlineOrderNumber: string) => string, 
      product_page: (isbn: string) => string
    }
  }
  