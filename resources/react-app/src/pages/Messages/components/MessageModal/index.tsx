import { Modal } from "@mui/material"
import { MessageModalProp } from "./type"
import "./style.css"
import { useRef, useState } from "react"
import { Message } from "../Row/types"
import api from "../../../../services/axios"
import { toast } from "react-toastify"

const MessageModal = (props: MessageModalProp) => {
  const { isOpen, messages, handleClose } = props
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
      date: Date().toLocaleString()
    }
    const mappedElement = mapMessageToElement(message)

    setMessagesElements([ mappedElement, ...messagesElements ])

    api.post('/api/orders/order-message')
      .then(response => response.data)
      .then(response => {
        if(response.success) return

        handleSendError(mappedElement)
      })
      .catch(() => handleSendError(mappedElement))
  }

  return (
    <Modal className='message-modal' open={isOpen} onClose={handleClose}>
      <div className="messages-modal-container">
        <i className="fa-solid fa-xmark messages-modal-close" onClick={handleClose}/>
        <div className="messages-modal-chat">{messagesElements}</div>
        <div className="messages-modal-input-container">
          <textarea ref={textareaRef} className="messages-modal-input" />
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
