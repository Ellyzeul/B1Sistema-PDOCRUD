export type FormProps = {
    orderNumber: string
}

export type CreateOrderRequestBody = {
    id_company: number, 
    order: Order, 
    client: Client, 
    items: Item[], 
}

type Order = {
    number: string, 
    order_date: string, 
    expected_date: string, 
    id_shop: string, 
    other_expenses: number, 
    discount: number, 
    freight: number, 
    total: number, 
}

type Client = {
    name: string,
    cpf_cnpj: string,
    phone: string,
    person_type: string,
    email: string,
    address: string,
    number: string,
    postal_code: string,
    uf: string,
    county: string,
    city: string,
    complement: string,
    country: string,
}

type Item = {
    title: string, 
    isbn: string, 
    value: number, 
    quantity: number, 
}
