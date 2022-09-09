export interface CompanyInfoCardProp {
  companyName: string,
  fantasyName: string,
  address: string,
  cnpj: string,
  stateRegistration: string,
  municipalRegistration: string,
  accounts: {
    bank: string, 
    agency: string, 
    account: string
  }[],
  sellercentrals: string[]
}
