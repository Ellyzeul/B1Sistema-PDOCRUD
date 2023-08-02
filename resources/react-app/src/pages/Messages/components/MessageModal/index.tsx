import { Modal, TextareaAutosize } from "@mui/material"
import { MessageModalProp } from "./type"
import "./style.css"
import { useRef, useState } from "react"
import { Message } from "../Row/types"
import api from "../../../../services/axios"
import { toast } from "react-toastify"
import getSellercentralIcon from "../common/getSellercentralIcon"
import getCompanyIcon from "../common/getCompanyIcon"

const MessageModal = (props: MessageModalProp) => {
  const { isOpen, messages, online_order_number, sellercentral, company, to_answer, type, handleClose } = props
  const [ messagesElements, setMessagesElements ] = useState(messages.map(mapMessageToElement))
  const textareaRef = useRef(null as HTMLTextAreaElement | null)

  const handleSendError = (mappedElement: JSX.Element) => {
    toast.error('Ocorreu algum erro ao enviar a mensagem...', { autoClose: false })
    setMessagesElements(messagesElements.filter(element => element !== mappedElement))
  }

  const handleSend = () => {
    if(!textareaRef.current) return
    const textarea = textareaRef.current
    const message: Message = {
      text: textarea.value, 
      from: 'seller', 
      date: (new Date(Date.now())).toLocaleString()
    }
    const requestBody = {
      sellercentral: sellercentral, 
      company: company, 
      text: textarea.value, 
      to_answer: to_answer, 
      message_type: type,
    }
    const mappedElement = mapMessageToElement(message)

    setMessagesElements([ mappedElement, ...messagesElements ])

    api.post('/api/orders/order-message', requestBody)
      .then(response => response.data)
      .then(response => {
        console.log(response)
        if(response.success) return

        handleSendError(mappedElement)
      })
      .catch(() => handleSendError(mappedElement))
  }

  return (
    <Modal className='message-modal' open={isOpen} onClose={handleClose}>
      <div className="messages-modal-container">
        <i className="fa-solid fa-xmark messages-modal-close" onClick={handleClose}/>
        <div className="messages-modal-chat">
          <div style={{ display: 'flex', marginTop: '20px', justifyContent: 'space-evenly' }}>
            <div>
              <img src={getSellercentralIcon(sellercentral)} alt={sellercentral} style={{ width: '24px' }} />
              <img src={getCompanyIcon(company)} alt={company} style={{ width: '24px' }} />
            </div>
            <div>Pedido: {online_order_number}</div>
            <div style={{ display: 'flex', flexDirection: 'column' }}>
              <a href={getOrderLink(sellercentral, online_order_number)}>Canal de venda</a>
              <a href={`/pedidos?order_number=${online_order_number}`} target="blank">Pedido no sistema</a>
            </div>
          </div>
          {messagesElements}
        </div>
        <div className="messages-modal-input-container">
          <TextareaAutosize 
            ref={textareaRef} 
            className="messages-modal-input" 
            minRows={3}
            maxRows={9} 
            style={{ resize: 'none' }}
          />
          <button className="messages-modal-send-message" onClick={handleSend}>Enviar</button>
        </div>
      </div>
    </Modal>
  )
}

export default MessageModal

let elementKey = 0

const mapMessageToElement = ({ text, from, date }: Message) => (
  <div className={`message-from-${from}`} key={elementKey++}>
    <div className="message"><p>{text}</p></div>
    <div className="message-date">{(new Date(date)).toLocaleString()}</div>
  </div>
)

const getOrderLink = (sellercentralName: string, onlineOrderNumber: string) => {
  if(sellercentralName === 'fnac') return `https://seller.fnac.pt/compte/vendeur/commande/${onlineOrderNumber}`
  if(sellercentralName === 'mercado-livre') return `https://www.mercadolivre.com.br/vendas/${onlineOrderNumber}/detalhe?callbackUrl=https%3A%2F%2Fwww.mercadolivre.com.br%2Fvendas%2Fomni%2Flista%3Fplatform.id%3DML%26channel%3Dmarketplace%26filters%3D%26sort%3DDATE_CLOSED_DESC%26page%3D1%26search%3D%26startPeriod%3DWITH_DATE_CLOSED_6M_OLD%26toCurrent%3D%26fromCurrent%3D`
}
