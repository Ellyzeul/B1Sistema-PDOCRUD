import { useState } from 'react';
import { Route, Routes, Navigate } from 'react-router-dom';
import './App.css';
import { Login } from './pages/Login';
import { OrdersPage } from './pages/Orders';

function App() {
  const [isLogged, setIsLogged] = useState(false)

  return (
    <Routes>
      <Route path='/login' element={<Login setIsLogged={setIsLogged}/>} />
      <Route path='/' element={<Navigate to='/orders'/>} />
      <Route path='/orders' element={isLogged ? <OrdersPage/> : <Navigate to='/login'/>} />
    </Routes>
  );
}

export default App;
