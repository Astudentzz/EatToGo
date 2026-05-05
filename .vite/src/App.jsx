import { Routes, Route, useLocation } from 'react-router-dom'
import { AppProvider } from './context/AppContext'
import Navbar from './components/Navbar'
import Home from './pages/Home'
import Restaurants from './pages/Restaurants'
import RestaurantDetail from './pages/RestaurantDetail'
import Reservations from './pages/Reservations'
import Login from './pages/Login'
import Register from './pages/Register'

const HIDE_NAVBAR = ['/login', '/register']

export default function App() {
  const location = useLocation()
  const hideNav = HIDE_NAVBAR.includes(location.pathname)

  return (
    <AppProvider>
      {!hideNav && <Navbar />}
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/restaurants" element={<Restaurants />} />
        <Route path="/restaurants/:id" element={<RestaurantDetail />} />
        <Route path="/reservations" element={<Reservations />} />
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />
      </Routes>
    </AppProvider>
  )
}
