// ─── EatToGo app.js v9 – Complete, Proposal-Aligned ──────────────────────────

// ─── SEED DATA ────────────────────────────────────────────────────────────────
const ETG = {
  restaurants: [
    {id:1,name:'Sakura Omakase',cuisine:'Japanese',rating:4.8,loc:'Kuala Lumpur',price:'RM25-60',emoji:'🍣',deal:'15% student deal',hours:'10:00 AM - 10:00 PM'},
    {id:2,name:'Spice Garden',cuisine:'Indian',rating:4.7,loc:'Skudai',price:'RM12-35',emoji:'🍛',deal:'Free drink',hours:'11:00 AM - 11:00 PM'},
    {id:3,name:'Pasta Street',cuisine:'Italian',rating:4.6,loc:'Johor Bahru',price:'RM18-45',emoji:'🍝',deal:'Combo save RM8',hours:'12:00 PM - 10:30 PM'},
    {id:4,name:'Burger Lab',cuisine:'Western',rating:4.5,loc:'Senai',price:'RM15-40',emoji:'🍔',deal:'Lunch promo',hours:'10:30 AM - 10:00 PM'}
  ],
  menu: [
    {id:1,cat:'Popular',name:'Salmon Sushi Set',price:28,emoji:'🍣',available:true},
    {id:2,cat:'Popular',name:'Chicken Katsu Don',price:18,emoji:'🍱',available:true},
    {id:3,cat:'Hot Dishes',name:'Spicy Ramen',price:22,emoji:'🍜',available:true},
    {id:4,cat:'Drinks',name:'Matcha Latte',price:9,emoji:'🍵',available:true},
    {id:5,cat:'Sides',name:'Edamame',price:7,emoji:'🫛',available:true},
    {id:6,cat:'Popular',name:'Tempura Set',price:26,emoji:'🍤',available:true}
  ]
};

// ─── UTILS ────────────────────────────────────────────────────────────────────
function $(q){ return document.querySelector(q); }
function $all(q){ return [...document.querySelectorAll(q)]; }
function get(k,d){ try{ const v = localStorage.getItem(k); return v !== null ? JSON.parse(v) : d; } catch(e){ return d; } }
function set(k,v){ localStorage.setItem(k, JSON.stringify(v)); }
function roleHome(r){ return r==='admin'?'admin.html':r==='staff'?'staff-dashboard.html':r==='owner'?'owner-dashboard.html':'index.html'; }
function currentUser(){ return get('session', null); }
function allRestaurants(){ return get('customRestaurants', ETG.restaurants); }
function allMenu(){ return get('customMenu', ETG.menu).map(x=>({...x, available: x.available !== false})); }
function getRestaurantById(id){ return allRestaurants().find(r=>String(r.id)===String(id)) || allRestaurants()[0]; }

// ─── SEED DEMO DATA ───────────────────────────────────────────────────────────
function seed(){
  if(!get('users',null)) set('users',[
    {name:'Navin Ramu',   email:'customer@eattogo.test', role:'customer'},
    {name:'Staff Member', email:'staff@eattogo.test',    role:'staff'},
    {name:'Restaurant Owner', email:'owner@eattogo.test',role:'owner'},
    {name:'Admin',        email:'admin@eattogo.test',    role:'admin'}
  ]);
  if(!get('bookings',null)) set('bookings',[
    {id:'B1001',customer:'Navin Ramu',phone:'0123456789',email:'customer@eattogo.test',
     restaurant:'Sakura Omakase',date:'2026-05-10',time:'7:30 PM',guests:2,
     status:'pending',arrival:'Not arrived',payment:'Pay at counter',comments:'Window seat if available.',feedbackSent:false}
  ]);
  if(!get('orders',null)) set('orders',[
    {id:'O3001',bookingId:'B1001',
     customer:{name:'Navin Ramu',phone:'0123456789',email:'customer@eattogo.test',comments:'Window seat if available.'},
     restaurant:'Sakura Omakase',date:'2026-05-10',time:'7:30 PM',guests:2,
     items:[{id:1,name:'Salmon Sushi Set',qty:2,price:28,subtotal:56},{id:4,name:'Matcha Latte',qty:1,price:9,subtotal:9}],
     total:65,status:'Preparing',payment:'Pay at counter'}
  ]);
  if(!get('requests',null)) set('requests',[
    {id:'R001',owner:'Ng Yue Yang',restaurant:'Yang Noodles',cuisine:'Chinese',
     details:'New restaurant listing request.',status:'pending',
     result:'Submitted to admin. Waiting for approval.'}
  ]);
  if(!get('feedbackRequests',null)) set('feedbackRequests',[]);
  if(!get('feedback',null)) set('feedback',[]);
}
seed();

// ─── AUTH NAV ────────────────────────────────────────────────────────────────
function nav(){
  const u = currentUser();
  const box = $('#authArea');
  if(box) box.innerHTML = u
    ? `<span class="me-2 fw-bold">${u.name}</span><button onclick="logout()" class="btn btn-outline-etg btn-sm">Sign Out</button>`
    : `<a href="login.html" class="btn btn-outline-etg btn-sm">Sign In</a>`;
}

function logout(){ localStorage.removeItem('session'); location.href='login.html'; }

function signup(e){
  e.preventDefault();
  const u = {name:$('#name').value.trim(), email:$('#email').value.trim(), role:$('#role').value};
  const users = get('users',[]);
  if(users.find(x=>x.email.toLowerCase()===u.email.toLowerCase())){
    alert('This email is already registered. Please sign in.'); return;
  }
  users.push(u); set('users',users); set('session',u);
  location.href = u.role==='owner'?'owner-dashboard.html':u.role==='staff'?'staff-dashboard.html':u.role==='admin'?'admin.html':'index.html';
}

