import { useSearchParams } from "react-router-dom";
import { Navbar } from "../../../../components/Navbar";
import "./style.css"
import { useEffect, useRef, useState } from "react";
import api from "../../../../services/axios";
import { Loader2Icon } from "lucide-react";
import { ToastContainer, toast } from "react-toastify";

export default function AddressPage() {
  const [params] = useSearchParams()
  const formRef = useRef(null)
  const [{address, order, validate_address}, setInitialState] = useState({} as Record<string, Record<string, unknown>>)

  useEffect(() => {
    api.get(`/api/address?order_number=${params.get('order-number')}&order_id=${params.get('order-id')}`)
      .then(response => response.data)
      .then((response: Record<string, Record<string, unknown>>) => {
        console.log(response)
        setInitialState(response)
      })
  }, [])
  
  function handleClick() {
    if(!formRef.current) return
    const form = formRef.current as HTMLFormElement
    const body: Record<string, string> = Object.keys(form)
      .map(key => form[key])
      .filter(input => (input instanceof HTMLInputElement) || (input instanceof HTMLSelectElement))
      .map(({name, value, type}: HTMLInputElement | HTMLSelectElement) => ([
        name,
        type === 'number'
          ? Number(value.replace(',', '.'))
          : value
      ]))
      .reduce((acc, [key, value]) => ({...acc, [key]: value}), {})

    if(((body.complement + body.delivery_instructions).length > 100)) {
      toast.error('Complemento e Instruções de entrega juntos superam 100 caracteres')
      return
    }
    if(body.state.length > 2) {
      toast.error('Estado deve conter o UF e não ter mais do que dois caracteres')
    }
    if(body.ship_phone.length > 16) {
      toast.error('Telefone dest contém mais do que 16 caracteres')
    }

    const loadingId = toast.loading('Salvando...')
    api.put('/api/address', {
      order_number: address['online_order_number'],
      order_id: params.get('order-id'),
      address: body,
    })
      .then(() => {
        toast.dismiss(loadingId)
        toast.success('Salvo com sucesso!')
      })
      .catch(() => {
        toast.dismiss(loadingId)
        toast.error('Algum erro ocorreu...')
      })
  }

  function Input({defaultValue, name, type, label, width, options}: InputProp) {
    return (
      <div className="attendance-address-input-container" style={width ? {width: `${width}%`} : {}}>
        <label htmlFor={name}>{label}</label>
        {
          type !== 'select'
            ? <input
              type={type}
              name={name}
              defaultValue={defaultValue as any}
            />
            : <select name={name} defaultValue={defaultValue as any}>{
              Object.keys(options ?? {}).map((option, key) => <option key={key} value={option}>{options ? options[Number(option)] : ''}</option>)
            }</select>
        }
      </div>
    )
  }

  return (
    <div className="page-container">
      <Navbar items={[]}/>
      <div className="content">
        <div className="container">
          {
            address
            ? <>
              <div className="order-number">Pedido: {params.get('order-number')}</div>
              <form ref={formRef} className="address-container">
                <div>
                  <Input defaultValue={address['buyer_name']} type="text" name="buyer_name" label="Cliente" width={30} />
                  <Input defaultValue={address['recipient_name']} type="text" name="recipient_name" label="Destinatário" width={30} />
                  <Input defaultValue={address['cpf_cnpj']} type="text" name="cpf_cnpj" label="CPF/CNPJ" width={10} />
                  <Input defaultValue={address['buyer_email']} type="text" name="buyer_email" label="E-mail" width={20} />
                </div>
                <div>
                  <Input defaultValue={address['address_1']} type="text" name="address_1" label="Endereço" width={60} />
                  <Input defaultValue={address['address_2']} type="text" name="address_2" label="Complemento" width={30} />
                </div>
                <div>
                  <Input defaultValue={address['county']} type="text" name="county" label="Bairro" width={20} />
                  <Input defaultValue={address['city']} type="text" name="city" label="Cidade" width={20} />
                  <Input defaultValue={address['state']} type="text" name="state" label="Estado" width={20} />
                  <Input defaultValue={address['postal_code']} type="text" name="postal_code" label="CEP" width={10} />
                  <Input defaultValue={address['country']} type="text" name="country" label="País" width={20} />
                </div>
                <div>
                  <Input defaultValue={address['expected_date']} type="date" name="expected_date" label="Data prevista" />
                  <Input defaultValue={address['buyer_phone']} type="text" name="buyer_phone" label="Telefone cliente" width={20} />
                  <Input defaultValue={address['ship_phone']} type="text" name="ship_phone" label="Telefone dest" width={20} />
                  <Input defaultValue={address['delivery_instructions']} type="text" name="delivery_instructions" label="Instruções de entrega" width={40} />
                </div>
                <div>
                  <Input defaultValue={address['price']} type="number" name="price" label="Preço" />
                  <Input defaultValue={address['freight']} type="number" name="freight" label="Frete" />
                  <Input defaultValue={address['freight_tax']} type="number" name="freight_tax" label="Taxa do frete" />
                </div>
                <div>
                  <Input type="select" name="delivery_method" defaultValue={order['id_delivery_method'] ?? 0} label="Método de entrega" options={DELIVERY_METHODS}/>
                  <Input type="number" name="weight" defaultValue={String(order['weight']).replace('.', ',')} label="Peso"/>
                  <Input type="number" name="height" defaultValue={3} label="Altura"/>
                  <Input type="number" name="width" defaultValue={18} label="Largura"/>
                  <Input type="number" name="length" defaultValue={18} label="Comprimento"/>
                </div>
                  <div><strong>Endereço para validação:</strong></div>
                <div>
                  <div>{validate_address['adress'] as string}, {validate_address['county'] as string}, {validate_address['city'] as string} - {validate_address['uf'] as string}</div>
                </div>
              </form>
              <div>
                <input
                  className="attendance-address-save"
                  type="button"
                  value="Salvar"
                  onClick={handleClick}
                />
              </div>
            </>
            : <div className="attendance-address-loading">
              <Loader2Icon className="attendance-address-loading-animation" />
            </div>
          }
        </div>
      </div>
      <ToastContainer/>
    </div>
  )
}

const DELIVERY_METHODS: Record<number, string> = {
  0: 'Selecionar',
  1: 'Outros',
  2: 'Correios',
  3: 'DHL',
  4: 'FedEx',
  5: 'Jadlog',
  6: 'Delnext',
  7: 'Encerrado',
  8: 'Envia.com',
  9: 'Mercado Livre',
  10: 'MelhorEnvio',
  11: 'Kangu',
  12: 'Loggi',
  13: 'USPS',
}

type InputProp = {
  defaultValue: unknown,
  name: string,
  type: string,
  label: string,
  width?: number,
  options?: Record<number, string>,
}
