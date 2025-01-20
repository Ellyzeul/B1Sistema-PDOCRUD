import { Home } from "../../components/Home";

export default function FinancialPage() {
  return (
    <Home
      sections={[
        {icon: "/icons/match.png", label: "Match de notas", url: "/financeiro/match"},
        {icon: "/icons/recibo.png", label: "Contas a pagar/receber", url: "/financeiro/contas"},
        {icon: "/icons/bank.png", label: "Extrato bancário", url: "/financeiro/bancos"},
        {icon: "/icons/accounting.png", label: "Contabilidade", url: "/financeiro/fiscal"},
      ]}
    />
  )
}