function forgot(e){
  e.preventDefault();
  alert('Demo: A password reset link has been sent to your email.\nBackend teammate can connect real email service later.');
  location.href = 'login.html';
}

function requireCustomer(action='continue'){
  const u = currentUser();
  if(!u){
    set('redirectAfterLogin', location.pathname.split('/').pop()+location.search);
    alert('Please login or register before you ' + action + '.');
    location.href = 'login.html'; return null;
  }
  if(u.role !== 'customer'){
    alert('Only customer accounts can reserve tables, add preorder items, and checkout.');
    location.href = roleHome(u.role); return null;
  }
  return u;
}

// ─── HOME / SEARCH ───────────────────────────────────────────────────────────
function searchHome(e){
  e.preventDefault();
  location.href = 'search-results.html?q=' + encodeURIComponent($('#q')?.value||'');
}

function renderCuisineFilter(){
  const el = $('#cuisine'); if(!el) return;
  const cur = el.value;
  const cuisines = [...new Set(allRestaurants().map(r=>r.cuisine))].sort();
  el.innerHTML = '<option value="">All cuisines</option>' + cuisines.map(c=>`<option value="${c}">${c}</option>`).join('');
  if(cur) el.value = cur;
}

function filterResults(){
  const params = new URLSearchParams(location.search);
  const input = $('#q');
  if(input && !input.dataset.loaded){ input.value = params.get('q')||''; input.dataset.loaded='1'; }
  const q = (input?.value || params.get('q') || '').toLowerCase();
  const cuisine = $('#cuisine')?.value || '';
  const list = allRestaurants().filter(r=>
    (!q || r.name.toLowerCase().includes(q) || r.cuisine.toLowerCase().includes(q) || r.loc.toLowerCase().includes(q)) &&
    (!cuisine || r.cuisine===cuisine)
  );
  renderRestaurants(list);
  const empty = $('#emptyResults');
  if(empty) empty.classList.toggle('d-none', list.length > 0);
}

// ─── RESTAURANT CARDS ─────────────────────────────────────────────────────────
function renderRestaurants(list=allRestaurants()){
  const el = $('#restaurantList'); if(!el) return;
  const isAdminPage = location.pathname.includes('admin.html');
  el.innerHTML = list.map(r=>`
    <div class="col-md-6 col-lg-3">
      <div class="restaurant-card h-100">
        <div class="food-img">${r.emoji}</div>
        <div class="p-3">
          <span class="badge-etg">${r.deal||'Featured'}</span>
          <h4 class="mt-3">${r.name}</h4>
          <p class="text-muted mb-1">${r.cuisine} • ${r.loc}</p>
          <p>⭐ ${r.rating} • ${r.price}</p>
          <div class="d-grid gap-2">
            <a class="btn btn-outline-etg" href="restaurant-detail.html?id=${r.id}">View Details</a>
            <a class="btn btn-etg" href="restaurant.html?id=${r.id}">Reserve Table</a>
            ${isAdminPage ? `
              <button class="btn btn-warning btn-sm" onclick="editRestaurantAdmin(${r.id})">✏️ Edit</button>
              <button class="btn btn-danger btn-sm" onclick="deleteRestaurantAdmin(${r.id})">🗑️ Delete</button>` : ''}
          </div>
        </div>
      </div>
    </div>`).join('');
}

// ─── RESTAURANT DETAIL PAGE (restaurant-detail.html) ─────────────────────────
function renderRestaurantInfoPage(){
  const el = $('#restaurantInfoPage'); if(!el) return;
  const r = getRestaurantById(new URLSearchParams(location.search).get('id'));
  set('selectedRestaurantId', r.id);
  document.title = r.name + ' – Details – EatToGo';
  el.innerHTML = `
    <section class="restaurant-hero">
      <div class="restaurant-hero-img">
        <div class="restaurant-overlay">
          <span class="badge-etg">Restaurant Details • ${r.cuisine} • ${r.loc}</span>
          <h1>${r.name}</h1>
          <p>View restaurant profile, menu highlights, operating hours, and reviews before making a reservation.</p>
          <div class="hero-meta">
            <span>⭐ ${r.rating}</span>
            <span>📍 ${r.loc}</span>
            <span>💵 ${r.price}</span>
            <span>🕒 ${r.hours}</span>
          </div>
        </div>
      </div>
    </section>
    <section class="section pt-4">
      <div class="container">
        <div class="row g-4 align-items-start">
          <div class="col-lg-8">
            <div class="card-etg p-4 mb-4">
              <h2>About ${r.name}</h2>
              <p class="text-muted">${r.name} is a ${r.cuisine.toLowerCase()} restaurant located in ${r.loc}. Operating hours: ${r.hours}.</p>
              <p class="text-muted mb-0">Current promotion: <b>${r.deal}</b></p>
            </div>
            <div class="gallery-grid mb-4">
              <div>${r.emoji}<small>Main Dining Area</small></div>
              <div>🍽️<small>Chef Specials</small></div>
              <div>🪑<small>Comfort Seating</small></div>
              <div>📸<small>Gallery</small></div>
            </div>
            <div class="card-etg p-4 mb-4">
              <h2>Menu Highlights</h2>
              <div class="row g-3">
                ${allMenu().slice(0,4).map(i=>`
                  <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3 p-2 border rounded-3">
                      <span style="font-size:32px">${i.emoji}</span>
                      <div><b>${i.name}</b><br><small class="text-muted">RM ${i.price.toFixed(2)} • ${i.cat}</small></div>
                    </div>
                  </div>`).join('')}
              </div>
            </div>
            <div class="card-etg p-4">
              <h2>Customer Reviews</h2>
              <div class="review-card"><b>⭐ 5.0 — Sarah L.</b><p>Excellent food and fast service. The pre-order feature made dining so smooth!</p></div>
              <div class="review-card"><b>⭐ 4.8 — Rahman A.</b><p>Clear restaurant info, easy to view details before booking. Highly recommend.</p></div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="card-etg p-4 summary-card">
              <h3>Restaurant Info</h3>
              <p><b>Cuisine:</b> ${r.cuisine}</p>
              <p><b>Location:</b> ${r.loc}</p>
              <p><b>Hours:</b> ${r.hours}</p>
              <p><b>Price Range:</b> ${r.price}</p>
              <p><b>Promotion:</b> ${r.deal}</p>
              <hr>
              <a class="btn btn-etg w-100" href="restaurant.html?id=${r.id}">Reserve a Table →</a>
              <a class="btn btn-outline-etg w-100 mt-2" href="search-results.html">← Browse All</a>
            </div>
          </div>
        </div>
      </div>
    </section>
    <footer class="minimal-footer">
      <div class="container d-flex justify-content-between flex-wrap gap-5">
        <div class="footer-brand"><h2>Eat<span>To</span>Go</h2><p>Final Year Project – Restaurant and Food Reservation System.</p></div>
        <div class="footer-members"><h4>Project Team</h4><ul class="list-unstyled"><li>Navin Ramu</li><li>Muhamamd Nazim</li><li>Hadif</li><li>Ng Yue Yang</li></ul></div>
      </div>
      <div class="footer-bottom">© 2026 EatToGo • Universiti Teknologi Malaysia (UTM)</div>
    </footer>`;
}

