import { toast } from "react-toastify"
import CotationMessageProp from "./types"

const CotationMessage = (props: CotationMessageProp) => {
  const { message, currency } = props

  return (
    <div className="cotation-message-container">
      <div>{currency}: </div><div className="cotation-message">
        <i className="fa-regular fa-copy cotation-message-copy" onClick={() => {
          navigator.clipboard.writeText(message)
          toast.success('Mensagem copiada!')
        }} />
        <pre>{message}</pre>
      </div>
    </div>
  )
}

export default CotationMessage
