import { Paper, Table, TableBody, TableCell, TableContainer, TableHead, TablePagination, TableRow } from "@mui/material"
import { Navbar } from "../../components/Navbar"
import "./style.css"
import Row from "./components/Row"
import { useEffect, useRef, useState } from "react"
import { ToastContainer, toast } from "react-toastify"
import api from "../../services/axios"
import { useRowData } from "./context"
import { OrderInfo } from "./components/OrderInfo"
import { OrderInfoProps } from "./components/OrderInfo/types"
import MessageModal from "./components/MessageModal"

const MessagesPage = () => {
	const [ messagesRows, setMessagesRows ] = useState([] as JSX.Element[])
  const [selectedButton, setSelectedButton] = useState<string | null>(null)
  const [ isModalOpen, setIsModalOpen ] = useState(false)
  const [ orderInfo, setOrderInfo ] = useState<OrderInfoProps | null>(null)
  const [rowsPerPage, setRowsPerPage] = useState(20) 
  const [error, setError] = useState<boolean>()
  const { selectedRowData } = useRowData()
  const [page, setPage] = useState(0)
  const buttonsRef = useRef(null)


  const buttons = [
    {label: "Mercado Livre", key: "mercado-livre-0", icon: "/seline_white_bg.png"},
    {label: "FNAC-PT", key: "fnac-pt-0", icon: "/seline_white_bg.png"},
    {label: "FNAC-ES", key: "fnac-es-0", icon: "/seline_white_bg.png"},
    {label: "FNAC-FR", key: "fnac-fr-0", icon: "/seline_white_bg.png"},
    {label: "Nuvemshop/Seline", key: "nuvemshop", icon: "/icons/sellercentrals/nuvemshop.png"},
    {label: "Mercado Livre", key: "mercado-livre-1", icon: "/b1_white_bg.png"},
  ]

  const getTicketsData = (sellercentral: string) =>{
    const sellercentral_label = (buttons.find(
      (button) => button.key === sellercentral
    ))?.label ?? sellercentral

    const loadingId = toast.loading('Procurando as mensagens...')
		api.get(`/api/orders/order-messages?sellercentral=${sellercentral}`)
      .then(response => response.data)
			.then(rows => {
				toast.dismiss(loadingId)
				toast.success('Mensagens recuperadas')
        console.log(rows)
				setMessagesRows(Object.keys(rows).map(orderNumber => (
					<Row {...{online_order_number: orderNumber, ...rows[orderNumber]}} />
				)))
			})  
			.catch(() => {
				toast.dismiss(loadingId)
				toast.error(`${sellercentral_label} - Erro ao recuperar as mensagens `, { autoClose: false })
			})       
  }

  const handleButtonClick = (key: string) => {
    setSelectedButton(key)
    getTicketsData(key)
  }

  useEffect(() => {
    if(!selectedRowData) return 
    const { id, online_order_number, id_sellercentral } = selectedRowData

    api.get(`/api/orders/order-message-chat?order_ticket=${id}&online_order_number=${online_order_number}`)
      .then(response => response.data)
      .then((response) => {
        console.log(response.order_info)
        setOrderInfo(response.order_info)
        setError(false)
        setIsModalOpen(true)
      })
      .catch((err) => {
        setError(true)
        setIsModalOpen(false)
				toast.error(`Erro ao recuperar chat de mensagens`)
        console.log(err)
			})  
  }, [selectedRowData])

  return (
    <div className="messages-page">
      <Navbar items={[]} />
      <div className="page-container">
        <div className="msg-buttons">
          <div ref={buttonsRef} className="sellercentrals-btn-container">
            {buttons.map((button) => (
            <button key={button.key} 
              onClick={() => handleButtonClick(button.key)}
              className={selectedButton === button.key ? 'selected-btn' : ''}
            >
              {button.icon && <img src={button.icon} alt={`${button.label} Icon`} />} 
              {button.label}
              </button> 
            ))}
          </div>
        </div>
        <div className="central-container borders">
          <div className="table-container">
          <TableContainer component={Paper} sx={{width: '100%', maxWidth: '100%'}}>
          <Table sx={{width: '100%'}} size={'small'}>
          {/* <colgroup>
              <col style={{width:'10%'}}/>
              <col style={{width:'10%'}}/>
              <col style={{width:'5%'}}/>
              <col style={{width:'5%'}}/>
              <col style={{width:'20%'}}/>
              <col style={{width:'20%'}}/>
              <col style={{width:'30%'}}/>
          </colgroup> */}
            <TableHead>
              <TableRow>
              <TableCell sx={{ textAlign: 'center', width: '15%' }}>Nº</TableCell>
                <TableCell sx={{ textAlign: 'center', width: '15%' }}>Pedido</TableCell>
                <TableCell sx={{ textAlign: 'center', width: '5%' }}>Situação</TableCell>
                <TableCell sx={{ textAlign: 'center', width: '5%' }}>Tipo</TableCell>
                <TableCell sx={{ textAlign: 'center', width: '15%' }}>Data</TableCell>
                <TableCell sx={{ textAlign: 'center', width: '15%' }}>Anexo</TableCell>
                <TableCell sx={{ textAlign: 'center', width: '10%' }}>Observação</TableCell>
              </TableRow>
            </TableHead>
            <TableBody sx={{width: '100%'}}>
              {messagesRows.length === 0 
                ? generalTableRowMessage()
                : messagesRows.slice(page * rowsPerPage, (page + 1) * rowsPerPage)}
            </TableBody>
          </Table>
            <TablePagination
                rowsPerPageOptions={[20, 25, 30]}
                component="div"
                count={messagesRows.length}
                rowsPerPage={rowsPerPage}
                page={page}
                onPageChange={(event, newPage) => {
                  setPage(newPage);
                }}
                onRowsPerPageChange={(event) => {
                  setRowsPerPage(parseInt(event.target.value, 20));
                  setPage(0);
                }}
              />
        </TableContainer>
          </div>
          {/* <div className="obs-container">
            <strong>Análise interna</strong>
            <button className="save-observation">Salvar</button>
            <textarea name="observation" cols={130} rows={20}></textarea>
          </div> */}
        </div>
        { orderInfo && !error ? <OrderInfo {...orderInfo as OrderInfoProps}/> : null }
        <MessageModal 
          isOpen={isModalOpen} 
          messages={[]} 
          online_order_number={orderInfo?.online_order_number || ""}
          sellercentral={orderInfo?.sellercentral || ""} 
          company={orderInfo?.id_company === '1' ? 'seline': 'b1'} 
          type={orderInfo?.type || ""} 
          to_answer={""} 
          handleClose={() => setIsModalOpen(false)} 
        />   
      </div>
      <ToastContainer/>
    </div>
  )
}

const generalTableRowMessage = () => {
  return (
    <TableRow>
      <TableCell colSpan={7} sx={{ textAlign: 'center'}}>Navegue pelos botões para carregar as mensagens de cada canal de venda</TableCell>
    </TableRow>
  )
}

export default MessagesPage
