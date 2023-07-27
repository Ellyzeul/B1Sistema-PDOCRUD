import { Message } from "../Row/types";

export type MessageModalProp = {
  isOpen: boolean, 
  messages: Message[], 
  handleClose: () => void, 
}
