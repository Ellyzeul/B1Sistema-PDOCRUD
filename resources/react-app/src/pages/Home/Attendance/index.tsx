import { Home } from "../../../components/Home"

export const Attendance = () => {
  return (
    <Home sections={[
      {icon: "/icons/rastreamento.png", label: "Rastreamento dos pedidos", url: "/atendimento/rastreamento"},
      {icon: "/icons/rotina.png", label: "Rotina de Desempenho / Rotinas Críticas / Emails", url: "/atendimento/rotina"},
    ]} />
  )
}
