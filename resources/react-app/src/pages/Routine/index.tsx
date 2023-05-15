import { Navbar } from "../../components/Navbar"
import { tabContentAlibrisSelineUSA, tabContentAmazonB1BR, tabContentAmazonB1USA, tabContentAmazonSelineBR, 
tabContentAmazonSelineUSA, tabContentEmails, tabContentEstanteVirtual, tabContentFNACptSeline, tabContentGeneral } from "./tabContents"
import "./style.css"
import { TabManager } from "../../components/TabManager"

export const RoutinePage = () => {
  const contents = {
    "general": tabContentGeneral(),
    "amazon-bra-seline": tabContentAmazonSelineBR(),
    "amazon-usa-seline": tabContentAmazonSelineUSA(),
    "amazon-bra-b1": tabContentAmazonB1BR(),
    "amazon-usa-b1": tabContentAmazonB1USA(),
    "estante-virtual": tabContentEstanteVirtual(),
    "alibris-usa-seline": tabContentAlibrisSelineUSA(),
    "fnac-pt-seline": tabContentFNACptSeline(),
    "emails": tabContentEmails()
  }

  const options = [
    {label: "Geral", key: "general"},
    {label: "Amazon BRA (Seline)", key: "amazon-bra-seline"},
    {label: "Amazon USA (Seline)", key: "amazon-usa-seline"},
    {label: "Amazon BRA (B1)", key: "amazon-bra-b1"},
    {label: "Amazon USA (B1)", key: "amazon-usa-b1"},
    {label: "Estante Virtual", key: "estante-virtual"},
    {label: "Alibris.com (Seline)", key: "alibris-usa-seline"},
    {label: "FNAC.pt (Seline)", key: "fnac-pt-seline"},
    {label: "EMAIL's", key: "emails"}
  ]

    return (
      <div id="routine-page-container">
          <Navbar items={[]} />
          <TabManager props={{contents, options}}/>
        </div>
      )
}