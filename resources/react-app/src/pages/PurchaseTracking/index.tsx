import { useEffect, useState } from "react"
import { Navbar } from "../../components/Navbar"
import PurchaseTrackingTable from "../../components/PurchaseTrackingTable"
import api from "../../services/axios"

const PurchaseTrackingPage = () => {
    const [trackingTable, setTrackingTable] = useState(null as JSX.Element | null)
    
    useEffect(() => {
        api.get('/api/tracking/read-purchases')
          .then(response => response.data)
          .then(response => {
            setTrackingTable(<PurchaseTrackingTable data={response} />)
          })
      }, [])
    return (
        <div className="tracking-page-container">
          <Navbar items={[]} />
          {trackingTable}
        </div>
      )
}

export default PurchaseTrackingPage