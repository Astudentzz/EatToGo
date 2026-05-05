import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useApp } from '../context/AppContext'

export default function Register() {
  const navigate = useNavigate()
  const { login } = useApp()
  const [form, setForm] = useState({ name: '', mobile: '', email: '', password: '', confirm: '' })
  const [error, setError] = useState('')

  const handle = (e) => {
    e.preventDefault()
    if (!form.name || !form.email || !form.password) return setError('Please fill in all required fields.')
    if (form.password !== form.confirm) return setError('Passwords do not match.')
    if (form.password.length < 6) return setError('Password must be at least 6 characters.')
    login({ name: form.name, email: form.email })
    navigate('/')
  }

  const f = (key) => ({
    value: form[key],
    onChange: e => { setForm({ ...form, [key]: e.target.value }); setError('') }
  })

  return (
    <div className="min-h-screen bg-gray-900 flex items-center justify-center p-6"
      style={{ backgroundImage: 'radial-gradient(ellipse at 80% 50%, rgba(249,115,22,0.08) 0%, transparent 60%)' }}
    >
      <div className="w-full max-w-md">
        <div className="text-center mb-8">
          <Link to="/" className="inline-flex items-center gap-1 mb-6">
            <span className="text-2xl font-bold text-white">Order</span>
            <span className="bg-brand text-white text-sm px-2 py-0.5 rounded font-bold">uk</span>
          </Link>
          <h1 className="text-2xl font-bold text-white">Create Account</h1>
          <p className="text-gray-400 text-sm mt-1">Join EatToGo today</p>
        </div>

        <div className="bg-white/5 backdrop-blur border border-white/10 rounded-2xl p-8">
          {error && <p className="text-red-400 text-sm mb-4 text-center">{error}</p>}
          <form onSubmit={handle} className="space-y-4">
            <div>
              <label className="block text-xs font-medium text-gray-400 mb-1.5">Full Name *</label>
              <input {...f('name')} placeholder="Bob Smith" type="text"
                className="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/10 text-white placeholder-white/30 focus:outline-none focus:border-brand text-sm" />
            </div>
            <div>
              <label className="block text-xs font-medium text-gray-400 mb-1.5">Mobile Number</label>
              <div className="flex gap-2">
                <div className="flex items-center gap-2 px-3 py-3 rounded-xl bg-white/10 border border-white/10 text-white text-sm flex-shrink-0">
                  🇬🇧 +44
                </div>
                <input {...f('mobile')} placeholder="7700 900000" type="tel"
                  className="flex-1 px-4 py-3 rounded-xl bg-white/10 border border-white/10 text-white placeholder-white/30 focus:outline-none focus:border-brand text-sm" />
              </div>
            </div>
            <div>
              <label className="block text-xs font-medium text-gray-400 mb-1.5">Email Address *</label>
              <input {...f('email')} placeholder="BobSmith22@gmail.com" type="email"
                className="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/10 text-white placeholder-white/30 focus:outline-none focus:border-brand text-sm" />
            </div>
            <div>
              <label className="block text-xs font-medium text-gray-400 mb-1.5">Password *</label>
              <input {...f('password')} placeholder="••••••••" type="password"
                className="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/10 text-white placeholder-white/30 focus:outline-none focus:border-brand text-sm" />
            </div>
            <div>
              <label className="block text-xs font-medium text-gray-400 mb-1.5">Confirm Password *</label>
              <input {...f('confirm')} placeholder="••••••••" type="password"
                className="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/10 text-white placeholder-white/30 focus:outline-none focus:border-brand text-sm" />
            </div>
            <button type="submit" className="w-full bg-brand hover:bg-brand-dark text-white py-3 rounded-xl font-semibold text-sm transition-all mt-2">
              Register
            </button>
          </form>

          <p className="text-center text-sm text-gray-400 mt-6">
            Already a user?{' '}
            <Link to="/login" className="text-brand hover:underline font-medium">Sign in</Link>
          </p>
        </div>
      </div>
    </div>
  )
}
