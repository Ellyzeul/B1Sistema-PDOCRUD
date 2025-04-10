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
  const [initialState, setInitialState] = useState({} as {
    address: Record<string, unknown>,
    order: Record<string, unknown>,
    items: Array<Record<string, unknown>>,
    validate_address: Record<string, unknown>,
    invoice: EmittedInvoice | null,
  })
  const {address, order, items, validate_address, invoice} = initialState
  const [validation, setValidation] = useState({} as Record<string, string>)

  useEffect(() => {
    api.get(`/api/address?order_number=${params.get('order-number')}&order_id=${params.get('order-id')}`)
      .then(response => response.data)
      .then((response: {
        address: Record<string, unknown>,
        order: Record<string, unknown>,
        items: Array<Record<string, unknown>>,
        validate_address: Record<string, unknown>,
        invoice: EmittedInvoice | null,
      }) => {
        console.log(response)
        setInitialState(response)
        setValidation(response.address as Record<string, string>)
      })
  }, [])
  
  function handleClick() {
    if(!formRef.current) return
    const body = getAddress(formRef.current as HTMLFormElement)
    body.delivery_method = Number(body.delivery_method) === 0 ? null : body.delivery_method
    const items = getItems(formRef.current)

    const loadingId = toast.loading('Salvando...')
    api.put('/api/address', {
      order_number: address['online_order_number'],
      order_id: params.get('order-id'),
      address: body,
      items,
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
    if(!((formRef.current as HTMLFormElement).querySelector('input[name="cpf_cnpj"]') as HTMLInputElement).value.trim()) {
      toast.error('O campo CPF/CNPJ não pode estar vazio')
      return
    }

    const loadingId = toast.loading('Criando rastreio')

    console.log(params.get('order-id'))
    api.post('/api/tracking/shipment', {
      order_id: params.get('order-id'),
      address: body,
      price: items.map(item => Number(item['selling_price'])).reduce((acc, cur) => acc + cur, 0),
      weight: items.map(item => Number(item['weight'])).reduce((acc, cur) => acc + cur, 0),
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

  function handleCreateInvoice() {
    if(!formRef.current) return
    const address = getAddress(formRef.current as HTMLFormElement)
    address.delivery_method = Number(address.delivery_method) === 0 ? null : address.delivery_method
    const items = getItems(formRef.current as HTMLFormElement)
    const loadingId = toast.loading('Gerando nota fiscal...')

    api.post('/api/emitted-invoice', {
      company: COMPANIES[order['id_company'] as number],
      order_number: order['online_order_number'],
      address,
      items,
    })
      .then(response => response.data)
      .then(({status, ...response}) => {
        toast.dismiss(loadingId)

        if(status === 'autorizado') {
          toast.success('Nota gerada!')
          setInitialState({
            ...initialState,
            invoice: response,
          })
          return
        }
        if(status === 'duplicado') {
          toast.info(response.mensagem_sefaz)
          return
        }
        if(status === 'erro_validacao') {
          toast.error(response.mensagem, {
            style: {whiteSpace: 'pre-line'}
          })
          return
        }

        toast.error(`Erro ${response.status_sefaz}: ${response.mensagem_sefaz}`)
      })
      .catch(err => {
        toast.dismiss(loadingId)
        toast.error('Algum erro ocorreu')
        console.log(err)
      })
  }

  function Input({defaultValue, name, type, label, readonly, width, options, onChange}: InputProp) {
    return (
      <div className="attendance-address-input-container" style={width ? {width: `${width}%`} : {}}>
        <label htmlFor={name}>{label}</label>
        {
          type !== 'select'
            ? <input
              type={type}
              name={name}
              readOnly={!!readonly}
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
                  <Input defaultValue={address['address_1']} type="text" name="address_1" label="Endereço" width={40} />
                  <Input defaultValue={address['address_number']} type="text" name="address_number" label="Número" width={20} />
                  <Input defaultValue={address['address_2']} type="text" name="address_2" label="Complemento" width={30} />
                </div>
                <div>
                  <Input defaultValue={address['county']} type="text" name="county" label="Bairro" width={18} />
                  <Input defaultValue={address['city']} type="text" name="city" label="Cidade" width={18} />
                  <Input defaultValue={address['state']} type="text" name="state" label="Estado" width={18} />
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
                  <Input defaultValue={address['country']} type="text" name="country" label="País" width={18} />
                </div>
                <div>
                  <Input defaultValue={address['expected_date']} type="date" name="expected_date" label="Data prevista" />
                  <Input defaultValue={address['buyer_phone']} type="text" name="buyer_phone" label="Telefone cliente" width={20} />
                  <Input defaultValue={address['ship_phone']} type="text" name="ship_phone" label="Telefone dest" width={20} />
                  <Input defaultValue={address['delivery_instructions']} type="text" name="delivery_instructions" label="Instruções de entrega" width={40} />
                </div>
                <div>
                  <div className="attendance-address-input-container">
                    <div>Subtotal</div>
                    <span>R$ {String(items.map(item => Number(item['selling_price'])).reduce((acc, cur) => acc + cur, 0)).replace('.', ',')}</span>
                  </div>
                  <Input defaultValue={address['freight']} type="number" name="freight" label="Frete" />
                  <Input defaultValue={address['freight_tax']} type="number" name="freight_tax" label="Taxa do frete" />
                  <div className="attendance-address-input-container">
                    <div>Total</div>
                    <span>R$ {(String(items.map(item => Number(item['selling_price'])).reduce((acc, cur) => acc + cur, 0) + Number(address['freight']))).replace('.', ',')}</span>
                  </div>
                </div>
                <div>
                  <Input type="select" name="delivery_method" defaultValue={order['id_delivery_method'] ?? null} label="Método de entrega" options={DELIVERY_METHODS}/>
                  <div className="attendance-address-input-container">
                    <div>Peso</div>
                    <span>{String(items.map(item => Number(item['weight'])).reduce((acc, cur) => acc + cur, 0)).replace('.', ',')}Kg</span>
                  </div>
                  <Input type="number" name="height" defaultValue={3} label="Altura"/>
                  <Input type="number" name="width" defaultValue={18} label="Largura"/>
                  <Input type="number" name="length" defaultValue={18} label="Comprimento"/>
                </div>
                  <div><strong>Endereço para validação:</strong></div>
                <div>
                  <div>{validate_address['adress'] as string}, {validate_address['county'] as string}, {validate_address['city'] as string} - {validate_address['uf'] as string}</div>
                </div>
                {
                  invoice
                    ? <div className="address-page-invoice-container">
                      <div><strong>Nota Fiscal:</strong></div>
                      <div>Chave: {invoice.key}</div>
                      <div>Número: {invoice.number}</div>
                      <div>{invoice.link_danfe ? <a target="_blank" href={invoice.link_danfe} rel="noreferrer">Link DANFE</a> : <></>}</div>
                      <div>{invoice.link_xml ? <a target="_blank" href={invoice.link_xml} rel="noreferrer">Link XML</a> : <></>}</div>
                    </div>
                    : <></>
                }
                <div className="address-page-items">
                  <div><strong>Itens:</strong></div>
                  {
                    groupItems(items).map((item, key) => <div className="address-page-item-row" key={key}>
                      <div>
                        <div><strong>ISBN</strong></div>
                        <div>{item['isbn'] as string}</div>
                      </div>
                      <Input type="number" name="quantity" defaultValue={item['quantity']} label="Quantidade" width={20}/>
                      <Input type="number" name="value" defaultValue={item['selling_price']} label="Valor" width={20} readonly={true}/>
                      <Input type="number" name="weight" defaultValue={item['weight']} label="Peso" width={20}/>
                      <input className="address-page-item-hidden-input" type="number" name="id" value={String(item['id'])} readOnly/>
                    </div>)
                  }
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
                {!initialState.invoice ? <button onClick={handleCreateInvoice}>Criar nota</button> : <></>}
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

function getItems(form: HTMLFormElement) {
  const items: Array<{
    isbn: string,
    quantity: number,
    value: number,
    weight: number,
    id: number,
  }> = []

  form.querySelectorAll('div.address-page-item-row').forEach(row => items.push({
    isbn: String(row.children[0].children[1].textContent),
    quantity: Number((row.children[1].children[1] as HTMLInputElement).value),
    value: Number((row.children[2].children[1] as HTMLInputElement).value.replace(',', '.')),
    weight: Number((row.children[3].children[1] as HTMLInputElement).value.replace(',', '.')),
    id: Number((row.children[4] as HTMLInputElement).value),
  }))

  return items
}

function groupItems(items: Array<Record<string, unknown>>): Array<Record<string, unknown>> {
  const byIsbn: Record<string, Array<Record<string, unknown>>> = {}

  items.forEach(item => {
    const isbn = item['isbn'] as string
    byIsbn[isbn] = !byIsbn[isbn] ? [] : byIsbn[isbn]

    byIsbn[isbn].push(item)
  })

  return Object.keys(byIsbn).map(isbn => ({
    ...byIsbn[isbn][0],
    quantity: byIsbn[isbn].length,
  }))
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

const COMPANIES: Record<number, string> = {
  0: 'seline',
  1: 'b1',
  2: 'j1',
  3: 'r1',
  5: 'livrux',
}

type InputProp = {
  defaultValue: unknown,
  name: string,
  type: string,
  label: string,
  readonly?: boolean,
  width?: number,
  options?: Record<number, string>,
  onChange?: (event: FormEvent<HTMLInputElement|HTMLSelectElement>) => unknown,
}

type EmittedInvoice = {
  key: string,
  number: string,
  emitted_at: string,
  order_number: number,
  company: 'seline' | 'b1',
  link_danfe?: string,
  link_xml?: string,
  cancelled: boolean,
  cancelment_same_day?: boolean,
}
