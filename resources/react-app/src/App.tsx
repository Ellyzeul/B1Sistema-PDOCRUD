import { useState } from 'react';
import { Route, Routes, Navigate } from 'react-router-dom';
import { ToastContainer } from 'react-toastify';
import './App.css';
import { Login } from './pages/Login';
import { OrdersPage } from './pages/Orders';

function App() {
  const userDataRaw = window.localStorage.getItem("userData")
  const userData = userDataRaw ? JSON.parse(userDataRaw) : false
  const isLogged = !!userData
  console.log(isLogged)

  return (
    <>
      <Routes>
        <Route path='/login' element={<Login />} />
        <Route path='/' element={<Navigate to='/orders'/>} />
        <Route path='/orders' element={isLogged ? <OrdersPage/> : <Navigate to='/login'/>} />
      </Routes>
      <ToastContainer />
    </>
  );
}

export default App;
