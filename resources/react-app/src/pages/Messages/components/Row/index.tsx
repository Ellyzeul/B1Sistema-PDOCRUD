import { SxProps, TableCell, TableRow } from "@mui/material"
import { RowProp } from "./types"
import MessageModal from "../MessageModal"
import { useState } from "react"
import getSellercentralIcon from "../common/getSellercentralIcon"
import getCompanyIcon from "../common/getCompanyIcon"

const Row = (props: RowProp) => {
  const { online_order_number, messages, type, sellercentral, company, to_answer } = props
  const [ isModalOpen, setIsModalOpen ] = useState(false)
  const latestMessage = messages.reduce((acc, cur) => acc.date > cur.date ? acc : cur)
  const messageType = type === 'offer' ? 'Anúncio' : 'Pedido'
  const isAnswered = latestMessage.from === 'seller'

  return (
    <>
      <TableRow sx={getRowStyle(isAnswered)} onClick={() => setIsModalOpen(true)}>
        <TableCell>{online_order_number}</TableCell>
        <TableCell>{isAnswered ? 'Respondida' : 'Pendente'}</TableCell>
        <TableCell>{messageType}</TableCell>
        <TableCell>
          <div className="message-text">{latestMessage.text}</div>
        </TableCell>
        <TableCell sx={{ textAlign: 'center' }}>
          {(new Date(latestMessage.date)).toLocaleString()}
        </TableCell>
        <TableCell align="center">
          <img src={getSellercentralIcon(sellercentral)} alt={sellercentral} style={{ width: '24px' }} />
          <img src={getCompanyIcon(company)} alt={company} style={{ width: '24px' }} />
        </TableCell>
      </TableRow>
      <MessageModal 
        isOpen={isModalOpen} 
        messages={messages} 
        online_order_number={online_order_number} 
        sellercentral={sellercentral} 
        company={company} 
        to_answer={to_answer} 
        handleClose={() => setIsModalOpen(false)} 
      />
    </>
  )
}

export default Row

const getRowStyle = (isAnswered: boolean): SxProps => ({ 
  opacity: isAnswered ? 0.6 : 1, 
  width: '100%', 
  userSelect: 'none', 
  cursor: 'pointer', 
  transition: '125ms', 
  ":hover": {
    backgroundColor: 'rgba(0, 0, 0, 0.1)'
  }
})
