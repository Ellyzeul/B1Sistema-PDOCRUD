import { useRef } from "react"
import { InputContainerProp } from "./types"
import "./style.css"

const InputContainer = (props: InputContainerProp) => {
  const { name, label, bling_data, sellercentral_data, input_type } = props
  const inputRef = useRef(null)

  const handleClick = () => {
    const input = inputRef.current as HTMLInputElement | null
    if(!input || !sellercentral_data) return

    input.value = getValue(sellercentral_data)
  }

  const getValue = (value: string) => {
    if(!input_type) return value
  
    try {
      return formatValues[input_type](value)
    }
    catch {
      return value
    }
  }

  return (
    <div className="address-panel-input-container">
      <span>{label}</span>
      <input ref={inputRef} name={name} type={input_type || "text"} defaultValue={bling_data} />
      <div>
        <span className="address-panel-sellercentral-data">{sellercentral_data}</span>
        {
          sellercentral_data
          ? <i className="fa-solid fa-pen address-panel-sellercentral-assign" onClick={handleClick} />
          : <i className="address-panel-sellercentral-assign" />
        }
      </div>
    </div>
  )
}

const formatValues = {
  'date': val => val.replace(/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/, '$3-$2-$1')
} as {[key: string]: (val: string) => string}

export default InputContainer
