import { useEffect, useState } from "react"
import { toast } from "react-toastify"
import CotationMessageProp from "./types"

const CotationMessage = (props: CotationMessageProp) => {
  const { cotation, cotation_date, sellercentral, currency } = props
  const [message, setMessage] = useState('')

  const generateCotationMessage = () => {
    const truncateNumber = (num: number) => Math.floor(num * 100) / 100
    const truncateFormatted = (num: number) => String(truncateNumber(num)).replace('.', ',')
    const { online_order_number, price, freight, item_tax, freight_tax } = sellercentral
    const tax = truncateNumber(Number(item_tax) + Number(freight_tax))
    const subtotal = truncateNumber(Number(price) + Number(freight) + tax)
    const subtotalBRL = truncateFormatted(Math.round(truncateNumber((Number(price) * cotation) + truncateNumber(Number(freight) * cotation) + truncateNumber(tax * cotation)) * 100) / 100)
    const cotationDate = (new Date(`${cotation_date} 00:00`)).toLocaleDateString('pt-BR')

    const { prefix, name: currencyName, amazon_link } = currency

    return (
`Nº Pedido Loja: ${online_order_number}
BOOK // ${amazon_link} //
Item 1 - ${prefix} ${price} = R$${truncateFormatted(Number(price) * cotation)}  // Frete - ${prefix} ${freight} = R$ ${truncateFormatted(Number(freight) * cotation)}
TAX = ${prefix} ${tax} = R$ ${truncateFormatted(tax * cotation)}
Subtotal ${prefix}  ${subtotal} = R$ ${subtotalBRL}
Data da Compra ${cotationDate.replaceAll('/', '.')} // ${currencyName} do Dia R$ ${String(cotation).replace('.', ',')}`
    )
  }

  useEffect(() => setMessage(generateCotationMessage()))

  return (
    <div className="cotation-message-container">
      <div className="cotation-message">
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
