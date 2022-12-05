import { Home } from "../../../components/Home"

export const Attendance = () => {
  return (
    <Home sections={[
      {icon: "/icons/rastreamento.png", label: "Rastreamento dos pedidos", url: "/rastreamento"},
    ]} />
  )
}
