import { SxProps, TableCell, TableRow, Select, MenuItem } from "@mui/material"
import { RowProp } from "./types"
import MessageModal from "../MessageModal"
import { useState } from "react"
import getSellercentralIcon from "../common/getSellercentralIcon"
import getCompanyIcon from "../common/getCompanyIcon"
import React from 'react';
import { useRowData } from "../../context"

// const Row = (props: RowProp) => {
//   const { online_order_number, messages, type, sellercentral, company, to_answer } = props
//   const [ isModalOpen, setIsModalOpen ] = useState(false)
//   const latestMessage = messages.reduce((acc, cur) => acc.date > cur.date ? acc : cur)
//   const messageType = type === 'offer' ? 'Anúncio' : 'Pedido'
//   const isAnswered = latestMessage.from === 'seller'

//   return (
//     <>
//       <TableRow sx={getRowStyle(isAnswered)} onClick={() => setIsModalOpen(true)}>
//         <TableCell>{online_order_number}</TableCell>
//         <TableCell>{isAnswered ? 'Respondida' : 'Pendente'}</TableCell>
//         <TableCell>{messageType}</TableCell>
//         <TableCell>
//           <div className="message-text">{latestMessage.text}</div>
//         </TableCell>
//         <TableCell sx={{ textAlign: 'center' }}>
//           {(new Date(latestMessage.date)).toLocaleString()}
//         </TableCell>
//         <TableCell align="center">
//           <img src={getSellercentralIcon(sellercentral)} alt={sellercentral} style={{ width: '24px' }} />
//           <img src={getCompanyIcon(company)} alt={company} style={{ width: '24px' }} />
//         </TableCell>
//       </TableRow>
//       <MessageModal 
//         isOpen={isModalOpen} 
//         messages={messages} 
//         online_order_number={online_order_number} 
//         sellercentral={sellercentral} 
//         company={company} 
//         type={type} 
//         to_answer={to_answer} 
//         handleClose={() => setIsModalOpen(false)} 
//       />
//     </>
//   )
// }

const Row = (props: RowProp) => {
  const [ isModalOpen, setIsModalOpen ] = useState(false)
  const { setSelectedRow } = useRowData()
  const { 
    id, 
    type, 
    online_order_number, 
    has_attachments, 
    id_company, 
    id_sellercentral, 
    observation, 
    situation, 
    timestamp } = props;

  const handleRowClick = () => {
    setSelectedRow({id, online_order_number, id_sellercentral})
    // setIsModalOpen(true);
  }

  return (
    <>
    <TableRow sx={getRowStyle(situation)} onClick={() => handleRowClick()}>
      <TableCell>{id}</TableCell>
      <TableCell>{online_order_number}</TableCell>
      <TableCell>
        <Select value={situation} onChange={() => console.log("change")} sx={{ height: '24px' }}>
          <MenuItem value="Pendente">Pendente</MenuItem>
          <MenuItem value="Respondido previamente">Respondido previamente</MenuItem>
          <MenuItem value="Respondido">Respondido</MenuItem>
          <MenuItem value="Finalizado">Finalizado</MenuItem>
        </Select>
      </TableCell>
      <TableCell>{type}</TableCell>
      <TableCell>{timestamp}</TableCell>
      <TableCell>{has_attachments == 0 ? "Não" : has_attachments}</TableCell>
      <TableCell>{observation}</TableCell>
    </TableRow>
    <MessageModal 
      isOpen={isModalOpen} 
      messages={[]} 
      online_order_number={online_order_number} 
      sellercentral={""} 
      company={id_company === 1 ? 'seline': 'b1'} 
      type={"order"} 
      to_answer={""} 
      handleClose={() => setIsModalOpen(false)} 
    />    
    </>
  )
}

export default Row

const getRowStyle = (situation: string): SxProps => ({ 
  opacity: situation === "Respondido" || situation === "Finalizado" ? 0.6 : 1, 
  width: '100%', 
  userSelect: 'none', 
  cursor: 'pointer', 
  transition: '125ms', 
  ":hover": {
    backgroundColor: 'rgba(0, 0, 0, 0.1)'
  }
})