// ─── RESTAURANT RESERVE PAGE (restaurant.html) ───────────────────────────────
function renderRestaurantDetail(){
  const el = $('#restaurantDetail'); if(!el) return;
  const r = getRestaurantById(new URLSearchParams(location.search).get('id'));
  set('selectedRestaurantId', r.id);
  document.title = r.name + ' – Reserve – EatToGo';
  const today = new Date().toISOString().split('T')[0];
  el.innerHTML = `
    <section class="restaurant-hero">
      <div class="restaurant-hero-img">
        <div class="restaurant-overlay">
          <span class="badge-etg">${r.cuisine} • ${r.loc} • Open now</span>
          <h1>${r.name}</h1>
          <p>Reserve your table and pre-order food before arriving for a faster dining experience.</p>
          <div class="hero-meta">
            <span>⭐ ${r.rating}</span><span>📍 ${r.loc}</span>
            <span>💵 ${r.price}</span><span>🕒 ${r.hours}</span>
          </div>
        </div>
      </div>
    </section>
    <section class="section pt-4">
      <div class="container">
        <div class="gallery-grid mb-5">
          <div>${r.emoji}<small>Signature Dish</small></div>
          <div>🍽️<small>Chef Specials</small></div>
          <div>🪑<small>Seating Area</small></div>
          <div>⭐<small>Top Rated</small></div>
        </div>
        <div class="row g-4 align-items-start">
          <div class="col-lg-8">
            <div class="card-etg p-4 mb-4">
              <h2>Reserve Your Table</h2>
              <p class="text-muted">Choose reservation details first. You can pre-order food on the next step.</p>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="fw-bold mb-1">Date</label>
                  <input id="resDate" type="date" class="form-control" value="${today}">
                </div>
                <div class="col-md-6">
                  <label class="fw-bold mb-1">Guests</label>
                  <select id="guests" class="form-select">
                    <option>2</option><option>4</option><option>6</option><option>8</option>
                  </select>
                </div>
              </div>
              <h5 class="mt-4">Available Time Slots</h5>
              <div class="d-flex gap-2 flex-wrap" id="slotArea">
                <button onclick="selectSlot(this)" class="slot active">7:00 PM</button>
                <button onclick="selectSlot(this)" class="slot">7:30 PM</button>
                <button onclick="selectSlot(this)" class="slot">8:00 PM</button>
                <button class="slot text-muted" disabled>Full – 8:30 PM</button>
              </div>
              <div class="alert alert-warning mt-3 p-2" style="font-size:13px">
                💵 <b>Cash-only payment</b> — you will pay at the restaurant counter on your visit date. No online payment required.
              </div>
            </div>
            <div class="card-etg p-4 mb-4">
              <h2>Menu Preview</h2>
              <p class="text-muted">Items available for pre-ordering after reservation.</p>
              <div id="menuList" class="row g-3"></div>
            </div>
            <div class="card-etg p-4">
              <h2>Ratings & Reviews</h2>
              <div class="review-card"><b>⭐ 5.0 — Aisha</b><p>Fast reservation, food was ready when we arrived. The pre-order feature is brilliant!</p></div>
              <div class="review-card"><b>⭐ 4.8 — Daniel</b><p>Easy preorder, very clear pay-at-counter instructions. Great experience overall.</p></div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="card-etg p-4 summary-card">
              <h3>Booking Summary</h3>
              <p><b>Restaurant:</b> ${r.name}</p>
              <p><b>Cuisine:</b> ${r.cuisine}</p>
              <p><b>Location:</b> ${r.loc}</p>
              <p><b>Selected Time:</b> <span id="selectedTime">7:00 PM</span></p>
              <p class="text-muted small">You must be logged in as a customer to make a reservation.</p>
              <button onclick="reserve()" class="btn btn-etg w-100 mb-2">Reserve Table & Pre-Order →</button>
              <a href="restaurant-detail.html?id=${r.id}" class="btn btn-outline-etg w-100">View Full Details</a>
            </div>
          </div>
        </div>
      </div>
    </section>
    <footer class="minimal-footer">
      <div class="container d-flex justify-content-between flex-wrap gap-5">
        <div class="footer-brand"><h2>Eat<span>To</span>Go</h2><p>Final Year Project – Restaurant and Food Reservation System.</p></div>
        <div class="footer-members"><h4>Project Team</h4><ul class="list-unstyled"><li>Navin Ramu</li><li>Muhamamd Nazim</li><li>Hadif</li><li>Ng Yue Yang</li></ul></div>
      </div>
      <div class="footer-bottom">© 2026 EatToGo • Universiti Teknologi Malaysia (UTM)</div>
    </footer>`;
  renderMenu();
}

