import { useState } from 'react'
import { Link } from 'react-router-dom'
import { useApp } from '../context/AppContext'
import Footer from '../components/Footer'
import { restaurants } from '../data/restaurants'

const statusConfig = {
  confirmed: { label: 'Confirmed', cls: 'bg-green-100 text-green-700' },
  completed: { label: 'Completed', cls: 'bg-gray-100 text-gray-500' },
  cancelled: { label: 'Cancelled', cls: 'bg-red-100 text-red-600' },
  pending: { label: 'Pending', cls: 'bg-yellow-100 text-yellow-700' },
}

export default function Reservations() {
  const { reservations, cancelReservation, user } = useApp()
  const [activeTab, setActiveTab] = useState('upcoming')
  const [search, setSearch] = useState('')

  if (!user) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="bg-white rounded-2xl border border-gray-100 p-10 max-w-md w-full text-center">
          <div className="text-4xl mb-4">🔒</div>
          <h2 className="text-xl font-bold text-gray-900 mb-2">Sign in to view reservations</h2>
          <p className="text-gray-400 text-sm mb-6">You need to be logged in to manage your reservations.</p>
          <div className="flex gap-3">
            <Link to="/login" className="flex-1 bg-brand text-white py-3 rounded-xl font-semibold text-sm hover:bg-brand-dark transition-all text-center">
              Log in
            </Link>
            <Link to="/register" className="flex-1 border border-gray-200 text-gray-600 py-3 rounded-xl font-semibold text-sm hover:bg-gray-50 transition-all text-center">
              Sign up
            </Link>
          </div>
        </div>
      </div>
    )
  }

  const now = new Date()
  const filtered = reservations.filter(r => {
    const rDate = new Date(r.date)
    const matchTab = activeTab === 'upcoming'
      ? r.status === 'confirmed' && rDate >= now
      : activeTab === 'past'
      ? r.status === 'completed' || (r.status === 'confirmed' && rDate < now)
      : r.status === 'cancelled'
    const matchSearch = search === '' || r.restaurantName.toLowerCase().includes(search.toLowerCase())
    return matchTab && matchSearch
  })

  const stats = {
    total: reservations.filter(r => r.status !== 'cancelled').length,
    upcoming: reservations.filter(r => r.status === 'confirmed').length,
    saved: (reservations.filter(r => r.status !== 'cancelled').length * 2.5).toFixed(2),
  }

  const suggestions = restaurants.slice(0, 4)

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <div className="bg-brand py-8">
        <div className="max-w-7xl mx-auto px-6 flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-white">My Reservations</h1>
            <p className="text-white/70 text-sm mt-1">{stats.upcoming} upcoming · manage your bookings</p>
          </div>
          <Link to="/restaurants" className="bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm px-5 py-2.5 rounded-xl font-medium transition-all">
            + New Reservation
          </Link>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-6 py-8 grid grid-cols-1 lg:grid-cols-4 gap-8">
        {/* Sidebar */}
        <div className="lg:col-span-1 space-y-4">
          {/* Stats */}
          <div className="bg-white rounded-xl border border-gray-100 p-5">
            <h3 className="text-sm font-bold text-gray-900 mb-4">Your stats</h3>
            <div className="space-y-3">
              <div className="flex justify-between items-center">
                <span className="text-sm text-gray-500">Total visits</span>
                <span className="text-lg font-bold text-brand">{stats.total}</span>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-sm text-gray-500">Upcoming</span>
                <span className="text-lg font-bold text-green-600">{stats.upcoming}</span>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-sm text-gray-500">Saved with deals</span>
                <span className="text-lg font-bold text-purple-600">£{stats.saved}</span>
              </div>
            </div>
          </div>

          {/* Status filter */}
          <div className="bg-white rounded-xl border border-gray-100 p-5">
            <h3 className="text-sm font-bold text-gray-900 mb-3">Filter by status</h3>
            {['all', 'confirmed', 'completed', 'cancelled'].map(f => (
              <button
                key={f}
                onClick={() => setActiveTab(
                  f === 'all' ? 'upcoming' :
                  f === 'confirmed' ? 'upcoming' :
                  f === 'completed' ? 'past' : 'cancelled'
                )}
                className={`w-full text-left text-sm px-3 py-2 rounded-lg mb-1 capitalize transition-all ${
                  (activeTab === 'upcoming' && (f === 'all' || f === 'confirmed')) ||
                  (activeTab === 'past' && f === 'completed') ||
                  (activeTab === 'cancelled' && f === 'cancelled')
                    ? 'bg-brand-light text-brand-dark font-medium'
                    : 'text-gray-500 hover:bg-gray-50'
                }`}
              >
                {f === 'all' ? 'All reservations' : f}
              </button>
            ))}
          </div>
        </div>

        {/* Main */}
        <div className="lg:col-span-3">
          {/* Toolbar */}
          <div className="flex items-center gap-3 mb-5">
            <div className="flex bg-gray-100 rounded-xl p-1 gap-1">
              {[['upcoming', 'Upcoming'], ['past', 'Past'], ['cancelled', 'Cancelled']].map(([key, label]) => (
                <button
                  key={key}
                  onClick={() => setActiveTab(key)}
                  className={`text-sm px-4 py-1.5 rounded-lg font-medium transition-all ${
                    activeTab === key ? 'bg-white text-brand shadow-sm' : 'text-gray-500 hover:text-gray-700'
                  }`}
                >
                  {label}
                </button>
              ))}
            </div>
            <input
              value={search}
              onChange={e => setSearch(e.target.value)}
              placeholder="Search reservations..."
              className="flex-1 max-w-xs text-sm px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:border-brand"
            />
          </div>

          {filtered.length === 0 ? (
            <div className="bg-white rounded-xl border border-gray-100 p-10 text-center">
              <div className="text-4xl mb-3">📅</div>
              <p className="text-gray-400 font-medium">No {activeTab} reservations</p>
              {activeTab === 'upcoming' && (
                <Link to="/restaurants" className="mt-4 inline-block bg-brand text-white text-sm px-5 py-2.5 rounded-xl font-medium hover:bg-brand-dark transition-all">
                  Browse restaurants
                </Link>
              )}
            </div>
          ) : (
            <div className="space-y-3">
              {filtered.map(res => {
                const s = statusConfig[res.status] || statusConfig.confirmed
                return (
                  <div key={res.id} className="bg-white rounded-xl border border-gray-100 overflow-hidden hover:border-orange-200 transition-all">
                    <div className="p-4 flex items-center gap-4">
                      <div
                        className="w-12 h-12 rounded-xl flex items-center justify-center text-2xl flex-shrink-0"
                        style={{ backgroundColor: restaurants.find(r => r.id === res.restaurantId)?.bgColor || '#1c0a00' }}
                      >
                        {res.emoji}
                      </div>
                      <div className="flex-1 min-w-0">
                        <div className="flex items-center gap-2 flex-wrap">
                          <h3 className="text-sm font-semibold text-gray-900">{res.restaurantName}</h3>
                          <span className={`text-xs px-2 py-0.5 rounded-full font-medium ${s.cls}`}>{s.label}</span>
                        </div>
                        <p className="text-xs text-gray-400 mt-0.5 truncate">📍 {res.address}</p>
                        <p className="text-xs text-gray-500 mt-1">
                          🗓 {new Date(res.date).toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' })}
                          &nbsp;·&nbsp; ⏰ {res.time}
                          &nbsp;·&nbsp; 👥 {res.guests} guest{res.guests > 1 ? 's' : ''}
                        </p>
                      </div>
                      <div className="flex items-center gap-2 flex-shrink-0 flex-wrap justify-end">
                        <Link to={`/restaurants/${res.restaurantId}`} className="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition-all">
                          View
                        </Link>
                        {res.status === 'confirmed' && (
                          <>
                            <button className="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition-all">
                              Modify
                            </button>
                            <button className="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-brand hover:text-white hover:border-brand transition-all">
                              Directions
                            </button>
                            <button
                              onClick={() => {
                                if (confirm('Cancel this reservation?')) cancelReservation(res.id)
                              }}
                              className="text-xs px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition-all"
                            >
                              Cancel
                            </button>
                          </>
                        )}
                      </div>
                    </div>
                  </div>
                )
              })}
            </div>
          )}

          {/* Suggestions */}
          <div className="mt-10">
            <h2 className="text-base font-bold text-gray-900 mb-4">Suggested for you →</h2>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-3">
              {suggestions.map(r => (
                <Link to={`/restaurants/${r.id}`} key={r.id} className="bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-md transition-all group">
                  <div className="h-16 flex items-center justify-center text-3xl" style={{ backgroundColor: r.bgColor }}>{r.emoji}</div>
                  <div className="p-2">
                    <p className="text-xs font-semibold text-gray-800 truncate">{r.name}</p>
                    <p className="text-xs text-gray-400 mt-0.5">★ {r.rating.toFixed(1)}</p>
                  </div>
                </Link>
              ))}
            </div>
          </div>
        </div>
      </div>

      <Footer />
    </div>
  )
}
