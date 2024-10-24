export type Invoice = {
  key: string,
  emitted_at: string,
  period: string,
  type: 'out' | 'in',
  value: number,
  status: 'authorized' | 'cancelled',
  manifestation: 'acknowledged' | 'confirmed',
  emitter: InvoiceCompany,
  recipient: InvoiceCompany,
  courier?:InvoiceCompany,
  has_xml: boolean,
  origin: string,
  cfops: string,
  match: 'linked' | 'partially_linked' | 'not_linked',
}

export type InvoiceCompany = {
  cnpj: string,
  name?: string,
  ie?: string,
  uf?: string,
}
