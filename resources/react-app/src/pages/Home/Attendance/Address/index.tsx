import { useSearchParams } from "react-router-dom";
import { Navbar } from "../../../../components/Navbar";
import "./style.css"
import { FormEvent, useEffect, useRef, useState } from "react";
import api from "../../../../services/axios";
import { Loader2Icon } from "lucide-react";
import { ToastContainer, toast } from "react-toastify";

export default function AddressPage() {
  const [params] = useSearchParams()
  const formRef = useRef(null)
  const [{address, order, validate_address}, setInitialState] = useState({} as Record<string, Record<string, unknown>>)
  const [validation, setValidation] = useState({} as Record<string, string>)

  useEffect(() => {
    api.get(`/api/address?order_number=${params.get('order-number')}&order_id=${params.get('order-id')}`)
      .then(response => response.data)
      .then((response: Record<string, Record<string, unknown>>) => {
        console.log(response)
        setInitialState(response)
        setValidation(response.address as Record<string, string>)
      })
  }, [])
  
  function handleClick() {
    if(!formRef.current) return
    const body = getAddress(formRef.current as HTMLFormElement)
    body.delivery_method = Number(body.delivery_method) === 0 ? null : body.delivery_method

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

  function handleCreateShipment() {
    if(!formRef.current) return

    const body = getAddress(formRef.current as HTMLFormElement)
    if(!validateBody(body)) return

    const loadingId = toast.loading('Criando rastreio')

    console.log(params.get('order-id'))
    api.post('/api/tracking/shipment', {
      order_id: params.get('order-id'),
      address: body,
    })
      .then(response => response.data)
      .then(({tracking_code}) => {
        toast.dismiss(loadingId)
        toast.success(`Rastreio ${tracking_code} criado!`)
      })
      .catch(() => {
        toast.dismiss(loadingId)
        toast.error('Erro ao criar rastreio...')
      })
  }

  function Input({defaultValue, name, type, label, width, options, onChange}: InputProp) {
    return (
      <div className="attendance-address-input-container" style={width ? {width: `${width}%`} : {}}>
        <label htmlFor={name}>{label}</label>
        {
          type !== 'select'
            ? <input
              type={type}
              name={name}
              defaultValue={defaultValue as any}
              onChange={onChange}
            />
            : <select name={name} defaultValue={defaultValue as any} onChange={onChange}>{
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
                  <div>
                    <Input 
                      defaultValue={validation['postal_code']} 
                      type="text" 
                      name="postal_code" 
                      label="CEP" 
                      width={90} 
                      onChange={({target}) => setValidation({...(validation ?? {}), postal_code: (target as HTMLInputElement).value})} 
                    />
                    {
                      !!validation['postal_code'] ?
                        (validation['postal_code'] as string).replaceAll(/[^0-9]/g, '').length < 8
                          ? <span style={{fontWeight: 'normal', fontSize: '0.75rem', color: 'red'}}>CEP com não tem 8 números</span>
                          : <></>
                        : <></>
                    }
                  </div>
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
                  <Input type="select" name="delivery_method" defaultValue={order['id_delivery_method'] ?? null} label="Método de entrega" options={DELIVERY_METHODS}/>
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
              <div className="attendance-address-btn-group">
                <input
                  className="attendance-address-save"
                  type="button"
                  value="Salvar"
                  onClick={handleClick}
                />
                <button onClick={handleCreateShipment}>Criar rastreio</button>
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

function getAddress(form: HTMLFormElement): Record<string, string|null> {
  return Object.keys(form)
    .map(key => form[key])
    .filter(input => (input instanceof HTMLInputElement) || (input instanceof HTMLSelectElement))
    .map(({name, value, type}: HTMLInputElement | HTMLSelectElement) => ([
      name,
      type === 'number'
        ? Number(value.replace(',', '.'))
        : value
    ]))
    .reduce((acc, [key, value]) => ({...acc, [key]: value}), {})
}

function validateBody(body: Record<string, string|null>): boolean {
  let validate = true

  if((((body.complement as string) + (body.delivery_instructions as string)).length > 100)) {
    toast.error('Complemento e Instruções de entrega juntos superam 100 caracteres')
    validate = false
  }
  if((body.state as string).length > 2) {
    toast.error('Estado deve conter o UF e não ter mais do que dois caracteres')
    validate = false
  }
  if((body.ship_phone as string).length > 16) {
    toast.error('Telefone dest contém mais do que 16 caracteres')
    validate = false
  }
  if(Number(body.weight) === 0) {
    toast.error('Peso não pode ser 0')
    validate = false
  }

  return validate
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
  onChange?: (event: FormEvent<HTMLInputElement|HTMLSelectElement>) => unknown,
}
