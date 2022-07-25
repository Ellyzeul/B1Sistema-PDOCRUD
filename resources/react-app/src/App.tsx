import { Route, Routes, Navigate } from 'react-router-dom';
import './App.css';
import { Login } from './pages/Login';
import { OrdersPage } from './pages/Orders';
import { PhotosSearchPage } from './pages/Photos/Search';
import { PhotosUploadPage } from './pages/Photos/Upload';

function App() {
  const userDataRaw = window.localStorage.getItem("userData")
  const userData = userDataRaw ? JSON.parse(userDataRaw) : false
  const isLogged = !!userData
  const getElement = (element: JSX.Element) => isLogged ? element : <Navigate to='/login'/>

  return (
    <Routes>
      <Route path='/login' element={<Login />} />
      <Route path='/' element={<Navigate to='/orders'/>} />
      <Route path='/orders' element={getElement(<OrdersPage/>)} />
      <Route path='/photos/upload' element={getElement(<PhotosUploadPage/>)} />
      <Route path='/photos/search' element={getElement(<PhotosSearchPage/>)} />
    </Routes>
  );
}

export default App;