function selectSlot(el){
  $all('.slot').forEach(s=>s.classList.remove('active'));
  el.classList.add('active');
  if($('#selectedTime')) $('#selectedTime').textContent = el.textContent;
}

function reserve(){
  const u = requireCustomer('reserve a table'); if(!u) return;
  const r = getRestaurantById(get('selectedRestaurantId', 1));
  const b = {
    id: 'B' + Date.now().toString().slice(-5),
    customer: u.name, phone: u.phone||'', email: u.email,
    restaurant: r.name, restaurantId: r.id,
    date: $('#resDate')?.value || new Date().toISOString().slice(0,10),
    time: $('#selectedTime')?.textContent || '7:00 PM',
    guests: $('#guests')?.value || 2,
    status: 'pending', arrival: 'Not arrived', payment: 'Not confirmed',
    comments: '', feedbackSent: false
  };
  const arr = get('bookings',[]); arr.unshift(b);
  set('currentBooking', b); set('bookings', arr);
  location.href = 'preorder.html';
}

// ─── MENU / CART ──────────────────────────────────────────────────────────────
function renderMenu(){
  const el = $('#menuList'); if(!el) return;
  const cart = get('cart',{});
  el.innerHTML = allMenu().map(i=>{
    const q = cart[i.id]||0;
    return `<div class="col-md-6">
      <div class="menu-item-card ${q?'in-cart':''} position-relative">
        <span class="selected-badge">Selected</span>
        <div class="item-emoji-wrap">${i.emoji}</div>
        <div class="p-3">
          <div class="d-flex justify-content-between"><h5>${i.name}</h5><b style="color:var(--orange)">RM ${i.price.toFixed(2)}</b></div>
          <small class="text-muted">${i.cat}</small>
          <div class="mt-3 d-flex justify-content-between align-items-center">
            <span class="${i.available?'text-success':'text-danger'} fw-bold small">${i.available?'✓ Available':'✗ Unavailable'}</span>
            <div class="qty-controls">
              <button class="qty-btn" onclick="qty(${i.id},-1)">−</button>
              <span class="qty-num">${q}</span>
              <button class="qty-btn add-btn" onclick="addCart(${i.id})" ${!i.available?'disabled':''}>+</button>
            </div>
          </div>
        </div>
      </div>
    </div>`;
  }).join('');
  renderCart();
}

function addCart(id){
  const u = requireCustomer('add preorder items'); if(!u) return;
  const item = allMenu().find(x=>x.id==id);
  if(item && item.available===false){ alert('This item is currently unavailable.'); return; }
  const cart = get('cart',{}); cart[id]=(cart[id]||0)+1; set('cart',cart); renderMenu();
}
function removeCart(id){ const c=get('cart',{}); delete c[id]; set('cart',c); renderMenu(); }
function qty(id,n){ if(n>0){addCart(id);return;} const c=get('cart',{}); c[id]=Math.max(0,(c[id]||0)+n); if(!c[id]) delete c[id]; set('cart',c); renderMenu(); }
function qtyCard(id,delta){ if(!requireCustomer('add preorder items')) return; qty(id,delta); }

function cartItems(){
  const cart=get('cart',{}), menu=allMenu();
  return Object.entries(cart).map(([id,q])=>{
    const item = menu.find(x=>String(x.id)===String(id)) || ETG.menu.find(x=>String(x.id)===String(id));
    return item ? {id:item.id,name:item.name,qty:q,price:item.price,subtotal:item.price*q,emoji:item.emoji} : null;
  }).filter(Boolean);
}

function renderCart(){
  const el = $('#cartItems'); if(!el) return;
  const items=cartItems(), total=items.reduce((s,i)=>s+i.subtotal,0);
  el.innerHTML = items.map(i=>`
    <div class="cart-item-row">
      <span style="font-size:28px">${i.emoji||'🍽️'}</span>
      <div class="flex-grow-1"><b>${i.name}</b><br><small>RM ${i.price.toFixed(2)} × ${i.qty} = RM ${i.subtotal.toFixed(2)}</small></div>
      <button class="btn btn-sm btn-outline-secondary" onclick="qty(${i.id},-1)">−</button>
      <b class="px-1">${i.qty}</b>
      <button class="btn btn-sm btn-outline-secondary" onclick="addCart(${i.id})">+</button>
      <button class="btn btn-sm btn-danger" onclick="removeCart(${i.id})">×</button>
    </div>`).join('') || '<p class="text-muted text-center p-3">Cart is empty.<br>Tap + on any dish to add it.</p>';
  if($('#total')) $('#total').textContent = 'RM ' + total.toFixed(2);
  if($('#selectedCount')) $('#selectedCount').textContent = items.reduce((s,i)=>s+i.qty,0) + ' items selected';
}

