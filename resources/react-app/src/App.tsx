import { useState } from 'react';
import { Route, Routes, Navigate } from 'react-router-dom';
import './App.css';
import { UserData } from './components/LoginForm/types';
import { DropdownProp } from './components/Navbar/Dropdown/types';
import { NavbarContext } from './contexts/Navbar';
import { UserDataContext } from './contexts/UserData';
import { HomePage } from './pages/Home';
import { Login } from './pages/Login';
import { OrdersPage } from './pages/Orders';
import { PhotosSearchPage } from './pages/Photos/Search';
import { PhotosUploadPage } from './pages/Photos/Upload';

function App() {
  const userDataRaw = window.localStorage.getItem("userData")
  const userDataParsed = userDataRaw ? JSON.parse(userDataRaw) as (UserData | null) : null
  const [userData, setUserData] = useState(userDataParsed)
	const [navbarItems, setNavbarItems] = useState({} as {[key: string]: DropdownProp[]})

  const isLogged = () => !!userData
  const getElement = (element: JSX.Element) => isLogged() ? element : <Navigate to='/login'/>

  return (
    <UserDataContext.Provider value={[userData, setUserData]}>
      <NavbarContext.Provider value={[navbarItems, setNavbarItems]}>
        <Routes>
          <Route path='/login' element={<Login />} />
          <Route path='/' element={getElement(<HomePage/>)} />
          <Route path='/pedidos' element={getElement(<OrdersPage/>)} />
          <Route path='/fotos/enviar' element={getElement(<PhotosUploadPage/>)} />
          <Route path='/fotos/pesquisa' element={getElement(<PhotosSearchPage/>)} />
        </Routes>
      </NavbarContext.Provider>
    </UserDataContext.Provider>
  );
}

export default App;
