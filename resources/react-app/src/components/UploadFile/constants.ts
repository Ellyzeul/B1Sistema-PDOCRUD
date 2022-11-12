export const uploadTypes = [
  {message: "Selecionar...", value: "select"},
  {message: "Atualização de pedidos", value: "order-update"},
  {message: "Envio de pedidos", value: "order-insert"},
]

export const fields = {
  "order-update": [
    {label: "Nº", field_name: "id", updatable: false, required: true},
    {label: "Empresa", field_name: "id_company", updatable: false, required: false},
    {label: "Canal de Venda", field_name: "id_sellercentral", updatable: false, required: false},
    {label: "Fase do processo", field_name: "id_phase", updatable: true, required: false},
    {label: "NF", field_name: "invoice_number", updatable: true, required: false},
    {label: "ORIGEM", field_name: "online_order_number", updatable: false, required: true},
    {label: "Nº Bling", field_name: "bling_number", updatable: true, required: false},
    {label: "Data do pedido", field_name: "order_date", updatable: false, required: false},
    {label: "Data prevista", field_name: "expected_date", updatable: false, required: false},
    {label: "ISBN", field_name: "isbn", updatable: false, required: false},
    {label: "Valor", field_name: "selling_price", updatable: false, required: false},
    {label: "Fornecedor", field_name: "supplier_name", updatable: true, required: false},
    {label: "Data da compra", field_name: "purchase_date", updatable: true, required: false},
    {label: "Endereço de entrega", field_name: "id_delivery_address", updatable: true, required: false},
    {label: "Nº Compra fornecedor", field_name: "supplier_purchase_number", updatable: true, required: false},
    {label: "Forma de envio", field_name: "id_delivery_method", updatable: true, required: false},
    {label: "Código de rastreio", field_name: "tracking_code", updatable: true, required: false},
    {label: "Código de coleta", field_name: "collection_code", updatable: true, required: false},
    {label: "Data de entrega", field_name: "delivered_date", updatable: true, required: false},
    {label: "Pedir avaliação", field_name: "ask_rating", updatable: true, required: false},
    {label: "Endereço verificado", field_name: "address_verified", updatable: true, required: false},
  ],
  "order-insert": [
    {label: "ID da empresa", field_name: "id_company", updatable: true, required: true},
    {label: "ID do país", field_name: "id_sellercentral", updatable: true, required: true},
    {label: "Nº Amazon", field_name: "online_order_number", updatable: true, required: true},
    {label: "Data do pedido", field_name: "order_date", updatable: true, required: true},
    {label: "Data prevista", field_name: "expected_date", updatable: true, required: true},
    {label: "ISBN", field_name: "isbn", updatable: true, required: true},
    {label: "Valor", field_name: "selling_price", updatable: true, required: true},
  ]
} as {[key: string]: {label: string, field_name: string, updatable: boolean, required: boolean}[]}
