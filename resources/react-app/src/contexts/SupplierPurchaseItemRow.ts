import { createContext } from "react";

const SupplierPurchaseItemRowContext = createContext({
  tableRows: [],
  setTableRows: () => {},
} as Context)

export default SupplierPurchaseItemRowContext

type Context = {
  tableRows: Array<JSX.Element>,
  setTableRows: (tableRows: Array<JSX.Element>) => void
}
