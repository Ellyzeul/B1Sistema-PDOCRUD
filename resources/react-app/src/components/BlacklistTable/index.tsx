import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import Paper from '@mui/material/Paper';
import { BlacklistTableProps, DataItem, list_type, ResponseData } from './types';
import { useEffect, useRef, useState } from 'react';
import { RemoveRowModal } from './RemoveRowModal';
import api from '../../services/axios';
import { AddRowModal } from './AddRowModal';
import { toast, ToastContainer } from "react-toastify";

export const BlacklistTable = (props: BlacklistTableProps) => {
    const { blacklist_type } = props
    const name = list_type[blacklist_type as keyof typeof list_type] 
    const [start, setStart] = useState(0)
    const [direction, setDirection] = useState(true)
    const [data, setData] = useState<ResponseData>({ 
        data: [], 
        totalElements: 0 , 
        remainingElementsRight: 0,
        remainingElementsLeft: 0,
    });
    const filterInputRef = useRef(null)

    const createData = (
        id: number,
        content: string,
        observation: string|null,
    ) => ({ id, content, observation });

    const rows: DataItem[] = data.data.map((item) =>
        createData(
        item.id,
        item.content,
        item.observation
        )
    )
    
      
    const searchData = () => {
      if (!filterInputRef.current) return;
      const filterInput = filterInputRef.current as HTMLInputElement;
      
      api.get(`/api/blacklist/search?content=${filterInput.value}&blacklist_type=${blacklist_type}`)
        .then(response => response.data)
        .then(response => {
          if (response.length === 0) return toast.error("Nenhum resultado encontrado");
          const responseData: ResponseData = {
            data: response,
            totalElements: response.length,
            remainingElementsRight: -1,
            remainingElementsLeft: -1,
          };
          setData(responseData);
        })
        .catch(error => console.error(error));
    }
      
    useEffect(() => {
      getBlacklistIntervalData(blacklist_type, start, direction);
    }, [start, direction, blacklist_type]);
      
    const getBlacklistIntervalData = (blacklist_type: number, start: number, direction: boolean) => {
        api.get(`/api/blacklist/read-from-interval?start=${start}&blacklist_type=${blacklist_type}&is_right=${direction}`)
          .then(response => {
            const responseData: ResponseData = {
              data: response.data.data,
              totalElements: response.data.totalElements,
              remainingElementsRight: response.data.remainingElementsRight,
              remainingElementsLeft: response.data.remainingElementsLeft,
            };
            setData(responseData);
          })
          .catch(error => console.error(error));
    }

    return (
      <>
        <div className="options-container">
          <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
            <AddRowModal name={name} />
            <div className="blacklist-filter">
              <input
                ref={filterInputRef}
                type="text"
                placeholder={`Pesquisa por ${name}`}
              />
              <button onClick={() => { searchData() }}>Filtrar</button>
            </div>
          </div>
        </div>

        <TableContainer component={Paper}>
            <Table sx={{ minWidth: 650 }} aria-label="blacklist table"  size={'small'}>
            <TableHead>
                <TableRow>
                <TableCell align='center' sx={{ fontWeight: 'bold' }}>{name}</TableCell>
                <TableCell align='center' sx={{ fontWeight: 'bold' }}>Observação</TableCell>
                <TableCell align='center'></TableCell>
                </TableRow>
            </TableHead>
            <TableBody>
                {rows.map((row) => (
                <TableRow
                key={row.id}
                sx={{ '&:last-child td, &:last-child th': { border: 0 }}}
                >
                    <TableCell align='center' padding="none">{row.content}</TableCell>
                    <TableCell align='center' padding="none">{row.observation}</TableCell>
                    <TableCell align='center' padding="none"><RemoveRowModal name={name} content={row.content}/></TableCell>
                </TableRow>
                ))}
            </TableBody>
            {/* <TableFooter>
                <TableRow>
                    <TableCell colSpan={3} align="center" padding="none">
                    <IconButton
                        onClick={handleClickFirstPage}
                        disabled={start <= 0}
                        aria-label="first page"
                    >
                        <FirstPageIcon />
                    </IconButton>                    
                    <IconButton
                        onClick={handleClickArrowLeft}
                        disabled={data.remainingElementsLeft <= 0}
                        aria-label="previous page"
                    >
                        <KeyboardArrowLeft />
                    </IconButton>
                    <IconButton
                        onClick={handleClickArrowRight}
                        disabled={data.remainingElementsRight <= 0}
                        aria-label="next page"
                    >
                        <KeyboardArrowRight />
                    </IconButton>
                    <IconButton
                        onClick={handleClickLastPage}
                        disabled={data.remainingElementsRight <= 0}
                        aria-label="last page"
                    >
                        <LastPageIcon />
                    </IconButton>
                    </TableCell>
                </TableRow>
            </TableFooter> */}
            </Table>
        </TableContainer>
        <ToastContainer/>
      </>
    )
}