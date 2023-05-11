import { useRef, useState } from "react"
import InputContainer from "./InputContainer"
import "./style.css"
import { AddressFormProp } from "./types"
import CurrencyCotation from "./CurrencyCotation"
import api from "../../../../../services/axios"
import { toast } from "react-toastify"

const AddressForm = (props: AddressFormProp) => {
  const { sellercentral, bling } = props
  const [cotation, setCotation] = useState(1)
  const addressFormRef = useRef(null)
  const observationRef = useRef(null)
  const { update_data } = bling
  const { id_company } = sellercentral
  const items = bling.items.map(({id, sku, title, value, quantity}, idx) => (
    <div key={idx} className="address-panel-item-row">
      <div>{title}</div>
      <div>{sku}</div>
      <input type="text" name="item_quantity" defaultValue={quantity} data-id={id} />
      <input type="text" name="item_value" defaultValue={value} data-id={id} />
      <input name="item_id" defaultValue={id} style={{display: 'none'}} />
      <input name="item_title" defaultValue={title} style={{display: 'none'}} />
      <input name="item_sku" defaultValue={sku} style={{display: 'none'}} />
      <div>{(value * quantity * cotation).toFixed(2)}</div>
    </div>
  ))

  const handleClick = () => {
    const addressForm = addressFormRef.current as HTMLDivElement | null
    const observationDiv = observationRef.current as HTMLDivElement | null
    if(!addressForm || !observationDiv) return
    const items = Array
      .from(addressForm.querySelectorAll('div.address-panel-item-row'))
      .map((div) => {
        const { value: id } = div.querySelector('input[name="item_id"]') as HTMLInputElement
        const { value: title } = div.querySelector('input[name="item_title"]') as HTMLInputElement
        const { value: quantity } = div.querySelector('input[name="item_quantity"]') as HTMLInputElement
        const { value: value } = div.querySelector('input[name="item_value"]') as HTMLInputElement
        const { value: sku } = div.querySelector('input[name="item_sku"]') as HTMLInputElement

        return {
          id: id,
          title: title,
          quantity: quantity,
          value: value,
          isbn: sku.split('_')[1],
        }
      })

    const ufSelect = addressForm.querySelector('select[name="uf"]') as HTMLSelectElement
    const observationTextarea = observationDiv.querySelector('textarea[name="observation"]') as HTMLTextAreaElement
    const order = [...Array.from(addressForm.querySelectorAll('input')), observationTextarea, ufSelect]
      .filter(input => !input.name.startsWith('item'))
      .map(({ name, value }) => ({ [name]: value }))
      .reduce((acc, cur) => ({ ...acc, ...cur }))

    const blingData = { ...order, 
      items: items, 
      update_data: update_data, 
    }

    const loadingId = toast.loading('Atualizando...')
    api.patch('/api/orders/bling/order', {
      bling_data: blingData,
      id_company: id_company,
    })
      .then(response => response.data)
      .then(({order, contact, products}) => {
        toast.dismiss(loadingId)
        if(order.error || contact || (products as {[key: string]: any}[]).some(product => product.error)) {
          toast.error('Algum erro ocorreu, consultar o TI...')
          return
        }

        toast.success('Pedido atualizado!')
      })
      .catch(() => {
        toast.dismiss(loadingId)
        toast.error('Algum erro ocorreu...')
      })
  }

  return (
    <div className="address-form">
      <div className="address-form-save-btn" onClick={handleClick}>Salvar</div>
      <div ref={addressFormRef} className="address-panel">
        <strong className="address-panel-section-header">Dados do Cliente</strong>
        <div className="address-panel-names-container">
          <InputContainer name="buyer_name" label="Cliente" bling_data={bling.buyer_name} sellercentral_data={sellercentral.buyer_name} />
          <InputContainer name="recipient_name" label="Destinatário" bling_data={bling.recipient_name} sellercentral_data={sellercentral.recipient_name} />
          <InputContainer name="cpf_cnpj" label="CPF/CNPJ" bling_data={bling.cpf_cnpj} sellercentral_data={sellercentral.cpf_cnpj} />
        </div>
        <div className="address-panel-address-number-container">
          <InputContainer name="address" label="Endereço" bling_data={bling.address} sellercentral_data={sellercentral.address_1} />
          <InputContainer name="number" label="Número" bling_data={bling.number} sellercentral_data='' />
        </div>
        <div className="address-panel-complement-container">
          <InputContainer name="complement" label="Complemento" bling_data={bling.complement} sellercentral_data={sellercentral.address_2} />
          <InputContainer name="county" label="Bairro" bling_data={bling.county} sellercentral_data={sellercentral.county} />
          <InputContainer name="city" label="Cidade" bling_data={bling.city} sellercentral_data={sellercentral.city} />
          <div className="address-panel-uf-container">  
            <div>
              <div>
                <strong>UF</strong>
              </div>
              <select name="uf" id="address-panel-uf-select" defaultValue={bling.uf as string}>{ufOptions}</select>
            </div>
          </div>
          <InputContainer name="postal_code" label="CEP" bling_data={bling.postal_code} sellercentral_data={sellercentral.postal_code} />
        </div>
        <strong className="address-panel-section-header">Valores e datas</strong>
        <div className="address-panel-values-container">
          <InputContainer name="freight" label="Frete" bling_data={bling.freight} />
          <InputContainer name="other_expenses" label="Outras despesas" bling_data={bling.other_expenses} />
          <InputContainer name="discounts" label="Descontos" bling_data={bling.discount} />
          <InputContainer name="expected_date" label="Data prevista" bling_data={bling.expected_date} input_type="date" />
        </div>
        <strong className="address-panel-section-header">Itens</strong>
        <div className="address-panel-items-header">
          <strong>Título</strong>
          <strong>SKU</strong>
          <strong>Qnt</strong>
          <strong>Valor</strong>
          <strong>Total</strong>
        </div>
        {items}
      </div>
      <div className="address-form-righthand">
        <div ref={observationRef} className="address-form-observation">
          <strong className="address-panel-section-header">Observações</strong>
          <CurrencyCotation 
            cotation={cotation} 
            setCotation={setCotation} 
            online_order_number={sellercentral.online_order_number} 
            observation={bling.observation} 
            address_form_ref={addressFormRef}
            id_sellercentral={sellercentral.id_sellercentral} 
          />
        </div>
      </div>
    </div>
  )
}

const ufOptions = (
  <>
    <option value=" "> UF ... </option>
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
  </>
)

export default AddressForm
