import { CostBenefitPrices } from "./types"
import "./style.css"

export default function CostBenefitIndex({modalState}: Prop) {
  const ratio = evaluateRatio(modalState)
  const index = evaluateIndex(ratio)
  const {hide_text} = modalState

  return (
    <div>
      {!hide_text && <>Margem da compra: {renderRatio(ratio)}</>}
      <div className={`supplier-purchase-index supplier-purchase-index-${index}`}/>
      {!hide_text && STATUSES[index]}
    </div>
  )
}

type Prop = {
  modalState: CostBenefitPrices,
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
