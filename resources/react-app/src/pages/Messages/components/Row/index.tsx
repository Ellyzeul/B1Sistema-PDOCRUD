import { SxProps, TableCell, TableRow } from "@mui/material"
import { RowProp } from "./types"
import MessageModal from "../MessageModal"
import { useState } from "react"

const Row = (props: RowProp) => {
  const { online_order_number, messages, sellercentral, company } = props
  const [ isModalOpen, setIsModalOpen ] = useState(false)
  const latestMessage = messages.reduce((acc, cur) => acc.date > cur.date ? acc : cur)
  const isAnswered = latestMessage.from === 'seller'

  return (
    <>
      <TableRow sx={getRowStyle(isAnswered)} onClick={() => setIsModalOpen(true)}>
        <TableCell>{online_order_number}</TableCell>
        <TableCell>{isAnswered ? 'Respondida' : 'Pendente'}</TableCell>
        <TableCell>
          <div className="message-text">{latestMessage.text}</div>
        </TableCell>
        <TableCell sx={{ textAlign: 'center' }}>
          {(new Date(latestMessage.date)).toLocaleString()}
        </TableCell>
        <TableCell align="center">
          <img src={sellercentralIcons[sellercentral]} alt={sellercentral} style={{ width: '24px' }} />
          <img src={companiesIcons[company]} alt={company} style={{ width: '24px' }} />
        </TableCell>
      </TableRow>
      <MessageModal isOpen={isModalOpen} handleClose={() => setIsModalOpen(false)} messages={messages} />
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

const sellercentralIcons = {
  'mercado-livre': '/icons/sellercentrals/mercado-livre.png', 
  'fnac': '/icons/sellercentrals/fnac.png', 
} as { [key: string]: string }

const companiesIcons = {
  'seline': '/seline_white_bg.png', 
  'b1': '/b1_logo.png'
} as { [key: string]: string }
