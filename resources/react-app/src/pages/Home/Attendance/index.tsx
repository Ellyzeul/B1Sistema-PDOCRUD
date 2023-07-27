import { Home } from "../../../components/Home"

export const Attendance = () => {
  return (
    <Home sections={[
      {icon: "/icons/rastreamento.png", label: "Rastreamento dos pedidos", url: "/atendimento/rastreamento"},
      {icon: "/icons/rotina.png", label: "Rotina de desempenho / Rotinas críticas / E-mails", url: "/atendimento/rotina"},
      {icon: "/icons/api.png", label: "Puxar pedidos via API", url: "/atendimento/importacao-api"},
      {icon: "/icons/mensagens.png", label: "Comunicação com os clientes", url: "/atendimento/mensagens"},
    ]} />
  )
}
