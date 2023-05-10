import { Modal } from "@mui/material"
import { MouseEventHandler, useEffect, useRef, useState } from "react"
import { AddressModalProp, OrderAddress } from "./types"
import "./style.css"
import api from "../../../../services/axios"
import SellercentralAddress from "./SellercentralAddress"
import BlingAddress from "./BlingAddress"
import { toast } from "react-toastify"
import CotationMessage from "./CotationMessage"
import { ShipmentAndPrice } from "../../../ShipmentAndPrice"

const CURRENCIES = {
  1: {
    currency: null,
    prefix: null,
    name: null,
    amazon_link: null,
  },
  2: {
    currency: 'CAD',
    prefix: 'CA$',
    name: 'Dólar canadense',
    amazon_link: 'Amazon.ca',
  },
  3: {
    currency: 'GBP',
    prefix: '£',
    name: 'Libra esterlina',
    amazon_link: 'Amazon.co.uk',
  },
  4: {
    currency: 'USD',
    prefix: 'US$',
    name: 'Dólar americano',
    amazon_link: 'Amazon.com',
  },
  5: {
    currency: null,
    prefix: null,
    name: null,
    amazon_link: null,
  },
  6: {
    currency: null,
    prefix: null,
    name: null,
    amazon_link: null,
  },
  7: {
    currency: 'USD',
    prefix: 'US$',
    name: 'Dólar americano',
    amazon_link: 'Alibris.com',
  }
}

const AddressModal = (props: AddressModalProp) => {
  const { orderNumber, orderId } = props
  const [isOpen, setIsOpen] = useState(false)
  const [{ sellercentral, bling }, setOrderAddress] = useState({} as OrderAddress)
  const cotationDateRef = useRef(null)
  const [cotation, setCotation] = useState(1)

  const handleOpen: MouseEventHandler = event => {
    event.preventDefault()
    setIsOpen(true)
  }

  const handleClose = () => setIsOpen(false)

  const getCotation = () => {
    if(!cotationDateRef.current) return
    const cotationDateInput = cotationDateRef.current as HTMLInputElement
    const cotationDate = cotationDateInput.value.replaceAll('-', '')
    if(cotationDate === '') {
      toast.error('Insira uma data para cotação...')
      return
    }
    const { currency } = CURRENCIES[sellercentral.id_sellercentral]
    if(currency === null) {
      toast.error('Este pedido é nacional, sem cotação...')
      return
    }

    api.get(`https://economia.awesomeapi.com.br/json/daily/${currency}-BRL?start_date=${cotationDate}&end_date=${cotationDate}`)
      .then(response => response.data)
      .then(([{ ask }]) => setCotation(Math.floor(ask * 100) / 100))
      .catch(() => {
        toast.error(`Sem cotação ${currency}-BRL para este dia...`)
        setCotation(1)
    })
  }

  useEffect(() => {
    if(!isOpen || !!sellercentral) return
    api.get(`/api/orders/address/get?order_number=${orderNumber}`)
      .then(response => response.data as OrderAddress)
      .then(setOrderAddress)
  }, [isOpen])

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
            <button onClick={getCotation}>Puxar cotação</button>
          </div>
          <div className="address-modal-scrollable-container">
            <div className="address-container-block">
              {
                sellercentral
                  ? <SellercentralAddress 
                    orderNumber={orderNumber} 
                    cotation={cotation} 
                    address={sellercentral} 
                  />
                  : <p>Sem endereço Amazon...</p>
              }
            </div>
            <div className="address-container-block">
              {
                cotation !== 1
                  ? <CotationMessage 
                    cotation={cotation} 
                    cotation_date={(cotationDateRef.current as HTMLInputElement | null)?.value || ''} 
                    sellercentral={sellercentral} 
                    currency={CURRENCIES[sellercentral.id_sellercentral]} 
                  /> 
                  : null
              }
              {
                bling
                  ? <BlingAddress address={bling} />
                  : <p>Sem endereço Bling...</p>
              }
            </div>
            <div className="shipment-and-price-container"><ShipmentAndPrice orderId={orderId}/></div>
          </div>
        </div>
      </Modal>
    </>
  )
}

export default AddressModal
