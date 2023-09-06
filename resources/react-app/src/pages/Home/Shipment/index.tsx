import { Home } from "../../../components/Home"

export const ShipmentPage = () => {
  return (
    <Home 
      sections={[
        {icon: "/icons/fotos.png", label: "Fotos dos Pedidos", url: "/expedicao/fotos/enviar"}, 
        {icon: "/icons/rastreamento.png", label: "Monitoramento de Compras", url: "/expedicao/compras/rastreamento"}, 
        {icon: "/icons/inventario.png", label: "Estoque", url: "/expedicao/compras/inventario"}, 
      ]} 
    />
  )
}