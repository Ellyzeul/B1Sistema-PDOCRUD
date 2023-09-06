import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import Paper from '@mui/material/Paper';
import { DataItem } from './types';
import { useRef, useState } from 'react';

import api from '../../services/axios';

import { toast, ToastContainer } from "react-toastify";
import { RemoveRowModal } from './RemoveRowModal';
import { AddRowModal } from './AddRowModal';

export const InventoryTable = () => {
    const [data, setData] = useState<DataItem[]>([]);
    const filterInputRef = useRef(null)

    const searchData = () => {
      if (!filterInputRef.current) return;
      const filterInput = filterInputRef.current as HTMLInputElement;
      
      api.get(`/api/inventory/search?isbn=${filterInput.value}`)
        .then(response => response.data)
        .then(response => {
          if (response.length === 0) return toast.error("Nenhum resultado encontrado")
          setData(response)
          toast.success(`Resultados encontrados para o ISBN ${filterInput.value}`)
        })
        .catch(error => console.error(error))       
    }
    
    return (
      <>
        <div className="options-container">
          <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
            <AddRowModal />
            <div className="blacklist-filter">
              <input
                ref={filterInputRef}
                type="text"
                placeholder={`Pesquisa por ISBN`}
              />
              <button onClick={() => { searchData() }}>Filtrar</button>
            </div>
          </div>
        </div>

        <TableContainer component={Paper}>
            <Table sx={{ minWidth: 650 }} aria-label="blacklist table"  size={'small'}>
            <TableHead>
                <TableRow>
                <TableCell align='center' sx={{ fontWeight: 'bold' }}>ISBN</TableCell>
                <TableCell align='center' sx={{ fontWeight: 'bold' }}>Quantidade</TableCell>
                <TableCell align='center' sx={{ fontWeight: 'bold' }}>Condição</TableCell>
                <TableCell align='center' sx={{ fontWeight: 'bold' }}>Localização</TableCell>
                <TableCell align='center' sx={{ fontWeight: 'bold' }}>Prateleira</TableCell>
                <TableCell align='center' sx={{ fontWeight: 'bold' }}>Observação</TableCell>
                <TableCell align='center'></TableCell>
                </TableRow>
            </TableHead>
            <TableBody>
                {data.map((row) => (
                <TableRow
                key={row.isbn}
                sx={{ '&:last-child td, &:last-child th': { border: 0 }}}
                >
                    <TableCell align='center' padding="none">{row.isbn}</TableCell>
                    <TableCell align='center' padding="none">{row.quantity}</TableCell>
                    <TableCell align='center' padding="none">{row.condition}</TableCell>
                    <TableCell align='center' padding="none">{row.location}</TableCell>
                    <TableCell align='center' padding="none">{row.bookshelf}</TableCell>
                    <TableCell align='center' padding="none">{row.observation}</TableCell>
                    <TableCell align='center' padding="none"><RemoveRowModal isbn={row.isbn} location={row.location}/></TableCell>
                </TableRow>
                ))}
            </TableBody>
            </Table>
        </TableContainer>
        <ToastContainer/>
      </>
    )
}