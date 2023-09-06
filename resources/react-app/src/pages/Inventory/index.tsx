import { Navbar } from "../../components/Navbar";
import "./style.css";
import { InventoryTable } from "../../components/InventoryTable";

export const InventoryPage = () => {
    return (
        <>
        <Navbar items={[]} />
        <div className="inventory-table-container">
            <InventoryTable/>
        </div>
        </>
    )
} 