export type CostBenefitPrices = {
  selling_price: Record<number, number>,
  items: Record<number, number>,
  freight: number,
  id_company: number,
  hide_text?: boolean,
}