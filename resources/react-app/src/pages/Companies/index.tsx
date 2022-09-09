import { useEffect, useState } from "react"
import { CompanyInfoCard } from "../../components/CompanyInfoCard"
import { CompanyInfoCardProp } from "../../components/CompanyInfoCard/types"
import { Navbar } from "../../components/Navbar"
import api from "../../services/axios"
import "./style.css"
import { CompanyInfoResponse } from "./types"

export const CompaniesPage = () => {
  const [companyCards, setCompanyCards] = useState([] as JSX.Element[])

  useEffect(() => {
    api.get('/api/company/read-info')
      .then(response => response.data as CompanyInfoResponse[])
      .then(response => {
        setCompanyCards(response.map(company => <CompanyInfoCard
          fantasyName={company.fantasy_name}
          companyName={company.company_name}
          address={company.address}
          cnpj={company.cnpj}
          stateRegistration={company.state_registration}
          municipalRegistration={company.municipal_registration}
          accounts={company.accounts}
          sellercentrals={company.sellercentrals}
        />))
      })
  }, [setCompanyCards])

  return (
    <div className="companies-page">
      <Navbar items={[]} />
      <div className="companies">
        {companyCards}
      </div>
    </div>
  )
}
