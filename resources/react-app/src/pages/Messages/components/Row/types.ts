export type RowProp = {
  online_order_number: string, 
  sellercentral: string, 
  company: string, 
  to_answer: {}, 
  messages: Message[]
}

export type Message = {
  text: string, 
  date: string, 
  from: 'seller' | 'client', 
}
