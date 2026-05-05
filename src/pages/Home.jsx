import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { restaurants, moodFilters } from '../data/restaurants'
import RestaurantCard from '../components/RestaurantCard'
import Footer from '../components/Footer'

export default function Home() {
  const navigate = useNavigate()
  const [search, setSearch] = useState('')
  const [activeMood, setActiveMood] = useState('Group')

  const filtered = restaurants
    .filter(r => activeMood ? r.mood.includes(activeMood.toLowerCase()) : true)
    .filter(r =>
      search === '' ||
      r.name.toLowerCase().includes(search.toLowerCase()) ||
      r.cuisine.toLowerCase().includes(search.toLowerCase()) ||
      r.address.toLowerCase().includes(search.toLowerCase())
    )
    .slice(0, 8)

  const handleSearch = (e) => {
    e.preventDefault()
    navigate(`/restaurants?q=${search}&mood=${activeMood}`)
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Hero */}
      <section className="relative overflow-hidden" style={{ background: 'linear-gradient(135deg, #1c0a00 0%, #3d1500 50%, #1c0a00 100%)' }}>
        <div className="absolute inset-0 opacity-5" style={{ backgroundImage: 'radial-gradient(circle at 20% 50%, #F97316 0%, transparent 50%), radial-gradient(circle at 80% 20%, #F97316 0%, transparent 40%)' }} />
        <div className="relative max-w-7xl mx-auto px-6 py-16 flex flex-col md:flex-row items-center gap-12">
          <div className="flex-1">
            <h1 className="text-4xl md:text-5xl font-bold text-white leading-tight mb-4">
              Find &amp; Reserve Your<br />
              <span style={{ fontFamily: "'Playfair Display', serif", fontStyle: 'italic' }} className="text-orange-400">Perfect Dining Spot</span>
            </h1>
            <p className="text-white/60 text-base mb-6">Browse restaurants and book tables instantly</p>
            <form onSubmit={handleSearch} className="flex gap-3 max-w-lg">
              <input
                value={search}
                onChange={e => setSearch(e.target.value)}
                placeholder="e.g. Restaurant near UTM..."
                className="flex-1 px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-white/40 focus:outline-none focus:border-orange-400 text-sm"
              />
              <button type="submit" className="bg-brand hover:bg-brand-dark text-white px-6 py-3 rounded-xl font-semibold text-sm transition-all">
                Search
              </button>
            </form>
          </div>

          <div className="flex flex-col gap-3 min-w-[220px]">
            {[
              { title: "We've received your order!", sub: "Awaiting restaurant acceptance", num: "1" },
              { title: "Order Accepted! ✓", sub: "Your order will be delivered shortly", num: "2", green: true },
              { title: "Your rider's nearby 🛵", sub: "They're almost there – get ready!", num: "3" },
            ].map(n => (
              <div key={n.num} className="bg-white/10 backdrop-blur border border-white/15 rounded-xl p-3 flex items-start gap-3">
                <div className="w-6 h-6 rounded-full border border-white/30 flex items-center justify-center text-xs text-white/60 font-bold flex-shrink-0 mt-0.5">{n.num}</div>
                <div>
                  <p className={`text-sm font-semibold ${n.green ? 'text-green-400' : 'text-white'}`}>{n.title}</p>
                  <p className="text-xs text-white/50">{n.sub}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Mood + Filter bar */}
      <div className="bg-white border-b border-gray-100 sticky top-14 z-40">
        <div className="max-w-7xl mx-auto px-6 py-3 flex items-center gap-3 flex-wrap">
          <span className="text-sm font-semibold text-gray-700">Dining Mood:</span>
          <div className="flex gap-2">
            {moodFilters.map(m => (
              <button
                key={m}
                onClick={() => setActiveMood(activeMood === m ? '' : m)}
                className={`text-sm px-4 py-1.5 rounded-full border transition-all font-medium ${
                  activeMood === m
                    ? 'bg-brand text-white border-brand'
                    : 'border-gray-200 text-gray-500 hover:border-brand hover:text-brand'
                }`}
              >
                {m}
              </button>
            ))}
          </div>
          <div className="h-5 w-px bg-gray-200 mx-1" />
          <div className="flex gap-2 ml-auto">
            {['Cuisine ▾', 'Location ▾', 'Rating ▾'].map(f => (
              <button key={f} className="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:border-brand hover:text-brand transition-all">
                {f}
              </button>
            ))}
          </div>
        </div>
      </div>

      {/* Restaurant grid */}
      <div className="max-w-7xl mx-auto px-6 py-10">
        <div className="flex items-center justify-between mb-6">
          <h2 className="text-lg font-bold text-gray-900">Popular Restaurants</h2>
          <button onClick={() => navigate('/restaurants')} className="text-sm text-brand hover:underline font-medium">
            View all →
          </button>
        </div>

        {filtered.length === 0 ? (
          <div className="text-center py-16 text-gray-400">
            <div className="text-4xl mb-3">🔍</div>
            <p>No restaurants match your search.</p>
          </div>
        ) : (
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            {filtered.map(r => <RestaurantCard key={r.id} restaurant={r} />)}
          </div>
        )}
      </div>

      <Footer />
    </div>
  )
}
