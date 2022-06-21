import { Route, Routes, Navigate } from 'react-router-dom';
import './App.css';
import { OrdersPage } from './pages/Orders';

function App() {
  return (
    <Routes>
      <Route path='/orders' element={<OrdersPage/>} />
      <Route path='/' element={<Navigate to='/orders'/>} />
    </Routes>
  );
}

export default App;
