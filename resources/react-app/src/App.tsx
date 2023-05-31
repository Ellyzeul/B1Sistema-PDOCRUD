import { useEffect, useState } from 'react';
import { Route, Routes, Navigate } from 'react-router-dom';
import './App.css';
import { UserData } from './components/LoginForm/types';
import { DropdownProp } from './components/Navbar/Dropdown/types';
import { NavbarContext } from './contexts/Navbar';
import { UserDataContext } from './contexts/UserData';
import { CompaniesPage } from './pages/Companies';
import DashboardOrders from './pages/Dashboards/Orders';
import { HomePage } from './pages/Home';
import { Attendance } from './pages/Home/Attendance';
import { Dashboards } from './pages/Home/Dashboards';
import { ShipmentPage } from './pages/Home/Shipment';
import { Login } from './pages/Login';
import { OrdersPage } from './pages/Orders';
import UploadFilePage from './pages/Orders/UploadFile';
import { PhotosSearchPage } from './pages/Photos/Search';
import { PhotosUploadPage } from './pages/Photos/Upload';
import TrackingPage from './pages/Tracking';
import { RoutinePage } from './pages/Routine';
import ShipmentLabelPage from './pages/Home/Shipment/ShipmentLabel';
import APIOrderImportPage from './pages/APIOrderImport';

function App() {
  const userDataRaw = window.localStorage.getItem("userData")
  const userDataParsed = userDataRaw ? JSON.parse(userDataRaw) as (UserData | null) : null
  const [userData, setUserData] = useState(userDataParsed)
	const [navbarItems, setNavbarItems] = useState({} as {[key: string]: DropdownProp[]})

  const isLogged = () => !!userData
  const getElement = (element: JSX.Element) => isLogged() ? element : <Navigate to='/login'/>
  const isUserDataValid = (userData: any) => 
    ["email", "name", "token", "ramal", "id_section"].reduce((isValid, key) => 
      !isValid ? "false" : (key in userData ? "true" : "false")
    ) === "true"

  useEffect(() => {
    if(userData === null || isUserDataValid(userData)) return

    window.localStorage.setItem("userData", "")
    window.location.pathname = "/login"
  }, [userData])

  return (
    <UserDataContext.Provider value={[userData, setUserData]}>
      <NavbarContext.Provider value={[navbarItems, setNavbarItems]}>
        <Routes>
          <Route path='/login' element={<Login />} />
          <Route path='/' element={getElement(<HomePage/>)} />
          <Route path='/pedidos' element={getElement(<OrdersPage/>)} />
          <Route path='/arquivo-upload' element={getElement(<UploadFilePage/>)} />
          <Route path='/expedicao' element={getElement(<ShipmentPage/>)} />
          <Route path='/expedicao/fotos/enviar' element={getElement(<PhotosUploadPage/>)} />
          <Route path='/expedicao/fotos/pesquisar' element={getElement(<PhotosSearchPage/>)} />
          <Route path='/atendimento' element={getElement(<Attendance/>)} />
          <Route path='/atendimento/rastreamento' element={getElement(<TrackingPage/>)} />
          <Route path='/atendimento/rotina' element={getElement(<RoutinePage/>)} />
          <Route path='/atendimento/importacao-api' element={getElement(<APIOrderImportPage/>)} />
          <Route path='/dashboard' element={getElement(<Dashboards/>)} />
          <Route path='/dashboard/pedidos' element={getElement(<DashboardOrders/>)} />
          <Route path='/empresas' element={getElement(<CompaniesPage/>)} />
          <Route path='/etiquetas/:order_id' element={getElement(<ShipmentLabelPage/>)}/>
        </Routes>
      </NavbarContext.Provider>
    </UserDataContext.Provider>
  );
}

export default App;
