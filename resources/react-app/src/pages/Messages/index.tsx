import { Paper, Table, TableBody, TableCell, TableContainer, TableHead, TableRow } from "@mui/material"
import { Navbar } from "../../components/Navbar"
import "./style.css"
import Row from "./components/Row"
import { useEffect, useState } from "react"
import { ToastContainer, toast } from "react-toastify"
import api from "../../services/axios"

const MessagesPage = () => {
	const [ messagesRows, setMessagesRows ] = useState([] as JSX.Element[])

	useEffect(() => {
		const loadingId = toast.loading('Procurando as mensagens...')

		api.get('/api/orders/order-messages')
			.then(response => response.data)
			.then(rows => {
				toast.dismiss(loadingId)
				toast.success('Mensagens recuperadas')
				setMessagesRows(Object.keys(rows).map(orderNumber => (
					<Row {...{online_order_number: orderNumber, ...rows[orderNumber]}} />
				)))
			})
			.catch(() => {
				toast.dismiss(loadingId)
				toast.error('Erro ao recuperar as mensagens', { autoClose: false })
			})
	}, [])

  return (
    <div id="messages-page">
      <Navbar items={[]} />
      <div id="messages-container">
        <TableContainer component={Paper} sx={{width: '80%', height: '90%'}}>
          <Table sx={{width: '100%'}}>
            <TableHead>
              <TableRow>
                <TableCell sx={{ textAlign: 'center', width: '15%' }}>Nº Pedido</TableCell>
                <TableCell sx={{ textAlign: 'center', width: '5%' }}>Situação</TableCell>
                <TableCell sx={{ textAlign: 'center', width: '55%' }}>Última mensagem</TableCell>
                <TableCell sx={{ textAlign: 'center', width: '15%' }}>Data</TableCell>
                <TableCell sx={{ textAlign: 'center', width: '10%' }}>Canal de venda</TableCell>
              </TableRow>
            </TableHead>
            <TableBody sx={{width: '100%'}}>
              {messagesRows}
            </TableBody>
          </Table>
        </TableContainer>
      </div>
			<ToastContainer/>
    </div>
  )
}

export default MessagesPage
