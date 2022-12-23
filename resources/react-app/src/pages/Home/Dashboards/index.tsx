import { Home } from "../../../components/Home"

export const Dashboards = () => {
  return (
    <Home sections={[
      {icon: "/icons/gestao.png", label: "Pedidos nas fases", url: "/dashboard/pedidos"},
    ]} />
  )
}
