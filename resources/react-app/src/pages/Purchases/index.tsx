import { Home } from "../../components/Home"

export const PurchasePage = () =>{
    return (
        <Home sections={[
            {icon: "/icons/blacklist_isbn.png", label: "Blacklist de ISBN's", url: "/compras/isbn"},
            {icon: "/icons/blacklist_supplier.png", label: "Blacklist de Fornecedores", url: "/compras/fornecedor"},
            {icon: "/icons/blacklist_editora.png", label: "Blacklist de Editoras", url: "/compras/editora"},
        ]} />
    )
}