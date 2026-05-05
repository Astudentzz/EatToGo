import { Link, useLocation, useNavigate } from 'react-router-dom'
import { useApp } from '../context/AppContext'

export default function Navbar() {
  const location = useLocation()
  const navigate = useNavigate()
  const { user, logout } = useApp()
  const path = location.pathname

  const links = [
    { to: '/', label: 'Home' },
    { to: '/restaurants', label: 'Restaurants' },
    { to: '/reservations', label: 'Reservations' },
  ]

  return (
    <nav className="sticky top-0 z-50 bg-white border-b border-gray-100 shadow-sm">
      <div className="max-w-7xl mx-auto px-6 h-14 flex items-center gap-8">
        <Link to="/" className="flex items-center gap-1 mr-4">
          <span className="text-xl font-bold tracking-tight text-gray-900">Order</span>
          <span className="bg-brand text-white text-xs px-1.5 py-0.5 rounded font-bold">uk</span>
        </Link>

        <div className="flex items-center gap-1 flex-1">
          {links.map(l => (
            <Link
              key={l.to}
              to={l.to}
              className={`text-sm px-3 py-1.5 rounded-lg transition-all font-medium ${
                path === l.to
                  ? 'bg-brand-light text-brand-dark'
                  : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50'
              }`}
            >
              {l.label}
            </Link>
          ))}
        </div>

        <div className="flex items-center gap-2">
          {user ? (
            <>
              <span className="text-sm text-gray-600 font-medium">Hi, {user.name}</span>
              <button
                onClick={logout}
                className="text-sm px-4 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition-all"
              >
                Log out
              </button>
            </>
          ) : (
            <>
              <Link
                to="/login"
                className="text-sm px-4 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition-all font-medium"
              >
                Log in
              </Link>
              <Link
                to="/register"
                className="text-sm px-4 py-1.5 rounded-lg bg-brand text-white hover:bg-brand-dark transition-all font-medium"
              >
                Sign up
              </Link>
            </>
          )}
        </div>
      </div>
    </nav>
  )
}
