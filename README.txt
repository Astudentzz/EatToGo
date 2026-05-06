EatToGo – Complete Frontend Prototype (v2 – Fixed & Complete)
=============================================================

Open in VS Code. Recommended: install Live Server extension and run index.html.

DEMO ROLES (login with any password):
  customer@eattogo.test  → Customer
  staff@eattogo.test     → Restaurant Staff
  owner@eattogo.test     → Restaurant Owner
  admin@eattogo.test     → Admin

DATA: Stored in browser localStorage for demo. Backend teammate connects PHP/Laravel/MySQL.

PAGES (14 total — all proposal use cases covered):
  index.html             → Home / Browse Restaurants
  login.html             → Sign In
  signup.html            → Sign Up
  forgot-password.html   → Forgot Password
  search-results.html    → Search & Filter Restaurants
  restaurant.html        → Restaurant Detail + Reserve Table
  preorder.html          → Pre-Order Food (menu + cart)
  pay-counter.html       → Pay at Counter notification
  booking-success.html   → Booking Confirmation
  booking-history.html   → My Bookings (per-row Confirm Arrival)
  order-status.html      → [NEW] Order Status Tracker (step-by-step timeline)
  feedback.html          → Upload Feedback after dining
  staff-dashboard.html   → Staff: Reservations + Orders + Menu Management [FIXED]
  owner-dashboard.html   → Owner: Submit restaurant request
  admin.html             → Admin: Full CRUD – Bookings, Restaurants, Menu, Accounts, Requests [FIXED]

FIXES IN v2:
  - [NEW] order-status.html: step-by-step order tracker for customers
  - admin.html: Full restaurant CRUD (Add/Update/Delete), full menu CRUD (Add/Update/Remove),
                working "Send Feedback Request" button, stats cards
  - staff-dashboard.html: Fixed sidebar links, working menu Add/Remove,
                           working order status update (Mark Ready), stats cards
  - booking-history.html: Per-row Confirm Arrival button, Track Order link per row
  - All customer navbars: Added "Order Status" link
  - Restaurants & menu from admin/staff management now reflected across the whole site
    (customRestaurants and customMenu in localStorage)
