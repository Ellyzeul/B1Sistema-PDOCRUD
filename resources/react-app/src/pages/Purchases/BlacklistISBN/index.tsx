import { Navbar } from "../../../components/Navbar"
import { BlacklistTable } from "../../../components/BlacklistTable"
import "./style.css";

export const BlacklistISBNPage = () => {
    return (
        <>
        <Navbar items={[]} />
        <div className="blacklist-table-container">
            <BlacklistTable blacklist_type={1}/>
        </div>
        </>
    )
} 