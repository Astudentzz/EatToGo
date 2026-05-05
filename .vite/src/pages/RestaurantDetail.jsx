import { useState } from 'react'
import { useParams, useNavigate, Link } from 'react-router-dom'
import { restaurants } from '../data/restaurants'
import { useApp } from '../context/AppContext'
import Footer from '../components/Footer'

const statusConfig = {
  available: { label: 'Available now', cls: 'bg-green-100 text-green-700' },
  limited: { label: 'Limited seats', cls: 'bg-yellow-100 text-yellow-700' },
  full: { label: 'Fully booked', cls: 'bg-red-100 text-red-700' },
}

export default function RestaurantDetail() {
  const { id } = useParams()
  const navigate = useNavigate()
  const { addReservation } = useApp()
  const r = restaurants.find(x => x.id === Number(id))

  const [activeCategory, setActiveCategory] = useState('All')
  const [selectedSlot, setSelectedSlot] = useState(null)
  const [selectedDate, setSelectedDate] = useState('')
  const [guests, setGuests] = useState(2)
  const [showConfirm, setShowConfirm] = useState(false)
  const [expanded, setExpanded] = useState(false)

  if (!r) return <div className="p-8 text-center text-gray-400">Restaurant not found.</div>

  const categories = ['All', ...new Set(r.menu.map(m => m.category))]
  const filteredMenu = activeCategory === 'All' ? r.menu : r.menu.filter(m => m.category === activeCategory)
  const s = statusConfig[r.status]

  const handleReserve = () => {
    if (!selectedDate || !selectedSlot) return alert('Please select a date and time slot.')
    addReservation({
      restaurantId: r.id,
      restaurantName: r.name,
      address: r.address,
      date: selectedDate,
      time: selectedSlot,
      guests,
      emoji: r.emoji,
    })
    setShowConfirm(true)
  }

  if (showConfirm) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center p-6">
        <div className="bg-white rounded-2xl border border-gray-100 p-10 max-w-md w-full text-center shadow-lg">
          <div className="text-5xl mb-4">🎉</div>
          <h2 className="text-2xl font-bold text-gray-900 mb-2">Reservation Confirmed!</h2>
          <p className="text-gray-500 mb-1">{r.name}</p>
          <p className="text-gray-500 mb-6">{selectedDate} at {selectedSlot} · {guests} guests</p>
          <div className="flex gap-3">
            <button onClick={() => navigate('/reservations')} className="flex-1 bg-brand text-white py-3 rounded-xl font-semibold hover:bg-brand-dark transition-all">
              View my reservations
            </button>
            <button onClick={() => { setShowConfirm(false); setSelectedSlot(null) }} className="flex-1 border border-gray-200 text-gray-600 py-3 rounded-xl font-semibold hover:bg-gray-50 transition-all">
              Back
            </button>
          </div>
        </div>
      </div>
    )
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Breadcrumb */}
      <div className="bg-white border-b border-gray-100">
        <div className="max-w-7xl mx-auto px-6 py-3 text-sm text-gray-400">
          <Link to="/restaurants" className="hover:text-brand transition-colors">Restaurants</Link>
          <span className="mx-2">/</span>
          <span className="text-gray-700 font-medium">{r.name}</span>
        </div>
      </div>

      {/* Hero image */}
      <div
        className="h-52 flex items-center justify-center text-7xl relative"
        style={{ backgroundColor: r.bgColor }}
      >
        <span>{r.emoji}</span>
        <div className="absolute bottom-4 left-6">
          <h1 className="text-2xl font-bold text-white">{r.name}</h1>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-6 py-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Main content */}
        <div className="lg:col-span-2">
          {/* Info row */}
          <div className="flex flex-wrap items-center gap-3 mb-6">
            <span className="bg-brand-light text-brand-dark text-sm px-3 py-1 rounded-full border border-orange-200 font-medium">{r.cuisine}</span>
            <span className={`text-sm px-3 py-1 rounded-full font-medium ${s.cls}`}>{s.label}</span>
            <span className="text-yellow-500 font-semibold text-sm">{'★'.repeat(Math.floor(r.rating))} {r.rating.toFixed(1)}</span>
            <span className="text-sm text-gray-400">({r.reviews} reviews)</span>
            <span className="text-sm text-gray-400 ml-auto">🕐 {r.hours}</span>
          </div>

          {/* Description */}
          <div className="bg-white rounded-xl border border-gray-100 p-5 mb-6">
            <p className="text-sm text-gray-600 leading-relaxed">
              {expanded ? r.description : r.description.slice(0, 160) + '...'}
            </p>
            <button onClick={() => setExpanded(!expanded)} className="text-sm text-brand font-medium mt-2 hover:underline">
              {expanded ? 'Show less' : 'Read more...'}
            </button>
          </div>

          {/* Menu */}
          <h2 className="text-lg font-bold text-gray-900 mb-3">Menu Preview</h2>
          <div className="flex gap-2 mb-4 overflow-x-auto pb-1 scrollbar-hide">
            {categories.map(c => (
              <button
                key={c}
                onClick={() => setActiveCategory(c)}
                className={`flex-shrink-0 text-sm px-4 py-1.5 rounded-full border transition-all font-medium ${
                  activeCategory === c
                    ? 'bg-brand text-white border-brand'
                    : 'border-gray-200 text-gray-500 hover:border-brand hover:text-brand'
                }`}
              >
                {c}
              </button>
            ))}
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            {filteredMenu.map(item => (
              <div key={item.id} className="bg-white rounded-xl border border-gray-100 p-4 flex gap-3 hover:border-orange-200 transition-all">
                <div className="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-2xl flex-shrink-0">
                  {item.emoji}
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-semibold text-gray-900">{item.name}</p>
                  <p className="text-xs text-gray-400 mt-0.5 leading-relaxed">{item.desc}</p>
                  <div className="flex items-center justify-between mt-2">
                    <span className="text-sm font-bold text-brand">£{item.price.toFixed(2)}</span>
                    <button className="w-7 h-7 bg-brand text-white rounded-full flex items-center justify-center text-lg leading-none hover:bg-brand-dark transition-all font-light">+</button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Booking sidebar */}
        <div className="lg:col-span-1">
          <div className="bg-white rounded-xl border border-gray-100 p-5 sticky top-20">
            <h3 className="text-base font-bold text-gray-900 mb-4">Reserve a table</h3>

            <div className="space-y-3 mb-4">
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1">Date</label>
                <input
                  type="date"
                  value={selectedDate}
                  min={new Date().toISOString().split('T')[0]}
                  onChange={e => setSelectedDate(e.target.value)}
                  className="w-full text-sm px-3 py-2.5 rounded-lg border border-gray-200 focus:outline-none focus:border-brand text-gray-700"
                />
              </div>
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1">Guests</label>
                <select
                  value={guests}
                  onChange={e => setGuests(Number(e.target.value))}
                  className="w-full text-sm px-3 py-2.5 rounded-lg border border-gray-200 focus:outline-none focus:border-brand text-gray-700"
                >
                  {[1,2,3,4,5,6,7,8].map(n => <option key={n} value={n}>{n} guest{n > 1 ? 's' : ''}</option>)}
                </select>
              </div>
            </div>

            <p className="text-xs font-medium text-gray-500 mb-2">Available time slots</p>
            <div className="grid grid-cols-3 gap-2 mb-5">
              {r.slots.map(slot => {
                const taken = r.takenSlots.includes(slot)
                return (
                  <button
                    key={slot}
                    disabled={taken}
                    onClick={() => setSelectedSlot(slot)}
                    className={`text-xs py-2 rounded-lg border font-medium transition-all ${
                      taken ? 'bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed' :
                      selectedSlot === slot ? 'bg-brand text-white border-brand' :
                      'border-gray-200 text-gray-600 hover:border-brand hover:text-brand'
                    }`}
                  >
                    {slot}
                  </button>
                )
              })}
            </div>

            <button
              onClick={handleReserve}
              disabled={r.status === 'full'}
              className="w-full bg-brand hover:bg-brand-dark disabled:bg-gray-200 disabled:cursor-not-allowed text-white py-3 rounded-xl font-semibold text-sm transition-all mb-3"
            >
              {r.status === 'full' ? 'Fully Booked' : 'Reserve Table'}
            </button>

            {/* Map placeholder */}
            <div className="rounded-xl bg-gray-100 h-24 flex items-center justify-center border border-gray-200 mt-2">
              <div className="text-center">
                <div className="text-xl mb-1">📍</div>
                <p className="text-xs text-gray-400">Get Directions</p>
              </div>
            </div>
            <p className="text-xs text-gray-400 mt-2 text-center">{r.address}</p>
          </div>
        </div>
      </div>

      <Footer />
    </div>
  )
}
