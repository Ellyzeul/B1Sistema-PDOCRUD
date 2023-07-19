import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import Paper from '@mui/material/Paper';
import { SendOrderToBlingTableProp } from './types';
import { SendOrderToBlingModal } from '../SendOrderToBlingModal';

export const SendOrderToBlingTable = (props: SendOrderToBlingTableProp) => {
  const { data } = props


  const createData = (
    orderId: number,
    company: number,
    sellerChannel: string,
    origin: string,
    orderDate: string
  ) => ({ orderId, company, sellerChannel, origin, orderDate });

  const rows = data.map((item) =>
    createData(
      item.orderId,
      item.company,
      item.sellerChannel,
      item.origin,
      item.orderDate
    )
  )

  return (
  <TableContainer component={Paper}>
    <Table sx={{ minWidth: 650 }} aria-label="simple table">
      <TableHead>
        <TableRow>
          <TableCell align='center'>Detalhes</TableCell>
          <TableCell align='center'>Nº</TableCell>
          <TableCell align='center'>Empresa</TableCell>
          <TableCell align='center'>Canal de venda</TableCell>
          <TableCell align='center'>Origem</TableCell>
          <TableCell align='center'>Data do pedido</TableCell>
        </TableRow>
      </TableHead>
      <TableBody>
        {rows.map((row) => (
          <TableRow
          key={row.orderId}
          sx={{ '&:last-child td, &:last-child th': { border: 0 } }}
          >
            <TableCell align='center'>
              {<SendOrderToBlingModal orderNumber={row.origin}/>}
            </TableCell>
            <TableCell align='center'>{row.orderId}</TableCell>
            <TableCell align='center'>{row.company === 0 ? <img src="/seline_white_bg.png" width="20" height="20"/> : <img src="/b1_white_bg.png" width="20" height="20"/>}</TableCell>
            <TableCell align='center'>{row.sellerChannel}</TableCell>
            <TableCell align='center'>{row.origin}</TableCell>
            <TableCell align='center'>{row.orderDate}</TableCell>
          </TableRow>
        ))}
      </TableBody>
    </Table>
  </TableContainer>
  )
}