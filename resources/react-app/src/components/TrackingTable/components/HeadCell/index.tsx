import { TableCell } from "@mui/material"
import TrackingTableHeadCellProp from "./types"

const TrackingTableHeadCell = (props: TrackingTableHeadCellProp) => {
  const { children } = props

  return (
    <TableCell
      align="center" 
      size="small" 
      padding="none"
    >
      {props.children}
    </TableCell>
  )
}

export default TrackingTableHeadCell
