import { useEffect, useState } from "react"
import { Navbar } from "../../components/Navbar"
import { TrackingTable } from "../../components/TrackingTable"
import api from "../../services/axios"
import "./style.css"

const TrackingPage = () => {
  const [table, setTable] = useState(null as JSX.Element | null)

  useEffect(() => {
    api.get('/api/tracking/read')
      .then(response => response.data)
      .then(response => setTable(<TrackingTable data={response} />))
  }, [])

  return (
    <div className="tracking-page-container">
      <Navbar items={[]} />
      {table}
    </div>
  )
}

export default TrackingPage
