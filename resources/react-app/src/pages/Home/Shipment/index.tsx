import { Home } from "../../../components/Home"

export const ShipmentPage = () => {
  return (
    <Home 
      sections={[
        {icon: "/icons/fotos.png", label: "Fotos dos pedidos", url: "/fotos/enviar"}
      ]} 
    />
  )
}