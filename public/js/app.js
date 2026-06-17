/* ============================================================
   Si Jaring Nusantara — app.js
   Mobile menu, cart drawer, checkout (POST to Laravel /checkout)
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {

  /* ---------- Mobile menu toggle ---------- */
  const menuToggle = document.getElementById('menuToggle');
  const mainNav    = document.getElementById('mainNav');

  if (menuToggle && mainNav) {
    menuToggle.addEventListener('click', () => {
      const open = mainNav.classList.toggle('open');
      menuToggle.classList.toggle('open', open);
      menuToggle.setAttribute('aria-expanded', String(open));
    });
    mainNav.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', () => {
        mainNav.classList.remove('open');
        menuToggle.classList.remove('open');
        menuToggle.setAttribute('aria-expanded', 'false');
      });
    });
  }

  /* ---------- Smooth scroll for anchor links ---------- */
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', (e) => {
      const href = link.getAttribute('href');
      if (href.length <= 1) return;
      const target = document.querySelector(href);
      if (!target) return;
      e.preventDefault();
      const headerH = document.querySelector('.site-header')?.offsetHeight ?? 72;
      const top = target.getBoundingClientRect().top + window.scrollY - headerH - 8;
      window.scrollTo({ top, behavior: 'smooth' });
    });
  });

  /* ---------- Cart (only on katalog page) ---------- */
  const cartDrawer = document.getElementById('cartDrawer');
  if (!cartDrawer) return; // not on katalog page

  const cart = {
    items: JSON.parse(sessionStorage.getItem('sj_cart') || '[]'),
    save() { sessionStorage.setItem('sj_cart', JSON.stringify(this.items)); },
    add(p) {
      const ex = this.items.find(i => i.id === p.id);
      if (ex) ex.qty++; else this.items.push({ ...p, qty: 1 });
      this.save(); render();
    },
    setQty(id, q) {
      const ex = this.items.find(i => i.id === id);
      if (!ex) return;
      if (q <= 0) { this.remove(id); return; }
      ex.qty = q;
      this.save(); render();
    },
    remove(id) {
      this.items = this.items.filter(i => i.id !== id);
      this.save(); render();
    },
    clear() { this.items = []; this.save(); render(); },
    total() { return this.items.reduce((s, i) => s + i.price * i.qty, 0); },
    count() { return this.items.reduce((s, i) => s + i.qty, 0); },
  };

  const fmt = n => 'Rp ' + n.toLocaleString('id-ID');

  function render() {
    const list  = document.getElementById('cartList');
    const empty = document.getElementById('cartEmpty');
    const foot  = document.getElementById('cartFoot');
    list.innerHTML = '';

    if (cart.items.length === 0) {
      empty.hidden = false; foot.hidden = true;
    } else {
      empty.hidden = true; foot.hidden = false;
      cart.items.forEach(i => {
        const li = document.createElement('li');
        li.className = 'cart-item';
        li.innerHTML = `
          <div>
            <h5>${i.name}</h5>
            <span class="ci-price">${fmt(i.price)}</span>
          </div>
          <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.4rem;">
            <div class="qty-ctl">
              <button type="button" data-qty="-" data-id="${i.id}">−</button>
              <span class="qty">${i.qty}</span>
              <button type="button" data-qty="+" data-id="${i.id}">+</button>
            </div>
            <button type="button" class="ci-remove" data-rm="${i.id}">// hapus</button>
          </div>`;
        list.appendChild(li);
      });
    }

    document.getElementById('cartSubtotal').textContent = fmt(cart.total());
    const cc = document.getElementById('cartCount');
    if (cc) {
      cc.textContent = cart.count();
      cc.dataset.count = cart.count();
    }
  }

  /* ---------- Add-to-cart (event delegation: juga jalan untuk kartu hasil AJAX) ---------- */
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-add]');
    if (!btn) return;
    cart.add({
      id:    btn.dataset.id,
      name:  btn.dataset.name,
      spec:  btn.dataset.spec || '',
      price: parseInt(btn.dataset.price, 10),
    });
    openCart();
    const orig = btn.textContent;
    btn.textContent = '✓ Ditambah';
    setTimeout(() => { btn.textContent = orig; }, 1200);
  });

  /* ---------- Cart list delegation (qty / remove) ---------- */
  document.getElementById('cartList').addEventListener('click', (e) => {
    const t = e.target;
    if (t.dataset.qty) {
      const it = cart.items.find(i => i.id === t.dataset.id);
      if (!it) return;
      cart.setQty(t.dataset.id, it.qty + (t.dataset.qty === '+' ? 1 : -1));
    } else if (t.dataset.rm) {
      cart.remove(t.dataset.rm);
    }
  });

  /* ---------- Drawer open / close ---------- */
  const drawer   = document.getElementById('cartDrawer');
  const backdrop = document.getElementById('cartBackdrop');
  function openCart() {
    drawer.classList.add('open');
    drawer.setAttribute('aria-hidden', 'false');
    backdrop.hidden = false;
    document.body.style.overflow = 'hidden';
  }
  function closeCart() {
    drawer.classList.remove('open');
    drawer.setAttribute('aria-hidden', 'true');
    backdrop.hidden = true;
    document.body.style.overflow = '';
  }
  const cartBtn = document.getElementById('cartBtn');
  if (cartBtn) cartBtn.addEventListener('click', openCart);
  document.getElementById('cartClose').addEventListener('click', closeCart);
  backdrop.addEventListener('click', closeCart);

  /* ---------- Modal helpers ---------- */
  function openModal(m)  { m.classList.add('open');    m.setAttribute('aria-hidden', 'false'); document.body.style.overflow = 'hidden'; }
  function closeModal(m) { m.classList.remove('open'); m.setAttribute('aria-hidden', 'true');  document.body.style.overflow = ''; }

  document.querySelectorAll('[data-close-modal]').forEach(b =>
    b.addEventListener('click', () => closeModal(b.closest('.modal')))
  );
  document.querySelectorAll('.modal').forEach(m =>
    m.addEventListener('click', e => { if (e.target === m) closeModal(m); })
  );

  /* ---------- Open checkout from cart ---------- */
  const checkoutModal = document.getElementById('checkoutModal');
  const confirmModal  = document.getElementById('confirmModal');

  document.getElementById('goCheckout').addEventListener('click', () => {
    if (cart.items.length === 0) return;
    closeCart();
    const ship = 25000;
    document.getElementById('sumList').innerHTML = cart.items.map(i =>
      `<li><span>${i.name} ×${i.qty}</span><span>${fmt(i.price * i.qty)}</span></li>`
    ).join('');
    document.getElementById('sumSubtotal').textContent = fmt(cart.total());
    document.getElementById('sumTotal').textContent    = fmt(cart.total() + ship);
    openModal(checkoutModal);
  });

  /* ---------- Submit checkout to Laravel ---------- */
  document.getElementById('checkoutForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = new FormData(e.target);
    const required = ['name', 'email', 'phone', 'address', 'city', 'zip'];
    for (const field of required) {
      if (!(data.get(field) || '').trim()) {
        alert('Mohon lengkapi semua field yang wajib diisi.');
        return;
      }
    }

    const payload = {
      name:    data.get('name').trim(),
      email:   data.get('email'),
      phone:   data.get('phone'),
      company: data.get('company') || '',
      address: data.get('address'),
      city:    data.get('city'),
      zip:     data.get('zip'),
      pay:     data.get('pay') || 'bca',   // metode riil dipilih di popup Midtrans
      items:   cart.items,
    };

    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Memproses...';

    try {
      const res = await fetch(window.SJ.checkoutUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': window.SJ.csrf,
          'Accept': 'application/json',
        },
        body: JSON.stringify(payload),
      });

      if (!res.ok) {
        const text = await res.text();
        throw new Error('HTTP ' + res.status + ': ' + text.slice(0, 200));
      }

      const json = await res.json();
      if (!json.success) throw new Error('Server returned success=false');

      // Tampilkan modal konfirmasi + kosongkan keranjang.
      const showConfirm = () => {
        document.getElementById('orderId').textContent    = json.order_id;
        document.getElementById('orderTotal').textContent = fmt(json.total);
        document.getElementById('waLink').href            = json.wa_link;
        openModal(confirmModal);
        cart.clear();
        e.target.reset();
      };

      closeModal(checkoutModal);

      // Setelah bayar: sinkronkan status ke Midtrans (Status API, tanpa webhook) lalu konfirmasi.
      const syncAndConfirm = () => {
        fetch('/midtrans/sync-status/' + encodeURIComponent(json.order_id), {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': window.SJ.csrf, 'Accept': 'application/json' },
        }).catch(() => {});
        showConfirm();
      };

      // Bila Midtrans aktif (ada snap_token) -> buka popup pembayaran Snap.
      if (json.snap_token && window.snap) {
        window.snap.pay(json.snap_token, {
          onSuccess: syncAndConfirm,
          onPending: syncAndConfirm,
          onError:   () => alert('Pembayaran gagal diproses. Silakan coba lagi.'),
          onClose:   () => {/* pengguna menutup popup tanpa menyelesaikan pembayaran */},
        });
      } else {
        // Fallback aman: tanpa gateway -> langsung konfirmasi (alur lama WhatsApp).
        showConfirm();
      }
    } catch (err) {
      console.error(err);
      alert('Terjadi kesalahan saat menyimpan pesanan: ' + err.message);
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Konfirmasi Pesanan →';
    }
  });

  document.getElementById('confirmDone').addEventListener('click', () => closeModal(confirmModal));

  /* ---------- Init ---------- */
  render();

});
