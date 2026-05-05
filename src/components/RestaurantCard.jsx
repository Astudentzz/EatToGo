import { Link } from 'react-router-dom'

const statusConfig = {
  available: { label: 'Available now', cls: 'bg-green-100 text-green-700' },
  limited: { label: 'Limited seats', cls: 'bg-yellow-100 text-yellow-700' },
  full: { label: 'Fully booked', cls: 'bg-red-100 text-red-700' },
}

export default function RestaurantCard({ restaurant }) {
  const { id, name, address, rating, status, slots, takenSlots, emoji, bgColor } = restaurant
  const s = statusConfig[status]

  return (
    <div className="bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-md transition-all hover:-translate-y-0.5 group">
      <div
        className="h-28 flex items-center justify-center text-5xl relative"
        style={{ backgroundColor: bgColor }}
      >
        <span>{emoji}</span>
        <div className="absolute top-2 right-2 bg-black/60 text-yellow-400 text-xs px-2 py-0.5 rounded font-semibold">
          ★ {rating.toFixed(1)}
        </div>
      </div>

      <div className="p-3">
        <h3 className="text-sm font-semibold text-gray-900 leading-tight mb-1">{name}</h3>
        <p className="text-xs text-gray-400 mb-2 truncate">{address}</p>

        <div className="flex items-center gap-2 mb-2">
          <span className={`text-xs px-2 py-0.5 rounded-full font-medium ${s.cls}`}>{s.label}</span>
        </div>

        <div className="flex gap-1 flex-wrap mb-3">
          {slots.slice(0, 4).map(slot => (
            <span
              key={slot}
              className={`text-xs px-2 py-0.5 rounded font-medium ${
                takenSlots.includes(slot)
                  ? 'bg-gray-100 text-gray-300'
                  : 'bg-brand-light text-brand-dark border border-orange-200'
              }`}
            >
              {slot}
            </span>
          ))}
        </div>

        <Link
          to={`/restaurants/${id}`}
          className="block w-full text-center text-xs py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-brand hover:text-white hover:border-brand transition-all font-medium"
        >
          View details
        </Link>
      </div>
    </div>
  )
}
