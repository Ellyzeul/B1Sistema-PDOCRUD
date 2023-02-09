import { Modal } from "@mui/material"
import { MouseEventHandler, useEffect, useState } from "react"
import { AddressModalProp, OrderAddress } from "./types"
import "./style.css"
import api from "../../../../services/axios"

const AddressModal = (props: AddressModalProp) => {
  const { orderNumber } = props
  const [isOpen, setIsOpen] = useState(false)
  const [orderAddress, setOrderAddress] = useState({} as OrderAddress)

  const handleOpen: MouseEventHandler = event => {
    event.preventDefault()
    setIsOpen(true)
  }

  const handleClose = () => setIsOpen(false)

  useEffect(() => {
    if(!isOpen || !!orderAddress.online_order_number) return
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
          <div className="address-container-block">
            <div>
              <p><strong>Nº do pedido: </strong>{orderNumber}</p>
              <p className="address-modal-amazon-address"><strong>Endereço</strong></p>
              {orderAddress.recipient_name ? <p><strong>Destinatário: </strong>{orderAddress.recipient_name}</p> : null}
              {orderAddress.address_1 ? <p><strong>Endereço 1: </strong>{orderAddress.address_1}</p> : null}
              {orderAddress.address_2 ? <p><strong>Endereço 2: </strong>{orderAddress.address_2}</p> : null}
              {orderAddress.address_3 ? <p><strong>Endereço 3: </strong>{orderAddress.address_3}</p> : null}
              {orderAddress.county ? <p><strong>Bairro: </strong>{orderAddress.county}</p> : null}
              <p>{orderAddress.online_order_number && <strong>Cidade: </strong>}
                {orderAddress.city}
                {orderAddress.state ? `, ${orderAddress.state}` : null}
                {orderAddress.postal_code ? `, ${orderAddress.postal_code}` : null}
                {orderAddress.country ? `, ${orderAddress.country}` : null}
              </p>
              {orderAddress.cellphone ? <p><strong>Celular: </strong>{orderAddress.cellphone}</p> : null}
            </div>
          </div>
          <div className="address-container-block"></div>
        </div>
      </Modal>
    </>
  )
}

export default AddressModal
