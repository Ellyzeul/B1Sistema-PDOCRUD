import getCurrentCurrencyCotation from "./getCurrencyCotation"

export default async function getBRLPrice(price?: number, currencyCode?: string, asString: boolean = true) {
  if(!price || !currencyCode) return undefined
  if(currencyCode === 'BRL') return asString ? String(price).replace('.', ',') : price

  const cotation = await getCurrentCurrencyCotation(currencyCode)
  if(cotation === -1) return 'Erro na cotação'
  const converted = (price * cotation)

  return asString ? converted.toFixed(2).replace('.', ',') : converted
}