function checkout(){
  const u = requireCustomer('checkout'); if(!u) return;
  const b = get('currentBooking',null);
  if(!b){ alert('Please select a reservation first.'); location.href='restaurant.html'; return; }
  location.href = 'checkout.html';
}

// ─── CHECKOUT → RECEIPT → PAY COUNTER ────────────────────────────────────────
function renderCheckout(){
  const u = currentUser();
  if($('#fullName') && u){ $('#fullName').value=u.name||''; $('#email').value=u.email||''; }
  renderReceiptSummary('checkoutSummary');
}

function submitCheckout(e){
  e.preventDefault();
  const u = requireCustomer('checkout'); if(!u) return;
  const b = get('currentBooking',null); if(!b){ location.href='restaurant.html'; return; }
  const items = cartItems(), total = items.reduce((s,i)=>s+i.subtotal,0);
  const customer = {
    name:$('#fullName').value.trim(),
    phone:$('#phone').value.trim(),
    email:$('#email').value.trim(),
    comments:$('#comments').value.trim()
  };
  const order = {
    id:'O'+Date.now().toString().slice(-5), bookingId:b.id,
    customer, restaurant:b.restaurant, date:b.date, time:b.time, guests:b.guests,
    items, total, status:'Pending preparation', payment:'Pay at counter'
  };
  set('currentOrder', order);
  const orders = get('orders',[]); orders.unshift(order); set('orders',orders);
  const bookings = get('bookings',[]).map(x=>x.id===b.id
    ? {...x, customer:customer.name, phone:customer.phone, email:customer.email, comments:customer.comments, payment:'Awaiting counter payment'}
    : x);
  set('bookings', bookings);
  set('currentBooking', {...b, ...customer, payment:'Awaiting counter payment'});
  localStorage.removeItem('cart');
  location.href = 'receipt.html';
}

function renderReceiptSummary(target='receiptBox'){
  const el = $('#'+target); if(!el) return;
  const o=get('currentOrder',null), b=get('currentBooking',{});
  const items=o?o.items:cartItems(), total=o?o.total:items.reduce((s,i)=>s+i.subtotal,0);
  const cust=o?.customer||{};
  el.innerHTML = `
    <div class="receipt-card">
      <h3>${o?'📋 Booking Receipt':'🛒 Checkout Summary'}</h3>
      <p><b>Restaurant:</b> ${o?.restaurant||b.restaurant||'EatToGo'}</p>
      <p><b>Date / Time:</b> ${o?.date||b.date||'-'} • ${o?.time||b.time||'-'}</p>
      <p><b>Guests:</b> ${o?.guests||b.guests||'-'}</p>
      ${o?`<hr><p><b>Customer:</b> ${cust.name}<br>
           <b>Phone:</b> ${cust.phone}<br>
           <b>Email:</b> ${cust.email}<br>
           <b>Comments:</b> ${cust.comments||'-'}</p>`:''}
      <div class="table-responsive">
        <table class="table">
          <thead><tr><th>Food Item</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th></tr></thead>
          <tbody>
            ${items.map(i=>`<tr><td>${i.name}</td><td>${i.qty}</td><td>RM ${i.price.toFixed(2)}</td><td>RM ${i.subtotal.toFixed(2)}</td></tr>`).join('')
              || '<tr><td colspan="4" class="text-muted text-center">No preorder items selected</td></tr>'}
          </tbody>
        </table>
      </div>
      <h4 class="text-end" style="color:var(--orange)">Total: RM ${total.toFixed(2)}</h4>
      <div class="alert alert-warning p-2 mt-2" style="font-size:13px">
        💵 Payment is <b>cash-only at the restaurant counter</b> on your reservation date.
      </div>
    </div>`;
}

function goPayment(){ location.href='pay-counter.html'; }

function confirmPayCounter(){
  const b=get('currentBooking',null), o=get('currentOrder',null);
  if(b){b.payment='Pay at counter – instruction sent'; set('currentBooking',b);}
  if(o){o.payment='Pay at counter – instruction sent'; set('currentOrder',o);}
  location.href='booking-success.html';
}

// ─── ARRIVAL & FEEDBACK ───────────────────────────────────────────────────────
function confirmArrival(){
  const bookings=get('bookings',[]);
  if(bookings[0]) bookings[0].arrival='Arrived';
  set('bookings',bookings);
  alert('Arrival confirmed! Restaurant staff has been notified. You will receive a feedback request after dining.');
  location.href='feedback.html';
}

function submitFeedback(e){
  e.preventDefault();
  const f=get('feedback',[]);
  f.unshift({
    name:get('session',{name:'Guest'}).name,
    rating:$('#rating').value,
    msg:$('#message').value,
    date:new Date().toISOString().slice(0,10)
  });
  set('feedback',f);
  alert('Feedback uploaded successfully. Thank you for dining with EatToGo!');
  location.href='booking-history.html';
}

function renderCustomerFeedbackNotice(){
  const el = $('#feedbackNotice'); if(!el) return;
  const u = currentUser();
  const requests = get('feedbackRequests',[]).filter(x=>!u||!x.email||x.email===u.email);
  el.innerHTML = requests.length
    ? `<div class="alert alert-success"><b>📧 Feedback request received from admin:</b><br>Please share your dining experience for <b>${requests[0].restaurant}</b> (Booking ${requests[0].bookingId}).</div>`
    : `<div class="alert alert-light border text-muted">No feedback request yet. Admin or staff will send this after your dining.</div>`;
}

