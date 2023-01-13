import { TableCell, TableRow } from "@mui/material"
import { useState } from "react"
import TrackingTableRowProp from "./types"

const TrackingTableRow = (props: TrackingTableRowProp) => {
  const { row: originalData } = props
  const [row, setData] = useState(originalData)

  return (
    <TableRow>
      <TableCell><i className="fa-solid fa-rotate"></i></TableCell>
      <TableCell>{row.tracking_code}</TableCell>
      <TableCell>{row.delivery_method}</TableCell>
      <TableCell>{row.online_order_number}</TableCell>
      <TableCell>{row.status}</TableCell>
      <TableCell>{(new Date(row.last_update_date)).toLocaleDateString('pt-BR')}</TableCell>
      <TableCell>{row.details}</TableCell>
      <TableCell>{(new Date(row.expected_date)).toLocaleDateString('pt-BR')}</TableCell>
      <TableCell>{(new Date(row.delivery_expected_date)).toLocaleDateString('pt-BR')}</TableCell>
      <TableCell>{(new Date(row.api_calling_date)).toLocaleDateString('pt-BR')}</TableCell>
      <TableCell>
        <textarea 
          defaultValue={row.observation} 
          cols={3}
          style={{
            width: '100%',
            height: '100%',
            resize: 'none'
          }}
        />
      </TableCell>
    </TableRow>
  )
}

export default TrackingTableRow
