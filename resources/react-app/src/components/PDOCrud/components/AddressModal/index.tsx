import { Modal } from "@mui/material"
import { MouseEventHandler, useEffect, useRef, useState } from "react"
import { AddressModalProp, OrderAddress } from "./types"
import "./style.css"
import api from "../../../../services/axios"
import { toast } from "react-toastify"
import { CURRENCIES } from "./constants"
import AddressForm from "./AddressForm"

const AddressModal = (props: AddressModalProp) => {
  const { orderNumber } = props
  const [isOpen, setIsOpen] = useState(false)
  const [{ sellercentral, bling }, setOrderAddress] = useState({} as OrderAddress)
  const cotationDateRef = useRef(null)
  const [cotation, setCotation] = useState(1)

  const handleOpen: MouseEventHandler = (event) => {
    const { detail } = event
    event.preventDefault()
    if(detail === 0) return

    setIsOpen(true)
  }

  const handleClose = () => setIsOpen(false)

  useEffect(() => {
    if(!isOpen || !!sellercentral) return
    api.get(`/api/orders/address?order_number=${orderNumber}`)
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
          {
            (sellercentral && bling)
            ? <AddressForm sellercentral={sellercentral} bling={bling} />
            : <>Sem endereço...</>
          }
        </div>
      </Modal>
    </>
  )
}

export default AddressModal
