import { useRef } from "react"
import { InputContainerProp } from "./types"
import "./style.css"

const InputContainer = (props: InputContainerProp) => {
  const { name, label, bling_data, sellercentral_data, input_type } = props
  const inputRef = useRef(null)

  const handleClick = () => {
    const input = inputRef.current as HTMLInputElement | null
    if(!input || !sellercentral_data) return

    input.value = sellercentral_data
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

export default InputContainer
