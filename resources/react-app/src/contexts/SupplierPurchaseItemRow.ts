import { createContext } from "react";
import { CostBenefitPrices } from "../components/SupplierPurchaseModal/CostBenefitIndex/types";

const SupplierPurchaseItemRowContext = createContext({
  tableRows: [],
  setTableRows: () => {},
  modalState: {items: {}, freight: 0, selling_price: {}, id_company: 0},
  setModalState: () => {},
} as Context)

export default SupplierPurchaseItemRowContext

type Context = {
  tableRows: Array<JSX.Element>,
  setTableRows: (tableRows: Array<JSX.Element>) => void,
  modalState: CostBenefitPrices,
  setModalState: (prices: CostBenefitPrices) => void,
}
