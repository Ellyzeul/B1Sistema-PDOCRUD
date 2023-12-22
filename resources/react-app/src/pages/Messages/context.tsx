import { createContext, useContext, useState } from "react"

type SelectedRowData = {
  id: number,
  online_order_number: string,
  id_sellercentral: number,
}

type RowDataContextType = {
  selectedRowData: SelectedRowData | null,
  setSelectedRow: (rowData: SelectedRowData | null) => void,
}

export const RowDataContext = createContext<RowDataContextType | null>(null)

export const RowDataProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [selectedRowData, setSelectedRowData] = useState<SelectedRowData | null>(null)

  const setSelectedRow = (rowData: SelectedRowData | null) => {
    setSelectedRowData(rowData)
  }

  const contextValue: RowDataContextType = {
    selectedRowData,
    setSelectedRow,
  }

  return(
    <RowDataContext.Provider value={contextValue}>
      {children}
    </RowDataContext.Provider>
  )
}

export const useRowData = (): RowDataContextType => {
  const context = useContext(RowDataContext)
  if(!context) throw new Error("useRowData must be used within a RowDataProvider")

  return context
}