export type InputContainerProp = {
  name: string, 
  label: string, 
  bling_data: any, 
  sellercentral_data?: any, 
  input_type?: InputType
}

type InputType = "button"
  | "checkbox"
  | "color"
  | "date"
  | "datetime-local"
  | "email"
  | "file"
  | "hidden"
  | "image"
  | "month"
  | "number"
  | "password"
  | "radio"
  | "range"
  | "reset"
  | "search"
  | "submit"
  | "tel"
  | "text"
  | "time"
  | "url"
  | "week"
