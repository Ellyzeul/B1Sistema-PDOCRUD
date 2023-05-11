import format from "date-fns/format"
import "./style.css"
import { CurrencyCotationProp } from "./types"
import { useRef } from "react"
import { toast } from "react-toastify"
import { CURRENCIES } from "../../constants"
import api from "../../../../../../services/axios"

const CurrencyCotation = (props: CurrencyCotationProp) => {
  const { setCotation, online_order_number, observation, address_form_ref, id_sellercentral } = props
  const cotationDateRef = useRef(null)
  const textareaRef = useRef(null)
  const values = {} as {[key: string]: number}
  const itemsValues = {} as {[id: string]: {
    value: number, 
    quantity: number
  }}

  const handleClick = () => {
    console.log(id_sellercentral)
    if(!cotationDateRef.current || !textareaRef.current) return
    const textarea = textareaRef.current as HTMLTextAreaElement
    const cotationDateInput = cotationDateRef.current as HTMLInputElement
    const cotationDate = cotationDateInput.value.replaceAll('-', '')
    if(cotationDate === '') {
      toast.error('Insira uma data para cotação...')
      return
    }
    const { currency, prefix, name, amazon_link } = CURRENCIES[id_sellercentral]
    if(!prefix || !name) {
      toast.error('Este pedido é nacional, sem cotação...')
      return
    }

    api.get(`https://economia.awesomeapi.com.br/json/daily/${currency}-BRL?start_date=${cotationDate}&end_date=${cotationDate}`)
      .then(response => response.data)
      .then(([{ ask }]) => {
        setCotation(ask)
        updateValues(ask)
        textarea.value = generateCotationMessage(ask, amazon_link as string, prefix, name, cotationDateInput.value)
      })
      .catch((err) => {
        console.log(err)
        toast.error(`Sem cotação de ${name.toLocaleLowerCase()} para este dia...`)
        setCotation(1)
      })
  }

  const updateValues = (cotation: number) => {
    if(cotation === 1) return
    const addressForm = address_form_ref.current as HTMLDivElement | null
    if(!addressForm) return
    const freight = addressForm.querySelector('input[name="freight"]') as HTMLInputElement
    const otherExpenses = addressForm.querySelector('input[name="other_expenses"]') as HTMLInputElement
    const discounts = addressForm.querySelector('input[name="discounts"]') as HTMLInputElement
    const itemsValuesInputs = Array.from(addressForm.querySelectorAll('input[name="item_value"]')) as HTMLInputElement[]

    if(!values.freight) {
      values.freight = Number(freight.value)
      values.other_expenses = Number(otherExpenses.value)
      values.discounts = Number(discounts.value)
    }
    if(Object.keys(itemsValues).length === 0) {
      Array.from(addressForm.querySelectorAll('div.address-panel-item-row'))
        .map((div) => {
          const { value: id } = div.querySelector('input[name="item_id"]') as HTMLInputElement
          const { value: quantity } = div.querySelector('input[name="item_quantity"]') as HTMLInputElement
          const { value: value } = div.querySelector('input[name="item_value"]') as HTMLInputElement

          return {
            id: id, 
            quantity: Number(quantity), 
            value: Number(value), 
          }
        })
        .forEach(({id, quantity, value}) => itemsValues[id] = {value: value, quantity: quantity})
    }
    freight.value = applyCotation(values.freight, cotation)
    otherExpenses.value = applyCotation(values.other_expenses, cotation)
    discounts.value = applyCotation(values.discounts, cotation)
    itemsValuesInputs.forEach(itemValue => {
      const id = itemValue.getAttribute('data-id')
      if(!id) return
      itemValue.value = applyCotation(itemsValues[id].value, cotation)
    })
  }

  const generateCotationMessage = (cotation: number, sellercental: string, currency: string, currencyName: string, cotationDate: string) => {
  const { freight, other_expenses, discounts } = values
  let subtotalBRL = 0
  let subtotal = 0

  return `Nº Pedido Loja: ${online_order_number}
BOOK // ${sellercental} //

${Object.keys(itemsValues).map((id, index) => {
  const { quantity, value} = itemsValues[id]
  const productTotalBRL = applyCotation(value * quantity, cotation)
  subtotalBRL += Number(productTotalBRL)
  subtotal += Number((value * quantity).toFixed(2))

  return `Item ${index + 1} - ${currency} ${value} - ${quantity} UN = R$ ${productTotalBRL.replace('.', ',')}`
})}

Frete ${currency} ${freight} = R$ ${String(applyCotation(freight, cotation)).replace('.', ',')}
Outras despesas ${currency} ${other_expenses} = R$ ${String(applyCotation(other_expenses, cotation)).replace('.', ',')}
Descontos ${currency} ${discounts} = R$ ${String(applyCotation(discounts, cotation)).replace('.', ',')}
Subtotal ${currency} ${(subtotal + freight + other_expenses - discounts).toFixed(2)} = R$ ${String((subtotalBRL 
  + Number(applyCotation(freight, cotation)) 
  + Number(applyCotation(other_expenses, cotation)) 
  - Number(applyCotation(discounts, cotation))
).toFixed(2)).replace('.', ',')}
Data da compra: ${cotationDate} // ${currencyName} do dia: R$ ${String(Math.floor(cotation*100)/100).replace('.', ',')}`
  }

  return (
    <div className="currency-cotation">
      <div className="currency-cotation-options">
        <input ref={cotationDateRef} type="date" defaultValue={format(new Date(), 'yyy-MM-dd')} />
        <div className="currency-cotation-fetch" onClick={handleClick}>Buscar cotação da moeda</div>
      </div>
      <textarea ref={textareaRef} name="observation" rows={10} defaultValue={observation} />
    </div>
  )
}

const applyCotation = (num: number, cotation: number) => (num * cotation).toFixed(2)

export default CurrencyCotation
