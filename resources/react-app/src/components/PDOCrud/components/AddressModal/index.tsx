import { Modal } from "@mui/material"
import { FormEventHandler, MouseEventHandler, useEffect, useRef, useState } from "react"
import { AddressModalProp, OrderAddress } from "./types"
import "./style.css"
import api from "../../../../services/axios"

const AddressModal = (props: AddressModalProp) => {
  const { orderNumber } = props
  const [isOpen, setIsOpen] = useState(false)
  const [orderAddress, setOrderAddress] = useState({} as OrderAddress)
  const [personType, setPersonType] = useState('F' as 'F' | 'J' | 'E')
  const formRef = useRef(null)
  const personTypeRef = useRef(null)
  const ufRef = useRef(null)

  const handleOpen: MouseEventHandler = event => {
    event.preventDefault()
    setIsOpen(true)
  }

  const handleClose = () => setIsOpen(false)

  const onPersonTypeChange: FormEventHandler = event => {
    const select = event.target as HTMLSelectElement
    const value = select.value as 'F' | 'J' | 'E'

    setPersonType(value)
    if(!ufRef.current) return
    const ufInput = ufRef.current as HTMLSelectElement

    ufInput.value = value === 'E' ? 'EX' : 'select'
  }

  const onUFChange: FormEventHandler = event => {
    if(!personTypeRef.current) return
    const personTypeSelect = personTypeRef.current as HTMLSelectElement
    const { value } = event.target as HTMLSelectElement

    if(value === 'EX' && personType !== 'E') {
      setPersonType('E')
      personTypeSelect.value = 'E'
      return
    }
    if(value !== 'EX' && personType === 'E') {
      setPersonType('F')
      personTypeSelect.value = 'F'
      return
    }
  }

  const onButtonClick = () => {
    if(!formRef.current) return
    const form = formRef.current as HTMLFormElement
    const formData = new FormData(form)
    const address = {} as {[key: string]: any}

    formData.forEach((value, key) => address[key] = value)
    console.log(address)
  }

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
              <p className="address-modal-amazon-address"><strong>Nº do pedido: </strong>{orderNumber}</p>
              {orderAddress.buyer_name ? <p><strong>Cliente: </strong>{orderAddress.buyer_name}</p> : null}
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
              {orderAddress.buyer_phone ? <p><strong>Celular do cliente: </strong>{orderAddress.buyer_phone}</p> : null}
              {orderAddress.ship_phone ? <p><strong>Celular do destinatário: </strong>{orderAddress.ship_phone}</p> : null}
              {orderAddress.expected_date ? <p><strong>Data prevista: </strong>{(new Date(orderAddress.expected_date)).toLocaleDateString('pt-BR')}</p> : null}
              {orderAddress.price ? <p><strong>Valor: </strong>{orderAddress.price}</p> : null}
              {orderAddress.price ? <p><strong>Frete: </strong>{orderAddress.freight}</p> : null}
              {
                orderAddress.item_tax || orderAddress.freight_tax 
                  ? <p><strong>Imposto: </strong>{Number(orderAddress.item_tax) + Number(orderAddress.freight_tax)}</p> 
                  : null
              }
              <p><strong>Total: </strong>{Number(orderAddress.price) + Number(orderAddress.freight) + Number(orderAddress.item_tax) + Number(orderAddress.freight_tax)}</p>
            </div>
          </div>
          <form ref={formRef} className="address-container-bling-block">
            <div>
              <label htmlFor="buyer_name">Nome do cliente: </label>
              <label htmlFor="contributor">Contribuinte: </label>
              <label htmlFor="person_type">Tipo de pessoa: </label>
              {personType === 'F' ? <label htmlFor="cpf"><strong>CPF: </strong></label> : null}
              {personType === 'J' ? <label htmlFor="cnpj"><strong>CNPJ: </strong></label> : null}
              {personType === 'E' ? <label htmlFor="country"><strong>País: </strong></label> : null}
              {personType === 'J' ? <label htmlFor="ie"><strong>IE: </strong></label> : null}
              <label htmlFor="person_type">Email do cliente: </label>
              <label htmlFor="person_type">Telefone: </label>
              <label htmlFor="person_type">Celular: </label>
              <label htmlFor="expected_date">Data Prevista: </label>
              <label htmlFor="recipient_name">Nome do destinatário: </label>
              <label htmlFor="address">Endereço: </label>
              <label htmlFor="number">Número: </label>
              <label htmlFor="complement">Complemento: </label>
              <label htmlFor="city">Cidade: </label>
              <label htmlFor="county">Bairro: </label>
              <label htmlFor="postal_code">CEP: </label>
              <label htmlFor="uf">UF: </label>
            </div>
            <div>
              <input name="buyer_name" type="text" />
              <select name="contributor" defaultValue={"9"}>
                <option value="1">1 - Contribuinte ICMS</option>
                <option value="2">2 - Contribuinte isento</option>
                <option value="9">9 - Não contribuinte</option>
              </select>
              <select ref={personTypeRef} name="person_type" onChange={onPersonTypeChange}>
                <option value="F">Pessoa Física</option>
                <option value="J">Pessoa Jurídica</option>
                <option value="E">Estrangeiro</option>
              </select>
              {personType === 'F' ? <input type="text" name="cpf"/> : null}
              {personType === 'J' ? <input type="text" name="cnpj"/> : null}
              {personType === 'E' ? <input type="text" name="country"/> : null}
              {personType === 'J' ? <input type="text" name="ie"/> : null}
              <input type="email" name="buyer_email" />
              <input type="tel" name="landline" />
              <input type="tel" name="cellphone" />
              <input type="date" name="expected_date" />
              <input type="text" name="recipient_name" />
              <input type="text" name="address" />
              <input type="text" name="number" />
              <input type="text" name="complement" />
              <input type="text" name="city" />
              <input type="text" name="county" />
              <input type="text" name="postal_code" />
              <select ref={ufRef} name="uf" onChange={onUFChange}>
                <option value="select">Selecionar</option>
                <option value="AC">AC</option>
                <option value="AL">AL</option>
                <option value="AM">AM</option>
                <option value="AP">AP</option>
                <option value="BA">BA</option>
                <option value="CE">CE</option>
                <option value="DF">DF</option>
                <option value="ES">ES</option>
                <option value="EX">EX</option>
                <option value="GO">GO</option>
                <option value="MA">MA</option>
                <option value="MG">MG</option>
                <option value="MS">MS</option>
                <option value="MT">MT</option>
                <option value="PA">PA</option>
                <option value="PB">PB</option>
                <option value="PE">PE</option>
                <option value="PI">PI</option>
                <option value="PR">PR</option>
                <option value="RJ">RJ</option>
                <option value="RN">RN</option>
                <option value="RO">RO</option>
                <option value="RR">RR</option>
                <option value="RS">RS</option>
                <option value="SC">SC</option>
                <option value="SE">SE</option>
                <option value="SP">SP</option>
                <option value="TO">TO</option>
              </select>
            </div>
          </form>
          <button 
            className="address-modal-save-address" 
            onClick={onButtonClick} 
          >Salvar</button>
        </div>
      </Modal>
    </>
  )
}

export default AddressModal
