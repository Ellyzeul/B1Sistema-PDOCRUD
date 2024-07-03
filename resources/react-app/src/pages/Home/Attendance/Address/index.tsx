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
  const [initialState, setInitialState] = useState({} as Record<string, unknown>)

  useEffect(() => {
    api.get(`/api/address?order_number=${params.get('order-number')}`)
      .then(response => response.data)
      .then((address: Record<string, unknown>) => setInitialState(address))
  }, [])
  
  function handleClick() {
    if(!formRef.current) return
    const form = formRef.current as HTMLFormElement
    const body = Object.keys(form)
      .map(key => form[key])
      .filter(input => input instanceof HTMLInputElement)
      .map(({name, value}: HTMLInputElement) => ([name, value]))
      .reduce((acc, [key, value]) => ({...acc, [key]: value}), {})

    const loadingId = toast.loading('Salvando...')
    api.put('/api/address', {
      order_number: initialState['online_order_number'],
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

  function Input({defaultValue, name, type, label, width}: InputProp) {
    return (
      <div className="attendance-address-input-container" style={width ? {width: `${width}%`} : {}}>
        <label htmlFor={name}>{label}</label>
        <input
          type={type}
          name={name}
          defaultValue={defaultValue as any}
        />
      </div>
    )
  }

  return (
    <div className="page-container">
      <Navbar items={[]}/>
      <div className="content">
        <div className="container">
          {
            initialState['online_order_number']
            ? <>
              <div className="order-number">Pedido: {params.get('order-number')}</div>
              <form ref={formRef} className="address-container">
                <div>
                  <Input defaultValue={initialState['buyer_name']} type="text" name="buyer_name" label="Cliente" width={30} />
                  <Input defaultValue={initialState['recipient_name']} type="text" name="recipient_name" label="Destinatário" width={30} />
                  <Input defaultValue={initialState['cpf_cnpj']} type="text" name="cpf_cnpj" label="CPF/CNPJ" width={10} />
                  <Input defaultValue={initialState['buyer_email']} type="text" name="buyer_email" label="E-mail" width={20} />
                </div>
                <div>
                  <Input defaultValue={initialState['address_1']} type="text" name="address_1" label="Endereço" width={60} />
                  <Input defaultValue={initialState['address_2']} type="text" name="address_2" label="Complemento" width={30} />
                </div>
                <div>
                  <Input defaultValue={initialState['county']} type="text" name="county" label="Bairro" width={20} />
                  <Input defaultValue={initialState['city']} type="text" name="city" label="Cidade" width={20} />
                  <Input defaultValue={initialState['state']} type="text" name="state" label="Estado" width={20} />
                  <Input defaultValue={initialState['postal_code']} type="text" name="postal_code" label="CEP" width={10} />
                  <Input defaultValue={initialState['country']} type="text" name="country" label="País" width={20} />
                </div>
                <div>
                  <Input defaultValue={initialState['expected_date']} type="date" name="expected_date" label="Data prevista" />
                  <Input defaultValue={initialState['buyer_phone']} type="text" name="buyer_phone" label="Telefone cliente" width={20} />
                  <Input defaultValue={initialState['ship_phone']} type="text" name="ship_phone" label="Telefone dest" width={20} />
                  <Input defaultValue={initialState['delivery_instructions']} type="text" name="delivery_instructions" label="Instruções de entrega" width={40} />
                </div>
                <div>
                  <Input defaultValue={initialState['price']} type="number" name="price" label="Preço" />
                  <Input defaultValue={initialState['freight']} type="number" name="freight" label="Frete" />
                  <Input defaultValue={initialState['freight_tax']} type="number" name="freight_tax" label="Taxa do frete" />
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

type InputProp = {
  defaultValue: unknown,
  name: string,
  type: string,
  label: string,
  width?: number,
}
