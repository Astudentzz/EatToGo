import { createContext, useContext, useState } from 'react'

const AppContext = createContext(null)

export function AppProvider({ children }) {
  const [user, setUser] = useState(null)
  const [reservations, setReservations] = useState([
    {
      id: 1,
      restaurantId: 1,
      restaurantName: "Sea Grill North Miami Beach",
      address: "3913 NE 163rd St, North Miami Beach, FL 33160",
      date: "2026-05-10",
      time: "09:30",
      guests: 4,
      status: "confirmed",
      emoji: "🐟",
    },
    {
      id: 2,
      restaurantId: 3,
      restaurantName: "Villagio Restaurant & Bar",
      address: "360 San Lorenzo Ave, Coral Gables, FL 33146",
      date: "2026-05-15",
      time: "19:00",
      guests: 2,
      status: "confirmed",
      emoji: "🍝",
    },
    {
      id: 3,
      restaurantId: 6,
      restaurantName: "Sushi Nomi",
      address: "800 Lincoln Rd, Miami Beach, FL 33139",
      date: "2026-04-20",
      time: "19:30",
      guests: 3,
      status: "completed",
      emoji: "🍣",
    },
    {
      id: 4,
      restaurantId: 7,
      restaurantName: "La Brasserie",
      address: "2750 NW 87th Ave, Doral, FL 33172",
      date: "2026-03-12",
      time: "20:30",
      guests: 2,
      status: "completed",
      emoji: "🥐",
    },
    {
      id: 5,
      restaurantId: 5,
      restaurantName: "The Coral Kitchen",
      address: "1200 Brickell Ave, Miami, FL 33131",
      date: "2026-02-28",
      time: "11:00",
      guests: 5,
      status: "cancelled",
      emoji: "🍔",
    },
  ])

  const addReservation = (res) => {
    setReservations(prev => [{ ...res, id: Date.now(), status: 'confirmed' }, ...prev])
  }

  const cancelReservation = (id) => {
    setReservations(prev => prev.map(r => r.id === id ? { ...r, status: 'cancelled' } : r))
  }

  const login = (data) => setUser(data)
  const logout = () => setUser(null)

  return (
    <AppContext.Provider value={{ user, login, logout, reservations, addReservation, cancelReservation }}>
      {children}
    </AppContext.Provider>
  )
}

export const useApp = () => useContext(AppContext)
