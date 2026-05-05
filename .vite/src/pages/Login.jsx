import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useApp } from '../context/AppContext'

export default function Login() {
  const navigate = useNavigate()
  const { login } = useApp()
  const [form, setForm] = useState({ email: '', password: '' })
  const [error, setError] = useState('')

  const handle = (e) => {
    e.preventDefault()
    if (!form.email || !form.password) return setError('Please fill in all fields.')
    login({ name: form.email.split('@')[0], email: form.email })
    navigate('/')
  }

  return (
    <div className="min-h-screen bg-gray-900 flex items-center justify-center p-6"
      style={{ backgroundImage: 'radial-gradient(ellipse at 20% 50%, rgba(249,115,22,0.08) 0%, transparent 60%), radial-gradient(ellipse at 80% 20%, rgba(249,115,22,0.05) 0%, transparent 50%)' }}
    >
      <div className="w-full max-w-md">
        <div className="text-center mb-8">
          <Link to="/" className="inline-flex items-center gap-1 mb-6">
            <span className="text-2xl font-bold text-white">Order</span>
            <span className="bg-brand text-white text-sm px-2 py-0.5 rounded font-bold">uk</span>
          </Link>
          <h1 className="text-2xl font-bold text-white">Welcome Back</h1>
          <p className="text-gray-400 text-sm mt-1">Login to your EatToGo account</p>
        </div>

        <div className="bg-white/5 backdrop-blur border border-white/10 rounded-2xl p-8">
          {error && <p className="text-red-400 text-sm mb-4 text-center">{error}</p>}
          <form onSubmit={handle} className="space-y-4">
            <div>
              <label className="block text-xs font-medium text-gray-400 mb-1.5">Email Address</label>
              <input
                type="email"
                value={form.email}
                onChange={e => setForm({ ...form, email: e.target.value })}
                placeholder="BobSmith22@gmail.com"
                className="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/10 text-white placeholder-white/30 focus:outline-none focus:border-brand text-sm"
              />
            </div>
            <div>
              <label className="block text-xs font-medium text-gray-400 mb-1.5">Password</label>
              <input
                type="password"
                value={form.password}
                onChange={e => setForm({ ...form, password: e.target.value })}
                placeholder="••••••••"
                className="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/10 text-white placeholder-white/30 focus:outline-none focus:border-brand text-sm"
              />
            </div>
            <div className="text-right">
              <a href="#" className="text-xs text-brand hover:underline">Forgot password?</a>
            </div>
            <button type="submit" className="w-full bg-brand hover:bg-brand-dark text-white py-3 rounded-xl font-semibold text-sm transition-all mt-2">
              Login
            </button>
          </form>

          <p className="text-center text-sm text-gray-400 mt-6">
            Don't have an account?{' '}
            <Link to="/register" className="text-brand hover:underline font-medium">Register here</Link>
          </p>
        </div>
      </div>
    </div>
  )
}
