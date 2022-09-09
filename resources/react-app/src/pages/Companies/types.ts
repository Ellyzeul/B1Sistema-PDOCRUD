export interface CompanyInfoResponse {
  company_name: string,
  fantasy_name: string,
  address: string,
  cnpj: string,
  state_registration: string,
  municipal_registration: string,
  accounts: {
    bank: string, 
    agency: string, 
    account: string
  }[],
  sellercentrals: string[]
}
