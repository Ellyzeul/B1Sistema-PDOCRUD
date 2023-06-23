import { useRef, useState } from "react"
import InputContainer from "./InputContainer"
import "./style.css"
import { AddressFormProp } from "./types"
import CurrencyCotation from "./CurrencyCotation"
import api from "../../../../../services/axios"
import { toast } from "react-toastify"
import { ShipmentAndPrice } from "./ShipmentAndPrice"
import { generateProductPageUrl, generateSellerCentralUrl} from "./generateSellerCentralURL"
import { ZipCodeConsultation } from "./ZipCodeConsultation"

const AddressForm = (props: AddressFormProp) => {
  const { sellercentral, bling, orderId, salesChannel } = props
  const [cotation, setCotation] = useState(1)
  const addressFormRef = useRef(null)
  const observationRef = useRef(null)
  const { update_data } = bling
  const { id_company } = sellercentral
  const items = bling.items.map(({id, sku, title, value, quantity, origin, ncm, cest}, idx) => (
    <div key={idx} className="address-panel-item-row">
      <div>{title}</div>
      <div>{sku}</div>
      <input type="text" name="item_quantity" defaultValue={quantity} data-id={id} />
      <input type="text" name="item_value" defaultValue={(value || 0).toFixed(2)} data-id={id} />
      <input name="item_id" defaultValue={id} style={{display: 'none'}} />
      <input name="item_title" defaultValue={title} style={{display: 'none'}} />
      <input name="item_sku" defaultValue={sku} style={{display: 'none'}} />
      <div>
        <div>{(value * quantity * cotation).toFixed(2)}</div>
        <a id="item_link" target="_blank" href={generateProductPageUrl(salesChannel, sku.split('_')[1])} rel="noreferrer" ><img src="/icons/url_16x16.png" alt="link" width="20" height="20"></img></a>
      </div>
      {
        (origin || ncm || cest)
        && <div className="address-panel-item-row-tribute-info">
          {origin && <div>Origem: {origin}</div>}
          {ncm && <div>NCM: {ncm}</div>}
          {cest && <div>CEST: {cest}</div>}
        </div>
      }
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
    const countrySelect = addressForm.querySelector('select[name="country"]') as HTMLSelectElement
    const personTypeSelect = addressForm.querySelector('select[name="person_type"]') as HTMLSelectElement
    const observationTextarea = observationDiv.querySelector('textarea[name="observation"]') as HTMLTextAreaElement
    const order = [...Array.from(addressForm.querySelectorAll('input')), observationTextarea, ufSelect, countrySelect, personTypeSelect]
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
        if(order && order.error) {
          displayUpdateError(order.error)
          return
        }
        if((products as {[key: string]: any}[]).some(product => product.error)) {
          console.log(products)
          toast.error('Algum erro ocorreu ao salvar as alterações no(s) produto(s), consultar o TI...')
          return
        }
        if(contact && ufSelect.value !== 'EX') {
          displayUpdateError(contact.error)
          return
        }

        toast.success('Pedido atualizado!')
      })
      .catch(err => {
        console.log(err)
        toast.dismiss(loadingId)
        toast.error('Algum erro ocorreu, consultar o TI...')
      })
  }

  const displayUpdateError = (error: {description: string, fields: {msg: string}[]}) => {
    const { description, fields } = error
    toast.error(
      <div>
        {description}
        {fields.map(field => <><br />{field.msg}</>).reduce((acc, cur) => <>{acc}{cur}</>)}
      </div>
    )
  }

  return (
    <div className="address-form">
      <div className="address-form-save-btn" onClick={handleClick}>Salvar</div>
      <div ref={addressFormRef} className="address-panel">
        <strong className="address-panel-section-header">Dados do Cliente</strong>
        <span className="address-panel-order-number">ORIGEM: {sellercentral.online_order_number} - Nº Bling: {bling.bling_number} ({id_company === 0 ? <img src="seline_white_bg.png" width="20" height="20"/> : <img src="b1_white_bg.png" width="20" height="20"/>}) - Canal de Venda: {<a target="_blank" href={generateSellerCentralUrl(salesChannel, sellercentral.online_order_number)} rel="noreferrer">{salesChannel}</a>}</span>
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
              <select name="uf" className="address-panel-uf-select" defaultValue={bling.uf as string}>{ufOptions}</select>
            </div>
          </div>
          <InputContainer name="postal_code" label="CEP" bling_data={bling.postal_code} sellercentral_data={sellercentral.postal_code} />
        </div>
        <div className="address-panel-phone-container">
          <div className="address-panel-country-container">  
            <div>
              <div>
                <strong>País</strong>
              </div>
              <select name="country" className="address-panel-country-select" defaultValue={bling.country}>{countryOptions}</select>
            </div>
            <span>{sellercentral.country}</span>
          </div>
          <div className="address-panel-person-type-container">
            <div>
              <strong>Pessoa</strong>
              <select name="person_type" className="address-panel-person-type-select" defaultValue={bling.person_type as string}>
                <option value="">Selecionar</option>
                <option value="F">Física</option>
                <option value="J">Juridica</option>
                <option value="E">Estrangeira</option>
              </select>
            </div>
          </div>
          <InputContainer name="cellphone" label="Celular" bling_data={bling.cellphone} sellercentral_data={sellercentral.ship_phone} />
          <InputContainer name="landline" label="Telefone" bling_data={bling.landline} sellercentral_data={sellercentral.buyer_phone} />
        </div>
        <ZipCodeConsultation bling_postal_code={bling.postal_code} country={sellercentral.country}/>
        <strong className="address-panel-section-header">Valores e datas</strong>
        <div className="address-panel-values-container">
          <InputContainer name="freight" label="Frete" bling_data={(bling.freight || 0).toFixed(2)} />
          <InputContainer name="other_expenses" label="Outras despesas" bling_data={(bling.other_expenses || 0).toFixed(2)} />
          <InputContainer name="discounts" label="Descontos" bling_data={(bling.discount || 0).toFixed(2)} />
          <InputContainer name="expected_date" label="Data prevista" bling_data={bling.expected_date} sellercentral_data={sellercentral.expected_date} input_type="date" />
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
        <div className="address-form-shipment-consultation">
          <ShipmentAndPrice 
            orderId={orderId} 
            address_form_ref={addressFormRef} 
            delivery_service={bling.delivery_service} 
            delivery_method={sellercentral.delivery_method} 
            tracking_code={sellercentral.tracking_code} 
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

const countryOptions = (
  <>
    <option value="">Brasil</option>
    <option value="ESTADOS UNIDOS">Estados Unidos</option>
    <option value="AUSTRALIA">Australia</option>
    <option value="AUSTRIA">Austria</option>
    <option value="BELGICA">Bélgica</option>
    <option value="BULGARIA, REPUBLICA DA">Bulgaria</option>
    <option value="CANADA">Canadá</option>
    <option value="CHINA, REPUBLICA POPULAR">China</option>
    <option value="CHRISTMAS,ILHA (NAVIDAD)">Ilha Christmas</option>
    <option value="COCOS(KEELING),ILHAS">Ilhas Cocos (Keeling)</option>
    <option value="CROACIA (REPUBLICA DA)">Croácia</option>
    <option value="CHIPRE">Chipre</option>
    <option value="CAZAQUISTAO, REPUBLICA DO">Cazaquistão</option>
    <option value="TCHECA, REPUBLICA">República Tchéquia</option>
    <option value="DINAMARCA">Dinamarca</option>
    <option value="ESTONIA, REPUBLICA DA">Estônia</option>
    <option value="FINLANDIA">Finlândia</option>
    <option value="FRANCA">França</option>
    <option value="GEORGIA, REPUBLICA DA">Georgia</option>
    <option value="ALEMANHA">Alemanha</option>
    <option value="GRECIA">Grécia</option>
    <option value="HONG KONG">Hong Kong</option>
    <option value="HUNGRIA, REPUBLICA DA">Húngria</option>
    <option value="IRLANDA">Irlanda</option>
    <option value="ISRAEL">Israel</option>
    <option value="ITALIA">Itália</option>
    <option value="JAPAO">Japão</option>
    <option value="LETONIA, REPUBLICA DA">Letônia</option>
    <option value="LITUANIA, REPUBLICA DA">Lituania</option>
    <option value="LUXEMBURGO">Luxemburgo</option>
    <option value="MACAU">Macau</option>
    <option value="MACEDONIA DO NORTE">Macedônia</option>
    <option value="MALTA">Malta</option>
    <option value="MONACO">Mônaco</option>
    <option value="MONGOLIA">Mongólia</option>
    <option value="MONTENEGRO">Montenegro</option>
    <option value="PAISES BAIXOS (HOLANDA)">Holanda</option>
    <option value="NOVA ZELANDIA">Nova Zelândia</option>
    <option value="NORFOLK,ILHA">Norfolk</option>
    <option value="NORUEGA">Noruega</option>
    <option value="FILIPINAS">Filipinas</option>
    <option value="POLONIA, REPUBLICA DA">Polônia</option>
    <option value="PORTUGAL">Portugal</option>
    <option value="CATAR">Catar</option>
    <option value="ROMENIA">Romênia</option>
    <option value="CINGAPURA">Cingapura</option>
    <option value="ESLOVACA, REPUBLICA">Eslováquia</option>
    <option value="ESLOVENIA, REPUBLICA DA">Eslovênia</option>
    <option value="AFRICA DO SUL">África do Sul</option>
    <option value="COREIA, REPUBLICA DA">Coréia do Sul</option>
    <option value="ESPANHA">Espanha</option>
    <option value="SUECIA">Suécia</option>
    <option value="SUICA">Suíça</option>
    <option value="FORMOSA (TAIWAN)">Taiwan</option>
    <option value="TAILANDIA">Tailândia</option>
    <option value="REINO UNIDO">Reino Únido</option>
    <option value="VATICANO, EST.DA CIDADE DO">Vaticano</option>
  </>
)

export default AddressForm
