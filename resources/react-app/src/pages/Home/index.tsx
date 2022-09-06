import { Home } from "../../components/Home"

export const HomePage = () => {
  return (
    <Home sections={[
      {icon: "/icons/gestao.png", label: "Controle de pedidos", url: "/pedidos"},
      {icon: "/icons/fotos.png", label: "Fotos dos pedidos", url: "/fotos/enviar"}
    ]} />
  )
}
