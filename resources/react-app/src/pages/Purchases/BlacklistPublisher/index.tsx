import { Navbar } from "../../../components/Navbar"
import { BlacklistTable } from "../../../components/BlacklistTable"
import "./style.css";

export const BlacklistPublisherPage = () => {
    return (
        <>
        <Navbar items={[]} />
        <div className="blacklist-table-container">
            <BlacklistTable blacklist_type={3}/>
        </div>
        </>
    )
} 