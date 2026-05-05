# EatToGo — Frontend

A React + Vite + Tailwind CSS web application for restaurant discovery and table reservations.

## Pages
- `/` — Home with hero, mood filters, restaurant grid
- `/restaurants` — Browse & filter all restaurants
- `/restaurants/:id` — Restaurant detail with menu + booking
- `/reservations` — Manage your reservations (requires login)
- `/login` — Login page
- `/register` — Register page

## Getting started

```bash
npm install
npm run dev
```

Open http://localhost:5173

## Tech Stack
- React 18
- React Router v6
- Tailwind CSS v3
- Vite 5

## GitHub setup

```bash
git init
git add .
git commit -m "initial: EatToGo frontend"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/eattogo.git
git push -u origin main
```

## Suggested branches
- `feature/auth` — Login & Register pages
- `feature/home` — Home page
- `feature/restaurants` — Restaurant listing & detail
- `feature/reservations` — Reservations page
