import { Route, Routes } from 'react-router-dom';
import './App.css';
import { OrdersPage } from './pages/Orders';

function App() {
  return (
    <Routes>
      <Route path='/orders' element={<OrdersPage/>} />
    </Routes>
  );
}

export default App;
