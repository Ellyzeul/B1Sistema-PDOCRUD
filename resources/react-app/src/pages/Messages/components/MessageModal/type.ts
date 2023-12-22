import { Message } from "../Row/types";

export type MessageModalProp = {
  isOpen: boolean, 
  messages: Message[], 
  online_order_number: string, 
  sellercentral: string, 
  company: string, 
  to_answer: {}, 
  type: string, 
  handleClose: () => void, 
}
