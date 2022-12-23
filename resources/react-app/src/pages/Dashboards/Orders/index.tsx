import { Table, TableBody, TableCell, TableRow } from "@mui/material"
import { DataGrid } from "@mui/x-data-grid"
import { GridColDef } from "@mui/x-data-grid/models"
import { useEffect, useState } from "react"
import { Cell, Text, Pie, PieChart, PieLabelRenderProps, PieLabel } from "recharts"
import { Navbar } from "../../../components/Navbar"
import api from "../../../services/axios"
import './style.css'

const DATAGRID_FIELDS = [
  {field: 'id', headerName: 'Fase'},
  {field: 'total', headerName: 'Total'},
] as GridColDef[]

const DashboardOrders = () => {
  const [data, setData] = useState([] as {id: string, total: number, color: string}[])

  useEffect(() => {
    api.get('/api/orders/get-total-orders-in-phase')
      .then(response => response.data)
      .then(response => setData(response))
  }, [])

  const renderLabel = (entry: PieLabelRenderProps) => {
    const { cx, cy, midAngle, innerRadius, outerRadius, id, total, endAngle, startAngle } = entry
    const [ circleX, circleY, mAngle, iRadius, oRadius, initAngle, finalAngle ] = [
      cx, cy, midAngle, innerRadius, outerRadius, startAngle, endAngle
    ].map(item => Math.floor(Number(item)))
    const RADIAN = Math.PI / 180
    const deltaAngle = Math.abs(finalAngle - initAngle)

    console.log(`${deltaAngle} - ${id}`)
    if(deltaAngle < 10) return <text />

    const radius = iRadius + (oRadius - iRadius) * 0.5;
    const x = circleX + radius * Math.cos(-mAngle * RADIAN);
    const y = circleY + radius * Math.sin(-mAngle * RADIAN);
    
    return (
      <text x={x} y={y} textAnchor="middle" dominantBaseline={"central"} style={{color: 'black'}}>
        {`${id} - ${total} Pedidos`}
      </text>
    )
  }

  return (
    <div id="dashboard-order-page">
      <Navbar items={[]} />
      <div id="page-content">
        <div id="dashboard-title">Pedidos nas fases</div>
        <PieChart width={400} height={400}>
          <Pie data={data} dataKey="total" nameKey="id" cx="50%" cy="50%" outerRadius={200} label={renderLabel}>
            {data.map(phase => <Cell key={`cell-${phase.id}`} fill={`#${phase.color}`} stroke="#000000" textAnchor="A" />)}
          </Pie>
        </PieChart>
        <div id="infos-table">
          <DataGrid 
            columns={DATAGRID_FIELDS} 
            rows={data.map(({ id, total }) => ({id: id, total: total}))} 
            style={{
              width: "100%",
              backgroundColor: "white",
            }}
            hideFooter 
          />
        </div>
      </div>
    </div>
  )
}

export default DashboardOrders
