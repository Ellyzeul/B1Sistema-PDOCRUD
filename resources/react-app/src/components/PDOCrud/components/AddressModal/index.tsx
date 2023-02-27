import { Modal } from "@mui/material"
import { MouseEventHandler, useEffect, useState } from "react"
import { AddressModalProp, OrderAddress } from "./types"
import "./style.css"
import api from "../../../../services/axios"
import SellercentralAddress from "./SellercentralAddress"
import BlingAddress from "./BlingAddress"

const AddressModal = (props: AddressModalProp) => {
  const { orderNumber } = props
  const [isOpen, setIsOpen] = useState(false)
  const [{ sellercentral, bling }, setOrderAddress] = useState({} as OrderAddress)

  const handleOpen: MouseEventHandler = event => {
    event.preventDefault()
    setIsOpen(true)
  }

  const handleClose = () => setIsOpen(false)

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
          <div className="address-container-block">
            {
              sellercentral
                ? <SellercentralAddress orderNumber={orderNumber} address={sellercentral} />
                : <p>Sem endereço Amazon...</p>
            }
          </div>
          <div className="address-container-block">
            {
              bling
                ? <BlingAddress address={bling} />
                : <p>Sem endereço Bling...</p>
            }
          </div>
        </div>
      </Modal>
    </>
  )
}

export default AddressModal
