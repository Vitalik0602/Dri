// ===== DRIVE HUB MAIN JS =====

document.addEventListener('DOMContentLoaded', () => {

  // ---- Header scroll ----
  const header = document.getElementById('site-header');
  const onScroll = () => {
    header?.classList.toggle('scrolled', window.scrollY > 30);
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // ---- Burger menu ----
  const burger = document.getElementById('burger');
  const mobileMenu = document.getElementById('mobile-menu');
  const overlay = document.getElementById('overlay');

  const closeMobile = () => {
    burger?.classList.remove('open');
    burger?.setAttribute('aria-expanded', 'false');
    mobileMenu?.classList.remove('open');
    mobileMenu?.setAttribute('aria-hidden', 'true');
    overlay?.classList.remove('open');
    document.body.style.overflow = '';
  };

  burger?.addEventListener('click', () => {
    const isOpen = mobileMenu?.classList.contains('open');
    if (isOpen) { closeMobile(); }
    else {
      burger.classList.add('open');
      burger.setAttribute('aria-expanded', 'true');
      mobileMenu?.classList.add('open');
      mobileMenu?.setAttribute('aria-hidden', 'false');
      overlay?.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
  });
  overlay?.addEventListener('click', closeMobile);

  // ---- Modal ----
  const modalOverlay = document.getElementById('modal-overlay');
  const modalClose = document.getElementById('modal-close');
  const modalCarId = document.getElementById('modal-car-id');

  const openModal = (carId = '') => {
    if (modalCarId) modalCarId.value = carId;
    modalOverlay?.classList.add('open');
    modalOverlay?.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  };

  const closeModal = () => {
    modalOverlay?.classList.remove('open');
    modalOverlay?.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  };

  document.querySelectorAll('[data-modal]').forEach(btn => {
    btn.addEventListener('click', () => openModal(btn.dataset.carId || ''));
  });

  modalClose?.addEventListener('click', closeModal);
  modalOverlay?.addEventListener('click', e => {
    if (e.target === modalOverlay) closeModal();
  });

  // ESC closes modal
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeModal(); closeMobile(); }
  });

  // ---- Phone mask ----
  document.querySelectorAll('input[type="tel"]').forEach(input => {
    input.addEventListener('input', function () {
      let v = this.value.replace(/\D/g, '');
      if (v.startsWith('8')) v = '7' + v.slice(1);
      if (!v.startsWith('7') && v.length > 0) v = '7' + v;
      let out = '+7';
      if (v.length > 1) out += ' (' + v.slice(1, 4);
      if (v.length >= 4) out += ') ' + v.slice(4, 7);
      if (v.length >= 7) out += '-' + v.slice(7, 9);
      if (v.length >= 9) out += '-' + v.slice(9, 11);
      this.value = out;
    });
  });

  // ---- AJAX form submit ----
  const modalForm = document.getElementById('modal-form');
  modalForm?.addEventListener('submit', async function (e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.textContent = 'Отправляем...';

    const fd = new FormData(this);
    try {
      const res = await fetch('/request.php', { method: 'POST', body: fd });
      const json = await res.json();
      if (json.ok) {
        modalForm.innerHTML = `
          <div style="text-align:center;padding:2rem 0">
            <div style="font-size:3rem;margin-bottom:1rem">✅</div>
            <h3>Заявка отправлена!</h3>
            <p style="color:var(--text-2);margin-top:.5rem">Мы перезвоним в течение 15 минут.</p>
          </div>`;
        setTimeout(closeModal, 4000);
      } else {
        throw new Error(json.error || 'Ошибка');
      }
    } catch (err) {
      btn.disabled = false;
      btn.textContent = 'Отправить заявку';
      alert('Ошибка отправки: ' + err.message);
    }
  });

  // ---- Car gallery thumbs ----
  const mainImg = document.getElementById('car-main-img');
  document.querySelectorAll('.car-thumb').forEach(thumb => {
    thumb.addEventListener('click', function () {
      if (mainImg) mainImg.src = this.dataset.full;
      document.querySelectorAll('.car-thumb').forEach(t => t.classList.remove('active'));
      this.classList.add('active');
    });
  });

  // ---- Animated counters ----
  const counters = document.querySelectorAll('[data-count]');
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const el = entry.target;
      const target = parseInt(el.dataset.count);
      const suffix = el.dataset.suffix || '';
      let start = 0;
      const step = target / 60;
      const timer = setInterval(() => {
        start += step;
        if (start >= target) { start = target; clearInterval(timer); }
        el.textContent = Math.floor(start) + suffix;
      }, 16);
      observer.unobserve(el);
    });
  }, { threshold: 0.5 });
  counters.forEach(c => observer.observe(c));

  // ---- Scroll reveal ----
  const reveals = document.querySelectorAll('.reveal');
  const revealObs = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('revealed');
        revealObs.unobserve(e.target);
      }
    });
  }, { threshold: 0.1 });
  reveals.forEach(r => revealObs.observe(r));

  // ---- Active nav link ----
  const path = window.location.pathname;
  document.querySelectorAll('.nav-link').forEach(link => {
    const href = link.getAttribute('href');
    if (href === '/' ? path === '/' : path.startsWith(href)) {
      link.classList.add('active');
    }
  });

});

// ---- Catalog filter auto-submit on select change ----
document.querySelectorAll('.filter-bar select').forEach(sel => {
  sel.addEventListener('change', () => {
    sel.closest('form')?.submit();
  });
});