// ─── BOOKINGS TABLE ───────────────────────────────────────────────────────────
function renderBookings(){
  const el = $('#bookingRows'); if(!el) return;
  const u = currentUser();
  const bookings = get('bookings',[]);
  el.innerHTML = bookings.map(x=>{
    let actions = '';
    if(u?.role==='staff'){
      actions = `
        <button class="btn btn-sm btn-success" onclick="updateBooking('${x.id}','confirmed')">✔ Confirm</button>
        <button class="btn btn-sm btn-outline-etg ms-1" onclick="markArrival('${x.id}')">📍 Mark Arrived</button>
        <br><small class="text-muted">Reject/payment: owner only</small>`;
    } else {
      actions = `
        <button class="btn btn-sm btn-success" onclick="updateBooking('${x.id}','confirmed')">✔ Confirm</button>
        <button class="btn btn-sm btn-danger ms-1" onclick="updateBooking('${x.id}','rejected')">✖ Reject</button>
        <button class="btn btn-sm btn-outline-etg ms-1" onclick="sendFeedbackRequest('${x.id}')">📧 Feedback</button>`;
    }
    return `<tr>
      <td><b>${x.id}</b></td>
      <td>${x.customer}<br><small class="text-muted">${x.phone||''}<br>${x.email||''}</small></td>
      <td>${x.restaurant}</td>
      <td>${x.date} ${x.time}</td>
      <td>${x.guests}</td>
      <td><span class="status ${x.status}">${x.status}</span></td>
      <td>${x.arrival==='Arrived'
        ? '<span class="status confirmed">Arrived</span>'
        : '<span class="text-muted small">Not arrived</span>'}
        ${x.feedbackSent ? '<br><small class="text-success">Feedback sent</small>':''}</td>
      <td>${actions}</td>
    </tr>`;
  }).join('') || '<tr><td colspan="8" class="text-muted text-center py-3">No bookings yet.</td></tr>';
}

function updateBooking(id, status){
  const u = currentUser();
  if(u?.role==='staff' && status==='rejected'){
    alert('Staff can confirm reservations but cannot reject them. This is an owner/admin action.'); return;
  }
  const b = get('bookings',[]).map(x=>x.id==id ? {...x,status} : x);
  set('bookings',b); renderBookings();
  alert('Reservation ' + id + ' status updated to: ' + status);
}

function markArrival(id){
  const b = get('bookings',[]).map(x=>x.id==id ? {...x,arrival:'Arrived'} : x);
  set('bookings',b); renderBookings();
  alert('Customer arrival confirmed by staff for booking ' + id + '.');
}

function sendFeedbackRequest(id){
  const bookings = get('bookings',[]);
  const b = bookings.find(x=>x.id===id);
  if(!b){ alert('Booking not found.'); return; }
  const arr = get('feedbackRequests',[]);
  if(arr.find(x=>x.bookingId===id)){
    alert('Feedback request already sent to this customer.'); return;
  }
  arr.unshift({id:'FR'+Date.now().toString().slice(-4), bookingId:id,
    customer:b.customer, email:b.email, restaurant:b.restaurant,
    date:new Date().toISOString().slice(0,10), status:'sent'});
  set('feedbackRequests',arr);
  // Mark the booking
  const updated = bookings.map(x=>x.id===id ? {...x,feedbackSent:true} : x);
  set('bookings',updated);
  renderBookings(); renderFeedbackRequestsTable();
  alert(`Feedback request sent to ${b.customer} (${b.email||'customer'}) for ${b.restaurant}.`);
}

// ─── ORDERS TABLE ─────────────────────────────────────────────────────────────
function renderOrders(){
  const el = $('#orderRows') || $('#staffOrders'); if(!el) return;
  const orders = get('orders',[]);
  el.innerHTML = orders.map(o=>`
    <tr>
      <td><b>${o.id}</b></td>
      <td>${o.bookingId}</td>
      <td>${o.customer?.name||'-'}<br><small class="text-muted">${o.restaurant}</small></td>
      <td>${o.items && o.items.length
        ? o.items.map(i=>`${i.emoji||'🍽️'} ${i.name} ×${i.qty}`).join('<br>')
        : '<span class="text-muted">No preorder</span>'}</td>
      <td>RM ${(o.total||0).toFixed(2)}</td>
      <td><span class="status ready">${o.status}</span></td>
      <td>
        <button class="btn btn-sm btn-success" onclick="updateOrder('${o.id}','Ready for counter payment')">✔ Mark Ready</button>
      </td>
    </tr>`).join('') || '<tr><td colspan="7" class="text-muted text-center py-3">No orders yet.</td></tr>';
}

function updateOrder(id, status){
  const orders = get('orders',[]).map(o=>o.id===id ? {...o,status} : o);
  set('orders',orders); renderOrders();
  alert('Order ' + id + ' preparation status updated to: ' + status);
}

// ─── ACCOUNTS TABLE ──────────────────────────────────────────────────────────
function renderAccounts(){
  const el = $('#accountRows'); if(!el) return;
  el.innerHTML = get('users',[]).map((u,i)=>`
    <tr>
      <td>${u.name}</td><td>${u.email}</td>
      <td><span class="status ${u.role==='admin'?'ready':u.role==='owner'?'confirmed':u.role==='staff'?'pending':''}">
        ${u.role}</span></td>
      <td><button onclick="deleteAccount(${i})" class="btn btn-sm btn-danger">Delete</button></td>
    </tr>`).join('') || '<tr><td colspan="4" class="text-muted text-center">No accounts found.</td></tr>';
}
function deleteAccount(i){
  if(!confirm('Delete this account?')) return;
  const u = get('users',[]); u.splice(i,1); set('users',u); renderAccounts();
}

