import { Home } from "../../components/Home"

export const HomePage = () => {
  return (
    <Home sections={[
      {icon: "/icons/gestao.png", label: "Controle de pedidos", url: "/pedidos"},
      {icon: "/icons/upload-de-arquivo.png", label: "Upload de arquivos", url: "/arquivo-upload"},
      {icon: "/icons/expedição.png", label: "Expedição", url: "/expedicao"},
      {icon: "/icons/atendimento.png", label: "Atendimento", url: "/atendimento"},
      {icon: "/icons/compras.png", label: "Compras", url: "/compras"},
      {icon: "/icons/dinheiro.png", label: "Financeiro", url: "/financeiro"},
      {icon: "/icons/pie-chart.png", label: "Dashboards", url: "/dashboard"},
      {icon: "/icons/companhia.png", label: "Empresas", url: "/empresas"},
    ]} />
  )
}
