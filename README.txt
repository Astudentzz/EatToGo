EatToGo – Frontend Prototype v9 (Proposal Gap Fix – Complete)
=============================================================

Open in VS Code with Live Server extension. Run index.html.

DEMO LOGIN ACCOUNTS (any password works):
  customer@eattogo.test  → Customer
  staff@eattogo.test     → Restaurant Staff
  owner@eattogo.test     → Restaurant Owner
  admin@eattogo.test     → Admin

DATA: Stored in browser localStorage. Backend teammate connects PHP/Laravel/MySQL.

PAGES (18 total – all proposal use cases covered):
  index.html              → Home / Browse Restaurants (View Details + Reserve buttons)
  login.html              → Sign In (toast notifications, role validation)
  signup.html             → Sign Up (role pre-select for owner CTA)
  forgot-password.html    → Forgot Password
  search-results.html     → Search & Filter (dynamic cuisine filter from restaurant data)
  restaurant-detail.html  → View Restaurant Details (PROPOSAL: "View restaurant details")
  restaurant.html         → Reserve Table (dynamic per restaurant ID)
  preorder.html           → Pre-Order Food (visual cart, qty controls)
  checkout.html           → Customer Details form
  receipt.html            → Booking Receipt (itemized order)
  pay-counter.html        → Pay at Counter instruction + cash-only alert
  booking-success.html    → Booking Confirmation
  booking-history.html    → My Bookings (confirm arrival, track, feedback)
  order-status.html       → Order Status Tracker
  feedback.html           → Dining Feedback (shows admin request notice)
  staff-dashboard.html    → Staff: Reservations, Orders, Menu Availability
  owner-dashboard.html    → Owner: Reservations, Menu CRUD, Request Restaurant Info
  admin.html              → Admin: Full CRUD – Restaurants, Menu, Accounts, Requests, Feedback

PROPOSAL COMPLIANCE SUMMARY:
  ✅ Customer: Sign in/up/out, forgot password, browse restaurants, view restaurant details,
     view menu, cash payment alert, book reservation, confirm reservation, receive confirmation,
     place orders, view total, pay at counter, view order status, confirm arrival, receive feedback request, upload feedback
  ✅ Staff: Sign in/up/out, forgot password, view restaurant info, view/add/remove menu item,
     receive reservation & order details, view details, send confirmation, confirm reservations,
     reject reservations (owner-only enforced), update order status, receive arrival confirmation
  ✅ Admin: Sign in/up/out, forgot password, view customer/staff/owner lists, view restaurant info requests,
     approve/reject requests, add/update/delete restaurant info, add/update/remove menu items,
     send feedback request, delete accounts
  ✅ Owner: Sign in/up/out, forgot password, request to add restaurant information, receive request result

FIXES IN v9:
  - app.js fully rewritten: clean, consistent, all functions connected
  - restaurant.html: fully dynamic per ?id= URL param (no more hardcoded "Sakura Omakase")
  - restaurant-detail.html: proper dedicated "View Details" page (separate from reservation)
  - index.html: duplicate team member name removed
  - receipt.html: proper navbar and footer added
  - renderRestaurants(): now renders "View Details" + "Reserve Table" as separate buttons
  - sendFeedbackRequest(): wired to feedback.html notice + booking table badge
  - renderOrders(): shows emoji + customer name for each order row
  - renderBookings(): staff-specific actions (can't reject), owner/admin can send feedback request
  - renderAccounts(): role colour badges
  - requestStatus(): updates owner-facing result text after admin action
  - All seed data corrected (no duplicate entries)
