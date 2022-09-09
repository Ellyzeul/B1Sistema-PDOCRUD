import "./style.css"
import { CompanyInfoCardProp } from "./types"

export const CompanyInfoCard = (props: CompanyInfoCardProp) => {
  const { 
    companyName, 
    fantasyName, 
    address, 
    cnpj, 
    stateRegistration, 
    municipalRegistration, 
    accounts, 
    sellercentrals 
  } = props

  return (
    <div className="company-info-card">
      <div>
        <span><b>Razão social: </b>{companyName}</span>
        <span><b>Nome fantasia: </b>{fantasyName}</span>
      </div>
      <hr />
      <div>
        <b>Endereço:</b>
        <p style={{whiteSpace: "pre-line"}}>{address}</p>
      </div>
      <hr />
      <div className="company-numbers">
        <div>
          <b>CNPJ: </b>
          <b>Inscrição Estadual: </b>
          <b>Inscrição Municipal (CCM): </b>
        </div>
        <div>
          <span>{cnpj}</span>
          <span>{stateRegistration}</span>
          <span>{municipalRegistration}</span>
        </div>
      </div>
      <hr />
      <div>
        <table className="bank-accounts">
          <thead>
            <th>Banco</th>
            <th>Agência</th>
            <th>Conta</th>
          </thead>
          <tbody>
            {
              accounts.map(account => <tr>
                <td>{account.bank}</td>
                <td>{account.agency}</td>
                <td>{account.account}</td>
              </tr>)
            }
          </tbody>
        </table>
      </div>
      <hr />
      <div className="sellercentrals-container">
        <b>Canais de venda: </b>
        <div className="sellercentrals-list">
          {sellercentrals.map(sellercentral => <div className="sellercentral">{sellercentral}</div>)}
        </div>
      </div>
    </div>
  )
}
