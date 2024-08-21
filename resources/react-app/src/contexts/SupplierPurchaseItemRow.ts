import { createContext } from "react";
import { CostBenefitPrices } from "../components/SupplierPurchaseModal/CostBenefitIndex/types";

const SupplierPurchaseItemRowContext = createContext({
  tableRows: [],
  setTableRows: () => {},
  prices: {items: {}, freight: 0, selling_price: {}},
  setPrices: () => {},
} as Context)

export default SupplierPurchaseItemRowContext

type Context = {
  tableRows: Array<JSX.Element>,
  setTableRows: (tableRows: Array<JSX.Element>) => void,
  prices: CostBenefitPrices,
  setPrices: (prices: CostBenefitPrices) => void,
}
