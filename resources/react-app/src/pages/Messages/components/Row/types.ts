export type RowProp = {
  id: number,
  type: 'Anúncio' | 'Pedido',
  online_order_number: string,
  has_attachments: number,
  id_company: number,
  id_sellercentral: number,
  observation: string,
  situation: string,
  timestamp: string,
}

export type Message = {
  text: string, 
  date: string, 
  from: 'seller' | 'client', 
}

// export type RowPropOld = {
//   online_order_number: string, 
//   sellercentral: string, 
//   company: string, 
//   type: 'order' | 'offer', 
//   to_answer: {}, 
//   messages: Message[]
// }