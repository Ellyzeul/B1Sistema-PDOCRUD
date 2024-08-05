export default function getCurrencyFromSellercentral(idSellercentral: number) {
  return idSellercentral in CURRENCIES
    ? CURRENCIES[idSellercentral]
    : null
}

const CURRENCIES: Record<number, {name: string, symbol: string, code: string}> = {
  1: {name: 'Real', symbol: 'R$', code:'BRL'},
  2: {name: 'Dólar canadense', symbol: 'CA$', code: 'CAD'},
  3: {name: 'Libra', symbol: '£', code: 'GBP'},
  4: {name: 'Dólar', symbol: 'US$', code: 'USD'},
  5: {name: 'Real', symbol: 'R$', code:'BRL'},
  6: {name: 'Real', symbol: 'R$', code:'BRL'},
  7: {name: 'Dólar', symbol: 'US$', code: 'USD'},
  8: {name: 'Euro', symbol: '€', code: 'EUR'},
  9: {name: 'Real', symbol: 'R$', code:'BRL'},
  10: {name: 'Real', symbol: 'R$', code:'BRL'},
  11: {name: 'Euro', symbol: '€', code: 'EUR'},
  12: {name: 'Euro', symbol: '€', code: 'EUR'},
}
