import { Route, Routes, Navigate } from 'react-router-dom';
import './App.css';
import { Login } from './pages/Login';
import { OrdersPage } from './pages/Orders';

function App() {
  const userDataRaw = window.localStorage.getItem("userData")
  const userData = userDataRaw ? JSON.parse(userDataRaw) : false
  const isLogged = !!userData

  return (
    <Routes>
      <Route path='/login' element={<Login />} />
      <Route path='/' element={<Navigate to='/orders'/>} />
      <Route path='/orders' element={isLogged ? <OrdersPage/> : <Navigate to='/login'/>} />
    </Routes>
  );
}

export default App;