// ─── REQUESTS TABLE ──────────────────────────────────────────────────────────
function renderRequests(){
  const el = $('#requestRows'); if(!el) return;
  el.innerHTML = get('requests',[]).map(r=>`
    <tr>
      <td>${r.id}</td>
      <td>${r.owner}</td>
      <td>${r.restaurant}<br><small class="text-muted">${r.cuisine||''}</small></td>
      <td><small>${r.details||'-'}</small></td>
      <td><span class="status ${r.status}">${r.status}</span><br>
          <small class="text-muted">${r.result||'Pending'}</small></td>
      <td>
        <button class="btn btn-sm btn-success" onclick="requestStatus('${r.id}','confirmed')">Approve</button>
        <button class="btn btn-sm btn-danger ms-1" onclick="requestStatus('${r.id}','rejected')">Reject</button>
      </td>
    </tr>`).join('') || '<tr><td colspan="6" class="text-muted text-center">No requests.</td></tr>';
}

function requestStatus(id, status){
  const result = status==='confirmed'
    ? '✅ Approved by admin. Owner may proceed with restaurant information update.'
    : '❌ Rejected by admin. Please revise details and resubmit.';
  const r = get('requests',[]).map(x=>x.id==id ? {...x,status,result} : x);
  set('requests',r); renderRequests(); renderOwnerRequests();
  alert(status==='confirmed' ? 'Restaurant request approved and owner notified.' : 'Request rejected and owner notified.');
}

function ownerRequest(e){
  e.preventDefault();
  const arr = get('requests',[]);
  arr.unshift({
    id:'R'+Date.now().toString().slice(-4),
    owner:$('#ownerName').value.trim(),
    restaurant:$('#restName').value.trim(),
    cuisine:$('#requestCuisine')?.value.trim()||'',
    details:$('#requestDetails')?.value.trim()||'',
    status:'pending',
    result:'Submitted to admin. Waiting for approval result.'
  });
  set('requests',arr);
  alert('Request submitted successfully. Your approval result will appear in the table below after admin reviews it.');
  e.target.reset(); renderOwnerRequests();
}

function renderOwnerRequests(){
  const el = $('#ownerRequests'); if(!el) return;
  el.innerHTML = get('requests',[]).map(r=>`
    <tr>
      <td><b>${r.restaurant}</b><br><small>${r.details||''}</small></td>
      <td><span class="status ${r.status}">${r.status}</span></td>
      <td>${r.result||'Awaiting admin review.'}</td>
    </tr>`).join('') || '<tr><td colspan="3" class="text-muted text-center">No requests submitted yet.</td></tr>';
}

// ─── ADMIN RESTAURANT CRUD ────────────────────────────────────────────────────
function saveRestaurantAdmin(e){
  e.preventDefault();
  const id = $('#adminRestaurantId')?.value;
  const data = {
    id: id ? Number(id) : Date.now(),
    name:$('#adminRestName').value.trim(),
    cuisine:$('#adminRestCuisine').value.trim(),
    loc:$('#adminRestLoc').value.trim(),
    price:$('#adminRestPrice').value.trim(),
    emoji:$('#adminRestEmoji').value.trim()||'🍽️',
    deal:$('#adminRestDeal').value.trim()||'New on EatToGo',
    hours:$('#adminRestHours').value.trim()||'10:00 AM - 10:00 PM',
    rating:parseFloat($('#adminRestRating').value)||4.5
  };
  let restaurants = allRestaurants();
  restaurants = id ? restaurants.map(r=>String(r.id)===String(id) ? data : r) : [data, ...restaurants];
  set('customRestaurants',restaurants);
  resetRestaurantForm(); renderRestaurants(); renderCuisineFilter();
  alert(id ? 'Restaurant updated successfully.' : 'Restaurant added and listed on the platform.');
}

function editRestaurantAdmin(id){
  const r = allRestaurants().find(x=>String(x.id)===String(id)); if(!r) return;
  ['adminRestaurantId','adminRestName','adminRestCuisine','adminRestLoc','adminRestPrice','adminRestEmoji','adminRestDeal','adminRestHours','adminRestRating']
    .forEach((fid,i)=>{
      const val=[r.id,r.name,r.cuisine,r.loc,r.price,r.emoji,r.deal,r.hours,r.rating][i];
      const el=$(('#'+fid)); if(el) el.value=val;
    });
  location.hash='restaurants';
  window.scrollTo({top:0,behavior:'smooth'});
}

function deleteRestaurantAdmin(id){
  if(!confirm('Delete this restaurant from the platform?')) return;
  set('customRestaurants', allRestaurants().filter(r=>String(r.id)!==String(id)));
  renderRestaurants(); renderCuisineFilter();
  alert('Restaurant deleted.');
}

function resetRestaurantForm(){
  const f=$('#adminRestaurantForm'); if(f) f.reset();
  if($('#adminRestaurantId')) $('#adminRestaurantId').value='';
  if($('#adminRestEmoji')) $('#adminRestEmoji').value='🍽️';
  if($('#adminRestRating')) $('#adminRestRating').value='4.5';
}

