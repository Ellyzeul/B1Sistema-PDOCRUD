import { Home } from "../../components/Home"

export const PurchasePage = () =>{
    return (
        <Home sections={[
            {icon: "/icons/blacklist_isbn.png", label: "Lista negra de ISBNs", url: "/compras/isbn"},
            {icon: "/icons/blacklist_supplier.png", label: "Lista negra de Fornecedores", url: "/compras/fornecedor"},
            {icon: "/icons/blacklist_editora.png", label: "Lista negra de Editoras", url: "/compras/editora"},
        ]} />
    )
}