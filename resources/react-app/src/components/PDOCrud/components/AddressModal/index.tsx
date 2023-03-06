import { Modal } from "@mui/material"
import { MouseEventHandler, useEffect, useRef, useState } from "react"
import { AddressModalProp, OrderAddress } from "./types"
import "./style.css"
import api from "../../../../services/axios"
import SellercentralAddress from "./SellercentralAddress"
import BlingAddress from "./BlingAddress"
import { toast } from "react-toastify"
import CotationMessage from "./CotationMessage"

const CURRENCIES = ['USD', 'CAD', 'EUR']

const AddressModal = (props: AddressModalProp) => {
  const { orderNumber } = props
  const [isOpen, setIsOpen] = useState(false)
  const [{ sellercentral, bling }, setOrderAddress] = useState({} as OrderAddress)
  const cotationDateRef = useRef(null)
  const [cotations, setCotations] = useState({} as {[key: string]: number | null})

  const handleOpen: MouseEventHandler = event => {
    event.preventDefault()
    setIsOpen(true)
  }

  const handleClose = () => setIsOpen(false)

  const getCotations = async () => {
    if(!cotationDateRef.current) return
    const cotationDateInput = cotationDateRef.current as HTMLInputElement
    const cotationDate = cotationDateInput.value.replaceAll('-', '')
    if(cotationDate === '') {
      toast.error('Insira uma data para cotação...')
      return
    }
    const toUpdate = {} as {[key: string]: number | null}

    CURRENCIES.forEach((currency) => {
      api.get(`https://economia.awesomeapi.com.br/json/daily/${currency}-BRL?start_date=${cotationDate}&end_date=${cotationDate}`)
        .then(response => response.data)
        .then(([{ ask }]) => toUpdate[currency] = ask as number)
        .catch(() => {
          toast.error(`Sem cotação ${currency}-BRL para este dia...`)
          toUpdate[currency] = null
        })
        .then(() => {
          if(Object.keys(toUpdate).length !== CURRENCIES.length) return
          setCotations(toUpdate)
        })
    })
  }

  const generateCADMessage = () => {
    if(!cotationDateRef.current) return ''
    const truncateNumber = (num: number) => Math.floor(num * 100) / 100
    const { online_order_number, price, freight, item_tax, freight_tax } = sellercentral
    const tax = truncateNumber(Number(item_tax) + Number(freight_tax))
    const subtotal = truncateNumber(Number(price) + Number(freight) + tax)
    const cotation = truncateNumber(Number(cotations['CAD']))
    const input = cotationDateRef.current as HTMLInputElement
    const cotationDate = (new Date(`${input.value} 00:00`)).toLocaleDateString('pt-BR')

    return (
`Nº Pedido Loja: ${online_order_number}
BOOK // Amazon.ca //
Item 1 - CA$ ${price} = R$${truncateNumber(Number(price) * cotation)}  // Frete - CA$ ${freight} = R$ ${truncateNumber(Number(freight) * cotation)}
TAX = CA$ ${tax} = R$ ${truncateNumber(tax * cotation)}
Subtotal CA$  ${subtotal} = R$ ${truncateNumber(subtotal * cotation)}
Data da Compra ${cotationDate.replaceAll('/', '.')} // Dólar do Dia R$ ${cotation}`
    )
  }

  const generateUSDMessage = () => {
    if(!cotationDateRef.current) return ''
    const truncateNumber = (num: number) => Math.floor(num * 100) / 100
    const { online_order_number, price, freight, item_tax, freight_tax } = sellercentral
    const tax = truncateNumber(Number(item_tax) + Number(freight_tax))
    const subtotal = truncateNumber(Number(price) + Number(freight) + tax)
    const cotation = truncateNumber(Number(cotations['USD']))
    const input = cotationDateRef.current as HTMLInputElement
    const cotationDate = (new Date(`${input.value} 00:00`)).toLocaleDateString('pt-BR')

    return (
`Nº Pedido Loja: ${online_order_number}
BOOK // Amazon.com //
Item 1 - US$ ${price} = R$${truncateNumber(Number(price) * cotation)}  // Frete - U$ ${freight} = R$ ${truncateNumber(Number(freight) * cotation)}
TAX = US$ ${tax} = R$ ${truncateNumber(tax * cotation)}
Subtotal US$  ${subtotal} = R$ ${truncateNumber(subtotal * cotation)}
Data da Compra ${cotationDate.replaceAll('/', '.')} // Dólar do Dia R$ ${cotation}`
    )
  }

  const generateEURMessage = () => {
    if(!cotationDateRef.current) return ''
    const truncateNumber = (num: number) => Math.floor(num * 100) / 100
    const { online_order_number, price, freight, item_tax, freight_tax } = sellercentral
    const tax = truncateNumber(Number(item_tax) + Number(freight_tax))
    const subtotal = truncateNumber(Number(price) + Number(freight) + tax)
    const cotation = truncateNumber(Number(cotations['EUR']))
    const input = cotationDateRef.current as HTMLInputElement
    const cotationDate = (new Date(`${input.value} 00:00`)).toLocaleDateString('pt-BR')

    return (
`Nº Pedido Loja: ${online_order_number}
BOOK // Amazon.co.uk //
Item 1 - € ${price} = R$${truncateNumber(Number(price) * cotation)}  // Frete - € ${freight} = R$ ${truncateNumber(Number(freight) * cotation)}
TAX = € ${tax} = R$ ${truncateNumber(tax * cotation)}
Subtotal €  ${subtotal} = R$ ${truncateNumber(subtotal * cotation)}
Data da Compra ${cotationDate.replaceAll('/', '.')} // Euro do Dia R$ ${cotation}`
    )
  }

  useEffect(() => {
    if(!isOpen || !!sellercentral) return
    api.get(`/api/orders/address/get?order_number=${orderNumber}`)
      .then(response => response.data as OrderAddress)
      .then(setOrderAddress)
  }, [isOpen])

  useEffect(() => {
    console.log(cotations)
  }, [cotations])

  return (
    <>
      <button
        className="open-address-modal"
        onClick={handleOpen}
      >
        <i className="address-modal-btn fa-solid fa-house"></i>
      </button>
      <Modal
        className='address-modal' 
        open={isOpen} 
        onClose={handleClose} 
      >
        <div className="address-modal-container">
          <div className="close-address-modal" onClick={handleClose}>
            <i className="fa-solid fa-xmark"></i>
          </div>
          <div className="dolar-cotation-widget">
            <input ref={cotationDateRef} type="date"/>
            <button onClick={getCotations}>Puxar cotação</button>
          </div>
          <div className="address-modal-scrollable-container">
            <div className="address-container-block">
              {
                sellercentral
                  ? <SellercentralAddress orderNumber={orderNumber} address={sellercentral} />
                  : <p>Sem endereço Amazon...</p>
              }
            </div>
            <div className="address-container-block">
              {cotations['USD'] ? <CotationMessage message={generateUSDMessage()} currency="USD" /> : null}
              {cotations['CAD'] ? <CotationMessage message={generateCADMessage()} currency="CAD" /> : null}
              {cotations['EUR'] ? <CotationMessage message={generateEURMessage()} currency="EUR" /> : null}
              {
                bling
                  ? <BlingAddress address={bling} />
                  : <p>Sem endereço Bling...</p>
              }
            </div>
          </div>
        </div>
      </Modal>
    </>
  )
}

export default AddressModal
