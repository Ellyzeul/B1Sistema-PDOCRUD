import { Table, TableBody, TableCell, TableRow } from "@mui/material"
import { DataGrid } from "@mui/x-data-grid"
import { GridColDef } from "@mui/x-data-grid/models"
import { useEffect, useState } from "react"
import { Cell, Text, Pie, PieChart, PieLabelRenderProps, PieLabel, Label, BarChart, CartesianGrid, XAxis, YAxis, Tooltip, Legend, Bar, ResponsiveContainer } from "recharts"
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
      <Label position="outside">{total}</Label>
    )
  }

  return (
    <div id="dashboard-order-page">
      <Navbar items={[]} />
      <div id="page-content">
        <div id="dashboard-title">Pedidos nas fases</div>
        <div id="chart-container">
          <ResponsiveContainer>
            <BarChart 
              width={200} 
              height={100} 
              data={data.map(({ id, total, color }) => ({id: id, Total: total, color: color}))}
            >
              <CartesianGrid strokeDasharray={'3 3'} />
              <XAxis dataKey='id' />
              <YAxis />
              <Tooltip />
              <Bar dataKey='Total'>
                {data.map(bar => <Cell id={bar.id} fill={`#${bar.color}`} stroke="black" />)}
              </Bar>
            </BarChart>
          </ResponsiveContainer>
        </div>
      </div>
    </div>
  )
}

export default DashboardOrders
