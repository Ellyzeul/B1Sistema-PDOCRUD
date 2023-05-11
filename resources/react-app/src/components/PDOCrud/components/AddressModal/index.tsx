import { Modal } from "@mui/material"
import { MouseEventHandler, useEffect, useRef, useState } from "react"
import { AddressModalProp, OrderAddress } from "./types"
import "./style.css"
import api from "../../../../services/axios"
import AddressForm from "./AddressForm"
import { toast } from "react-toastify"

const AddressModal = (props: AddressModalProp) => {
  const { orderNumber, orderId } = props
  const [isOpen, setIsOpen] = useState(false)
  const [{ sellercentral, bling }, setOrderAddress] = useState({} as OrderAddress)
  const [hasAddress, setHasAddress] = useState(true)

  const handleOpen: MouseEventHandler = (event) => {
    const { detail } = event
    event.preventDefault()
    if(detail === 0) return

    setIsOpen(true)
  }

  const handleClose = () => setIsOpen(false)

  useEffect(() => {
    if(!isOpen || !!sellercentral) return
    const loadingId = toast.loading('Procurando endereço...')
    api.get(`/api/orders/address?order_number=${orderNumber}`)
      .then(response => response.data as OrderAddress)
      .then(response => {
        toast.dismiss(loadingId)
        setOrderAddress(response)
      })
      .catch(() => {
        toast.dismiss(loadingId)
        setHasAddress(false)
      })
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
            : hasAddress
              ? <></>
              : <div style={{width: '100%', height: '100%', display: 'grid', placeItems: 'center'}}>
                  <strong>Sem endereço</strong>
              </div>
          }
        </div>
      </Modal>
    </>
  )
}

export default AddressModal
