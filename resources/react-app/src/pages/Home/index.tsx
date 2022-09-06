import { Home } from "../../components/Home"

export const HomePage = () => {
  return (
    <Home sections={[
      {icon: "/icons/gestao.png", label: "Controle de pedidos", url: "/pedidos"},
      {icon: "/icons/expedição.png", label: "Expedição", url: "/expedicao"}
    ]} />
  )
}
