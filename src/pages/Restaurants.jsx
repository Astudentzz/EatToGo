import { useState, useEffect } from 'react'
import { useSearchParams } from 'react-router-dom'
import { restaurants, moodFilters, cuisines } from '../data/restaurants'
import RestaurantCard from '../components/RestaurantCard'
import Footer from '../components/Footer'

export default function Restaurants() {
  const [searchParams] = useSearchParams()
  const [search, setSearch] = useState(searchParams.get('q') || '')
  const [activeMood, setActiveMood] = useState(searchParams.get('mood') || '')
  const [activeCuisine, setActiveCuisine] = useState('All')
  const [activeRating, setActiveRating] = useState(0)
  const [activeStatus, setActiveStatus] = useState('all')

  const filtered = restaurants.filter(r => {
    const matchSearch = search === '' ||
      r.name.toLowerCase().includes(search.toLowerCase()) ||
      r.cuisine.toLowerCase().includes(search.toLowerCase()) ||
      r.address.toLowerCase().includes(search.toLowerCase())
    const matchMood = activeMood === '' || r.mood.includes(activeMood.toLowerCase())
    const matchCuisine = activeCuisine === 'All' || r.cuisine === activeCuisine
    const matchRating = r.rating >= activeRating
    const matchStatus = activeStatus === 'all' || r.status === activeStatus
    return matchSearch && matchMood && matchCuisine && matchRating && matchStatus
  })

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Hero search banner */}
      <div className="bg-gray-900 py-10">
        <div className="max-w-7xl mx-auto px-6">
          <h1 className="text-3xl font-bold text-white mb-5">Explore Restaurants</h1>
          <div className="flex gap-3 max-w-2xl">
            <input
              value={search}
              onChange={e => setSearch(e.target.value)}
              placeholder="Search by name, cuisine, or location..."
              className="flex-1 px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-white/40 focus:outline-none focus:border-orange-400 text-sm"
            />
            <button className="bg-brand text-white px-6 py-3 rounded-xl font-semibold text-sm hover:bg-brand-dark transition-all">
              Search
            </button>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-6 py-6">
        {/* Filters row */}
        <div className="bg-white rounded-xl border border-gray-100 p-4 mb-6 flex flex-wrap gap-4 items-center">
          <div className="flex items-center gap-2 flex-wrap">
            <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider">Mood:</span>
            {moodFilters.map(m => (
              <button
                key={m}
                onClick={() => setActiveMood(activeMood === m ? '' : m)}
                className={`text-xs px-3 py-1.5 rounded-full border transition-all font-medium ${
                  activeMood === m ? 'bg-brand text-white border-brand' : 'border-gray-200 text-gray-500 hover:border-brand hover:text-brand'
                }`}
              >
                {m}
              </button>
            ))}
          </div>
          <div className="w-px h-6 bg-gray-200" />
          <div className="flex items-center gap-2 flex-wrap">
            <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider">Cuisine:</span>
            {cuisines.map(c => (
              <button
                key={c}
                onClick={() => setActiveCuisine(c)}
                className={`text-xs px-3 py-1.5 rounded-full border transition-all font-medium ${
                  activeCuisine === c ? 'bg-brand text-white border-brand' : 'border-gray-200 text-gray-500 hover:border-brand hover:text-brand'
                }`}
              >
                {c}
              </button>
            ))}
          </div>
          <div className="w-px h-6 bg-gray-200" />
          <div className="flex items-center gap-2">
            <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider">Status:</span>
            {['all', 'available', 'limited', 'full'].map(s => (
              <button
                key={s}
                onClick={() => setActiveStatus(s)}
                className={`text-xs px-3 py-1.5 rounded-full border transition-all font-medium capitalize ${
                  activeStatus === s ? 'bg-brand text-white border-brand' : 'border-gray-200 text-gray-500 hover:border-brand hover:text-brand'
                }`}
              >
                {s}
              </button>
            ))}
          </div>
        </div>

        {/* Results */}
        <div className="flex items-center justify-between mb-4">
          <p className="text-sm text-gray-500">{filtered.length} restaurants found</p>
        </div>

        {filtered.length === 0 ? (
          <div className="text-center py-20 text-gray-400">
            <div className="text-5xl mb-4">🔍</div>
            <p className="text-lg font-medium">No restaurants found</p>
            <p className="text-sm mt-1">Try adjusting your filters</p>
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
