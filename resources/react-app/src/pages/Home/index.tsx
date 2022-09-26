import { Home } from "../../components/Home"

export const HomePage = () => {
  return (
    <Home sections={[
      {icon: "/icons/gestao.png", label: "Controle de pedidos", url: "/pedidos"},
      {icon: "/icons/upload-de-arquivo.png", label: "Upload de arquivos", url: "/arquivo-upload"},
      {icon: "/icons/expedição.png", label: "Expedição", url: "/expedicao"},
      {icon: "/icons/companhia.png", label: "Empresas", url: "/empresas"},
    ]} />
  )
}
