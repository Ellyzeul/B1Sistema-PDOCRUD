import { CostBenefitPrices } from "./types"
import "./style.css"

export default function CostBenefitIndex({prices}: Prop) {
  const ratio = evaluateRatio(prices)
  const index = evaluateIndex(ratio)

  return (
    <div>
      Margem da compra: {renderRatio(ratio)}
      <div className={`supplier-purchase-index supplier-purchase-index-${index}`}/>
      {STATUSES[index]}
    </div>
  )
}

type Prop = {
  prices: CostBenefitPrices,
}

const STATUSES: Record<string, string> = {
  blank: '--',
  caution: 'Cuidado',
  bad: 'Pedir autorização',
  good: 'Razoável - dentro do esperado',
  perfect: 'Ideal - parabéns',
}

function evaluateRatio({selling_price, items, freight}: CostBenefitPrices) {
  const result = sumRecord(selling_price) / (freight + sumRecord(items))

  return isFinite(result) ? result : 0
}

function evaluateIndex(ratio: number) {
  if(ratio >= 2.5) return 'perfect'
  else if(ratio >= 2) return 'good'
  else if(ratio >= 1.6) return 'caution'
  else if(ratio > 0) return 'bad'
  else return 'blank'
}

function sumRecord(record: Record<number, number>) {
  return Object.keys(record)
    .map(id => record[Number(id)])
    .reduce((acc, cur) => acc + cur, 0)
}

function renderRatio(ratio: number) {
  return ratio > 0
    ? ratio.toFixed(2).replace('.', '.')
    : '--'
}