// ─── MENU CRUD ────────────────────────────────────────────────────────────────
function saveMenuItem(e){
  e.preventDefault();
  const id = $('#menuItemId')?.value;
  const data = {
    id: id ? Number(id) : Date.now(),
    name:$('#menuName').value.trim(),
    cat:$('#menuCategory').value.trim(),
    price:parseFloat($('#menuPrice').value)||0,
    emoji:$('#menuEmoji').value.trim()||'🍽️',
    available:$('#menuAvailable') ? $('#menuAvailable').value==='true' : true
  };
  let menu = allMenu();
  menu = id ? menu.map(i=>String(i.id)===String(id) ? data : i) : [data,...menu];
  set('customMenu',menu);
  resetMenuForm(); renderMenuCrud(); renderMenu(); renderAvailability();
  alert(id ? 'Menu item updated.' : 'Menu item added.');
}

function editMenuItem(id){
  const i = allMenu().find(x=>String(x.id)===String(id)); if(!i) return;
  if($('#menuItemId')) $('#menuItemId').value=i.id;
  if($('#menuName')) $('#menuName').value=i.name;
  if($('#menuCategory')) $('#menuCategory').value=i.cat;
  if($('#menuPrice')) $('#menuPrice').value=i.price;
  if($('#menuEmoji')) $('#menuEmoji').value=i.emoji||'🍽️';
  if($('#menuAvailable')) $('#menuAvailable').value=String(i.available!==false);
  location.hash = location.pathname.includes('admin.html') ? 'admin-menu' : 'menu';
}

function deleteMenuItem(id){
  if(!confirm('Remove this menu item?')) return;
  set('customMenu', allMenu().filter(i=>String(i.id)!==String(id)));
  renderMenuCrud(); renderMenu(); renderAvailability();
}

function resetMenuForm(){
  const ids=['menuItemId','menuName','menuCategory','menuPrice'];
  ids.forEach(id=>{const el=$('#'+id);if(el)el.value='';});
  if($('#menuEmoji')) $('#menuEmoji').value='🍽️';
  if($('#menuAvailable')) $('#menuAvailable').value='true';
}

function renderMenuCrud(){
  const el = $('#menuCrudRows'); if(!el) return;
  el.innerHTML = allMenu().map(i=>`
    <tr>
      <td>${i.emoji||'🍽️'} ${i.name}</td>
      <td>${i.cat}</td>
      <td>RM ${i.price.toFixed(2)}</td>
      <td><span class="status ${i.available?'confirmed':'rejected'}">${i.available?'Available':'Unavailable'}</span></td>
      <td>
        <button class="btn btn-sm btn-warning" onclick="editMenuItem(${i.id})">Edit</button>
        <button class="btn btn-sm btn-danger ms-1" onclick="deleteMenuItem(${i.id})">Delete</button>
      </td>
    </tr>`).join('') || '<tr><td colspan="5" class="text-muted text-center">No menu items.</td></tr>';
}

// ─── AVAILABILITY (STAFF) ────────────────────────────────────────────────────
function toggleAvailability(id){
  const u = currentUser();
  if(!u || !['staff','owner'].includes(u.role)){ alert('Only staff or owner can edit menu availability.'); return; }
  const m = allMenu().map(i=>i.id===id ? {...i,available:!i.available} : i);
  set('customMenu',m); renderAvailability();
}

function renderAvailability(){
  const el = $('#availabilityRows'); if(!el) return;
  el.innerHTML = allMenu().map(i=>`
    <tr>
      <td>${i.emoji} ${i.name}</td>
      <td>${i.cat}</td>
      <td class="text-muted">RM ${i.price.toFixed(2)}</td>
      <td><span class="status ${i.available?'confirmed':'rejected'}">${i.available?'Available':'Unavailable'}</span></td>
      <td><button class="btn btn-sm btn-outline-etg" onclick="toggleAvailability(${i.id})">Toggle</button></td>
    </tr>`).join('');
}

// ─── FEEDBACK REQUESTS TABLE (ADMIN) ─────────────────────────────────────────
function renderFeedbackRequestsTable(){
  const el = $('#feedbackRequestRows'); if(!el) return;
  const bookings = get('bookings',[]);
  const sent = get('feedbackRequests',[]);
  el.innerHTML = bookings.map(b=>{
    const already = sent.find(x=>x.bookingId===b.id);
    return `<tr>
      <td>${b.id}</td>
      <td>${b.customer}<br><small class="text-muted">${b.email||''}</small></td>
      <td>${b.restaurant}</td>
      <td>${b.date} ${b.time}</td>
      <td>${already
        ? '<span class="status confirmed">Sent</span>'
        : '<span class="text-muted">Not sent</span>'}</td>
      <td>
        ${already
          ? '<span class="text-muted small">Already sent</span>'
          : `<button class="btn btn-sm btn-etg" onclick="sendFeedbackRequest('${b.id}')">📧 Send Request</button>`}
      </td>
    </tr>`;
  }).join('') || '<tr><td colspan="6" class="text-muted text-center py-3">No bookings yet.</td></tr>';
}

// ─── RENDER ALL ───────────────────────────────────────────────────────────────
window.addEventListener('DOMContentLoaded', ()=>{
  nav();
  renderCuisineFilter();
  filterResults();
  renderRestaurantInfoPage();
  renderRestaurantDetail();
  renderRestaurants();
  renderMenu();
  renderBookings();
  renderOrders();
  renderAccounts();
  renderRequests();
  renderOwnerRequests();
  renderFeedbackRequestsTable();
  renderCustomerFeedbackNotice();
  renderCheckout();
  renderReceiptSummary();
  renderAvailability();
  renderMenuCrud();
  // Update user count for admin
  const m = document.getElementById('userMetric');
  if(m) m.textContent = get('users',[]).length;
});
