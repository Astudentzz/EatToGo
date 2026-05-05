import { Link } from 'react-router-dom'

export default function Footer() {
  return (
    <footer className="bg-gray-50 border-t border-gray-100 mt-16">
      <div className="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
          <div className="flex items-center gap-1 mb-3">
            <span className="text-lg font-bold text-gray-900">Order</span>
            <span className="bg-brand text-white text-xs px-1.5 py-0.5 rounded font-bold">uk</span>
          </div>
          <p className="text-sm text-gray-500 mb-4">Get exclusive deals in your inbox</p>
          <div className="flex gap-2">
            <input
              type="email"
              placeholder="youremail@gmail.com"
              className="flex-1 text-sm px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:border-brand"
            />
            <button className="bg-brand text-white text-sm px-4 py-2 rounded-lg hover:bg-brand-dark transition-all">
              Subscribe
            </button>
          </div>
          <p className="text-xs text-gray-400 mt-2">We won't spam. Read our email policy.</p>
        </div>

        <div>
          <h4 className="text-sm font-semibold text-gray-900 mb-3">Legal Pages</h4>
          <ul className="space-y-2 text-sm text-gray-500">
            <li><a href="#" className="hover:text-brand transition-colors">Terms and conditions</a></li>
            <li><a href="#" className="hover:text-brand transition-colors">Privacy</a></li>
            <li><a href="#" className="hover:text-brand transition-colors">Cookies</a></li>
            <li><a href="#" className="hover:text-brand transition-colors">Modern Slavery Statement</a></li>
          </ul>
        </div>

        <div>
          <h4 className="text-sm font-semibold text-gray-900 mb-3">Important Links</h4>
          <ul className="space-y-2 text-sm text-gray-500">
            <li><a href="#" className="hover:text-brand transition-colors">Get help</a></li>
            <li><a href="#" className="hover:text-brand transition-colors">Add your restaurant</a></li>
            <li><a href="#" className="hover:text-brand transition-colors">Sign up to deliver</a></li>
            <li><a href="#" className="hover:text-brand transition-colors">Create a business account</a></li>
          </ul>
        </div>

        <div>
          <h4 className="text-sm font-semibold text-gray-900 mb-3">Follow us</h4>
          <div className="flex gap-3">
            {['f', 'in', 'tw', 'yt'].map(s => (
              <div key={s} className="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-500 hover:bg-brand hover:text-white cursor-pointer transition-all">
                {s}
              </div>
            ))}
          </div>
        </div>
      </div>
      <div className="border-t border-gray-100 py-4 px-6 max-w-7xl mx-auto flex flex-wrap gap-4 justify-between text-xs text-gray-400">
        <span>Order.uk Copyright 2024, All Rights Reserved.</span>
        <div className="flex gap-4">
          <a href="#" className="hover:text-brand">Privacy Policy</a>
          <a href="#" className="hover:text-brand">Terms</a>
          <a href="#" className="hover:text-brand">Pricing</a>
        </div>
      </div>
    </footer>
  )
}
