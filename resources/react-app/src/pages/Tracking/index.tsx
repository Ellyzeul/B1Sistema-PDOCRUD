import { useEffect, useState } from "react"
import { Navbar } from "../../components/Navbar"
import TrackingTable from "../../components/TrackingTable"
import api from "../../services/axios"
import "./style.css"

const TrackingPage = () => {
  const [data, setData] = useState([] as any[])

  useEffect(() => {
    api.get('/api/tracking/read')
      .then(response => response.data)
      .then(response => setData(response))
  }, [])

  useEffect(() => console.log(data), [data])

  return (
    <div className="tracking-page-container">
      <Navbar items={[]} />
      <TrackingTable data={data} />
    </div>
  )
}

export default TrackingPage
