export type RowProp = {
  online_order_number: string, 
  sellercentral: string, 
  company: string, 
  type: 'order' | 'offer', 
  to_answer: {}, 
  messages: Message[]
}

export type Message = {
  text: string, 
  date: string, 
  from: 'seller' | 'client', 
}
