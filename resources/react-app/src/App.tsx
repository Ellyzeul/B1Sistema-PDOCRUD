import { useState } from 'react';
import { Route, Routes, Navigate } from 'react-router-dom';
import './App.css';
import { UserData } from './components/LoginForm/types';
import { UserDataContext } from './contexts/UserData';
import { Login } from './pages/Login';
import { OrdersPage } from './pages/Orders';
import { PhotosSearchPage } from './pages/Photos/Search';
import { PhotosUploadPage } from './pages/Photos/Upload';

function App() {
  const userDataRaw = window.localStorage.getItem("userData")
  const userDataParsed = userDataRaw ? JSON.parse(userDataRaw) as (UserData | null) : null
  const [userData, setUserData] = useState(userDataParsed)

  const isLogged = () => !!userData
  const getElement = (element: JSX.Element) => isLogged() ? element : <Navigate to='/login'/>

  return (
    <UserDataContext.Provider value={[userData, setUserData]}>
      <Routes>
        <Route path='/login' element={<Login />} />
        <Route path='/' element={<Navigate to='/orders'/>} />
        <Route path='/orders' element={getElement(<OrdersPage/>)} />
        <Route path='/photos/upload' element={getElement(<PhotosUploadPage/>)} />
        <Route path='/photos/search' element={getElement(<PhotosSearchPage/>)} />
      </Routes>
    </UserDataContext.Provider>
  );
}

export default App;
