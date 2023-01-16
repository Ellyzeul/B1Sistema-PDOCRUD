import { useEffect, useState } from "react"
import { Navbar } from "../../components/Navbar"
import TrackingTable from "../../components/TrackingTable"
import api from "../../services/axios"
import "./style.css"

const TrackingPage = () => {
  const [data, setData] = useState([] as any[])
  const [trackingTable, setTrackingTable] = useState(null as JSX.Element | null)

  useEffect(() => {
    api.get('/api/tracking/read')
      .then(response => response.data)
      .then(response => {
        setData(response)
        setTrackingTable(<TrackingTable data={response} />)
      })
  }, [])

  useEffect(() => console.log(data), [data])

  return (
    <div className="tracking-page-container">
      <Navbar items={[]} />
      {trackingTable}
    </div>
  )
}

export default TrackingPage
