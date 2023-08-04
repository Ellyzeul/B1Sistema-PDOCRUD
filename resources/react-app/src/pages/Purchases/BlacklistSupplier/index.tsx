import { Navbar } from "../../../components/Navbar"
import { BlacklistTable } from "../../../components/BlacklistTable"
import "./style.css";

export const BlacklistSupplierPage = () => {
    return (
        <>
        <Navbar items={[]} />
        <div className="blacklist-table-container">
            <BlacklistTable blacklist_type={2}/>
        </div>
        </>
    )
} 