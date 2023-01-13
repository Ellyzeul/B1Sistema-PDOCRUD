import { TrackingTableProp } from "./types"
import { ToastContainer } from "react-toastify"
import { Table, TableBody, TableCell, TableContainer, TableHead, TableRow, Typography } from "@mui/material"
import TrackingTableRow from "./components/Row"
import "./style.css"
import TrackingTableHeadCell from "./components/HeadCell"

const TrackingTable = (props: TrackingTableProp) => {;
  const { data } = props

  return (
    <div id="tracking-table-container">
      <TableContainer>
        <Table stickyHeader>
          <TableHead>
            <TableRow>
              <TrackingTableHeadCell></TrackingTableHeadCell>
              <TrackingTableHeadCell>Rastreio</TrackingTableHeadCell>
              <TrackingTableHeadCell><i className="fa-solid fa-truck"></i></TrackingTableHeadCell>
              <TrackingTableHeadCell>ORIGEM</TrackingTableHeadCell>
              <TrackingTableHeadCell>Última ocorrência</TrackingTableHeadCell>
              <TrackingTableHeadCell>Data da ocorrência</TrackingTableHeadCell>
              <TrackingTableHeadCell>Detalhes</TrackingTableHeadCell>
              <TrackingTableHeadCell>Prazo para o cliente</TrackingTableHeadCell>
              <TrackingTableHeadCell>Prazo da transportadora</TrackingTableHeadCell>
              <TrackingTableHeadCell>Última atualização</TrackingTableHeadCell>
              <TrackingTableHeadCell>Observação</TrackingTableHeadCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {data.map(row => (
              <TrackingTableRow row={row} />
            ))}
          </TableBody>
        </Table>
      </TableContainer>
      <ToastContainer/>
    </div>
  )
}

export default TrackingTable
