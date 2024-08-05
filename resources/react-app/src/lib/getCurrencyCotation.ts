import api from "../services/axios";

export default async function getCurrentCurrencyCotation(currencyCode: string): Promise<number> {
  const cotationDate = new Date().toISOString().split('T')[0].replaceAll('-', '')
  const cotationKey = `${currencyCode}-${cotationDate}`
  
  if(!COTATIONS[cotationKey] || COTATIONS[cotationKey] === -1) {
    COTATIONS[cotationKey] = await api
      .get(`https://economia.awesomeapi.com.br/json/daily/${currencyCode}-BRL?start_date=${cotationDate}&end_date=${cotationDate}`)
      .then(response => response.data)
      .then(([{ask}]) => ask)
      .catch(() => -1)
  }

  return COTATIONS[cotationKey]
}

const COTATIONS: Record<string, number> = {}
