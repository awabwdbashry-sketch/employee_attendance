/* الواجهة المشتركة: القائمة الجانبية، القوائم المنسدلة، النوافذ، التأكيد، التنبيهات، التبويبات، الجداول.
   لا يغيّر أي منطق للأعمال: كل الإرسالات تبقى نماذج HTML عادية بنفس الحقول والمسارات. */
(function () {
  'use strict';
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var A = window.APP || {};
  var icon = function (n) { return '<svg class="i" aria-hidden="true"><use href="#i-' + n + '"/></svg>'; };
  var esc = function (s) { return String(s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); };

  /* ---------- التنبيهات المنبثقة ---------- */
  function wireToast(t) {
    var close = function () { t.classList.add('out'); setTimeout(function () { t.remove(); }, 320); };
    var x = $('.t-x', t); if (x) x.onclick = close;
    setTimeout(close, t.classList.contains('error') ? 7000 : 4500);
  }
  window.toast = function (msg, type) {
    type = type || 'success';
    var box = $('#toasts');
    if (!box) { box = document.createElement('div'); box.id = 'toasts'; box.className = 'toasts'; document.body.appendChild(box); }
    var t = document.createElement('div');
    t.className = 'toast ' + type; t.setAttribute('role', type === 'error' ? 'alert' : 'status');
    t.innerHTML = '<span class="t-ic">' + icon(type === 'success' ? 'check' : 'alert') + '</span><span class="t-m"></span><button class="t-x" type="button" aria-label="إغلاق">' + icon('x') + '</button>';
    $('.t-m', t).textContent = msg; box.appendChild(t); wireToast(t);
  };
  $$('#toasts .toast').forEach(wireToast);

  /* ---------- النوافذ ---------- */
  var lockCheck = function () { if (!$$('.modal').some(function (m) { return !m.hidden; })) document.body.classList.remove('lock'); };
  window.openModal = function (id) {
    var m = document.getElementById(id); if (!m) return;
    m._op = document.activeElement; m.hidden = false; document.body.classList.add('lock');
    var f = $('[autofocus],input:not([type=hidden]):not([type=file]):not([type=checkbox]),select,textarea', m) || $('.mx,.btn', m);
    setTimeout(function () { if (f) f.focus(); }, 40);
  };
  window.closeModal = function (id) {
    var m = typeof id === 'string' ? document.getElementById(id) : id; if (!m || m.hidden) return;
    m.hidden = true; lockCheck();
    if (m.id === 'cfm' && window._cfmRes) { var r = window._cfmRes; window._cfmRes = null; r(false); }
    if (m._op && m._op.focus) try { m._op.focus(); } catch (e) {}
  };
  document.addEventListener('click', function (e) {
    var t = e.target;
    var c = t.closest ? t.closest('[data-close]') : null;
    if (c) { var m = c.closest('.modal'); if (m) { e.preventDefault(); window.closeModal(m); } return; }
    if (t.classList && t.classList.contains('mbd')) window.closeModal(t.closest('.modal'));
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      var open = $$('.modal').filter(function (m) { return !m.hidden; }).pop();
      if (open) { window.closeModal(open); return; }
      closeDDs(); closeDrawer();
    }
    if (e.key === 'Tab') {
      var m = $$('.modal').filter(function (x) { return !x.hidden; }).pop(); if (!m) return;
      var f = $$('a[href],button:not([disabled]),input:not([type=hidden]):not([disabled]),select,textarea,[tabindex]:not([tabindex="-1"])', m).filter(function (x) { return x.offsetParent !== null; });
      if (!f.length) return;
      var a = f[0], z = f[f.length - 1];
      if (e.shiftKey && document.activeElement === a) { z.focus(); e.preventDefault(); }
      else if (!e.shiftKey && document.activeElement === z) { a.focus(); e.preventDefault(); }
    }
  });

  /* ---------- نافذة التأكيد (بديل confirm الأصلي) ---------- */
  function confirmDlg(o) {
    var m = document.getElementById('cfm');
    if (!m) {
      m = document.createElement('div'); m.id = 'cfm'; m.className = 'modal'; m.hidden = true;
      m.setAttribute('role', 'alertdialog'); m.setAttribute('aria-modal', 'true'); m.setAttribute('aria-labelledby', 'cfm-t');
      m.innerHTML = '<div class="mbd"></div><div class="mbox sm"><div class="mb" style="padding-top:24px"><div class="confirm-ic" id="cfm-i"></div><h3 id="cfm-t"></h3><p class="muted" id="cfm-p" style="margin-top:4px"></p></div><div class="mf"><button type="button" class="btn" id="cfm-ok"></button><button type="button" class="btn ghost" data-close>إلغاء</button></div></div>';
      document.body.appendChild(m);
      $('#cfm-ok').addEventListener('click', function () { var r = window._cfmRes; window._cfmRes = null; m.hidden = true; lockCheck(); if (r) r(true); });
    }
    $('#cfm-t').textContent = o.title || 'تأكيد العملية';
    $('#cfm-p').textContent = o.text || '';
    var ic = $('#cfm-i'); ic.className = 'confirm-ic' + (o.danger ? ' danger' : ''); ic.innerHTML = icon('alert');
    var ok = $('#cfm-ok'); ok.textContent = o.ok || 'تأكيد'; ok.className = 'btn' + (o.danger ? ' danger solid' : '');
    return new Promise(function (res) { window._cfmRes = res; window.openModal('cfm'); setTimeout(function () { ok.focus(); }, 60); });
  }
  window.confirmDlg = confirmDlg;
  function cfgOf(el, fallback) {
    return { title: el.dataset.confirmTitle || '', text: el.dataset.confirm || fallback || '', ok: el.dataset.confirmOk || '', danger: el.dataset.confirmTone === 'danger' };
  }
  document.addEventListener('submit', function (e) {
    var f = e.target, s = e.submitter, src = (s && s.dataset && s.dataset.confirm) ? s : (f.dataset && f.dataset.confirm ? f : null);
    if (!src || f._ok) return;
    e.preventDefault();
    confirmDlg(cfgOf(src)).then(function (ok) {
      if (!ok) return;
      f._ok = true; if (f.requestSubmit) f.requestSubmit(s || undefined); else f.submit(); f._ok = false;
    });
  }, true);
  document.addEventListener('click', function (e) {
    var a = e.target.closest ? e.target.closest('a[data-confirm]') : null; if (!a) return;
    e.preventDefault();
    confirmDlg(cfgOf(a)).then(function (ok) { if (ok) location.href = a.href; });
  });


  /* ---------- نوافذ النماذج: تعبئة الحقول وفتحها دون إعادة تحميل (الرابط ?edit= يبقى احتياطًا) ---------- */
  document.addEventListener('click', function (e) {
    var b = e.target.closest ? e.target.closest('[data-fill]') : null; if (!b) return;
    var m = document.getElementById(b.dataset.modal); if (!m) return;
    e.preventDefault();
    var f = $('form', m), d = {};
    try { d = JSON.parse(b.dataset.fill); } catch (x) {}
    if (f) Object.keys(d).forEach(function (k) {
      var el = f.elements[k]; if (!el) return;
      if (el.type === 'checkbox') el.checked = !!d[k]; else el.value = d[k] === null ? '' : d[k];
      el.dispatchEvent(new Event('input', { bubbles: true })); el.dispatchEvent(new Event('change', { bubbles: true }));
    });
    var t = $('.mh h3', m); if (t && b.dataset.title) t.textContent = b.dataset.title;
    var sb = $('.mf button[type=submit],.mf button:not([type])', m); if (sb && b.dataset.submit) sb.textContent = b.dataset.submit;
    window.openModal(m.id);
  });

  /* ---------- القوائم المنسدلة ---------- */
  function closeDDs(except) {
    $$('[data-dd]').forEach(function (b) {
      var m = document.getElementById(b.dataset.dd);
      if (m && m !== except && !m.hidden) { m.hidden = true; b.setAttribute('aria-expanded', 'false'); }
    });
  }
  document.addEventListener('click', function (e) {
    var b = e.target.closest ? e.target.closest('[data-dd]') : null;
    if (b) {
      e.preventDefault();
      var m = document.getElementById(b.dataset.dd); if (!m) return;
      var willOpen = m.hidden; closeDDs(m);
      m.hidden = !willOpen; b.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
      if (willOpen && b.dataset.dd === 'bd') window.bellLoad();
      return;
    }
    if (!e.target.closest('.dd-menu')) closeDDs();
  });
  window.closeDDs = closeDDs;

  /* ---------- الإشعارات (نفس واجهة notifications/api.php) ---------- */
  window.bellLoad = function (d) {
    var o = {};
    if (d) { o.method = 'POST'; o.body = new URLSearchParams(Object.assign({ csrf: A.csrf }, d)); }
    var box = $('#bd-b'); if (!box) return Promise.resolve();
    return fetch(A.base + '/notifications/api.php', o).then(function (r) { return r.json(); }).then(function (r) {
      box.innerHTML = r.items.length ? r.items.map(function (i) {
        return '<div class="ni ' + (i.is_read ? '' : 'un') + '"><span>' + esc(i.message) + '</span><small>' + esc(i.created_at) + '</small>' +
          (i.is_read ? '' : '<a href="#" data-read="' + i.id + '">تعليم كمقروء</a>') + '</div>';
      }).join('') : '<div class="empty sm"><span class="ei">' + icon('bell') + '</span><b>لا توجد إشعارات</b></div>';
      var bl = $('.bell'), n = bl && $('i.n', bl);
      if (r.unread) { if (!n) { n = document.createElement('i'); n.className = 'n'; bl.appendChild(n); } n.textContent = r.unread; } else if (n) n.remove();
      $$('.side nav a .cnt[data-unread]').forEach(function (c) { if (r.unread) c.textContent = r.unread; else c.remove(); });
    }).catch(function () { box.innerHTML = '<div class="empty sm"><b>تعذر تحميل الإشعارات</b><p>حاول مرة أخرى بعد قليل.</p></div>'; });
  };
  document.addEventListener('click', function (e) {
    var r = e.target.closest ? e.target.closest('[data-read]') : null;
    if (r) { e.preventDefault(); window.bellLoad({ id: r.dataset.read }); return; }
    var all = e.target.closest ? e.target.closest('[data-read-all]') : null;
    if (all) { e.preventDefault(); window.bellLoad({ do: 'all' }); }
  });
  window.bellOpen = function (e) { if (e) e.preventDefault(); var b = $('.bell'); if (b) b.click(); return false; };

  /* ---------- القائمة الجانبية على الهاتف ---------- */
  var side = $('#side'), burger = $('#burger'), scrim = null;
  function openDrawer() {
    if (!side) return; side.classList.add('open'); document.body.classList.add('lock');
    if (burger) burger.setAttribute('aria-expanded', 'true');
    scrim = document.createElement('div'); scrim.className = 'scrim'; scrim.onclick = closeDrawer; document.body.appendChild(scrim);
    var a = $('nav a.on', side) || $('nav a', side); if (a) a.focus({ preventScroll: true });
  }
  function closeDrawer() {
    if (!side || !side.classList.contains('open')) return;
    side.classList.remove('open'); if (scrim) { scrim.remove(); scrim = null; }
    if (burger) { burger.setAttribute('aria-expanded', 'false'); burger.focus(); }
    lockCheck();
  }
  if (burger) burger.addEventListener('click', function () { side.classList.contains('open') ? closeDrawer() : openDrawer(); });
  if (side) side.addEventListener('click', function (e) { if (e.target.closest('nav a')) { side.classList.remove('open'); if (scrim) { scrim.remove(); scrim = null; } lockCheck(); } });
  window.addEventListener('resize', function () { if (window.innerWidth > 960) closeDrawer(); });
  if (location.hash === '#qr-card') {
    $$('.side nav a').forEach(function (a) { a.classList.toggle('on', /#qr-card$/.test(a.getAttribute('href'))); });
  }

  /* ---------- الجداول: تسميات الحقول للعرض على الهاتف ---------- */
  $$('.tbl table').forEach(function (t) {
    var rows = $$('tr', t), hr = rows.filter(function (r) { return r.querySelector('th'); })[0];
    if (!hr) return;
    hr.classList.add('hd');
    var h = $$('th', hr).map(function (x) { return x.textContent.trim(); });
    rows.forEach(function (r) {
      if (r === hr) return;
      Array.prototype.forEach.call(r.children, function (c, i) { if (c.tagName === 'TD' && !c.classList.contains('empty')) c.dataset.l = h[i] || ''; });
    });
  });

  /* ---------- التبويبات ---------- */
  var panes = $$('[data-tab]');
  if (panes.length) {
    var nm = { overview: 'نظرة عامة', attendance: 'الحضور', leaves: 'الإجازات', balance: 'الأرصدة', qr: 'رمز الحضور', activity: 'النشاط', general: 'عام', hours: 'ساعات الدوام', days: 'أيام العمل', leave: 'قواعد الإجازات', system: 'الرابط والأمان' };
    var keys = []; panes.forEach(function (p) { if (keys.indexOf(p.dataset.tab) < 0) keys.push(p.dataset.tab); });
    var bar = document.createElement('div'); bar.className = 'tabs'; bar.setAttribute('role', 'tablist');
    var show = function (k) {
      panes.forEach(function (x) { x.hidden = x.dataset.tab !== k; });
      $$('button', bar).forEach(function (b) { var on = b.dataset.k === k; b.classList.toggle('on', on); b.setAttribute('aria-selected', on ? 'true' : 'false'); b.tabIndex = on ? 0 : -1; });
    };
    keys.forEach(function (k) {
      var b = document.createElement('button'); b.type = 'button'; b.dataset.k = k; b.setAttribute('role', 'tab');
      var lab = panes.filter(function (p) { return p.dataset.tab === k && p.dataset.tabLabel; })[0];
      b.textContent = lab ? lab.dataset.tabLabel : (nm[k] || k); b.onclick = function () { show(k); }; bar.appendChild(b);
    });
    bar.addEventListener('keydown', function (e) {
      var bs = $$('button', bar), i = bs.indexOf(document.activeElement); if (i < 0) return;
      var n = e.key === 'ArrowLeft' ? i + 1 : e.key === 'ArrowRight' ? i - 1 : null; if (n === null) return;
      n = (n + bs.length) % bs.length; bs[n].focus(); bs[n].click(); e.preventDefault();
    });
    panes[0].before(bar);
    show(location.hash === '#qr-card' && keys.indexOf('qr') > -1 ? 'qr' : keys[0]);
  }

  /* ---------- حالة التحميل عند الإرسال ---------- */
  document.addEventListener('submit', function (e) {
    var f = e.target;
    if (e.defaultPrevented || (f.method || '').toLowerCase() !== 'post') return;
    var b = e.submitter || f.querySelector('button:not([type=button]),input[type=submit]');
    if (!b || b.tagName === 'INPUT' || b.hasAttribute('data-noload')) return;
    setTimeout(function () {
      b.dataset.o = b.innerHTML; b.disabled = true; b.classList.add('is-loading');
      b.textContent = b.classList.contains('danger') || /approve|reject/.test(b.value || '') ? 'جارٍ المعالجة…' : (f.enctype === 'multipart/form-data' ? 'جارٍ الإرسال…' : 'جارٍ الحفظ…');
    }, 0);
  });
  window.addEventListener('pageshow', function () {
    $$('button[data-o]').forEach(function (b) { b.disabled = false; b.classList.remove('is-loading'); b.innerHTML = b.dataset.o; });
  });

  /* ---------- إظهار/إخفاء كلمة المرور ---------- */
  document.addEventListener('click', function (e) {
    var b = e.target.closest ? e.target.closest('[data-pw]') : null; if (!b) return;
    var i = document.getElementById(b.dataset.pw) || b.parentNode.querySelector('input'); if (!i) return;
    var s = i.type === 'password'; i.type = s ? 'text' : 'password';
    b.textContent = s ? 'إخفاء' : 'إظهار'; b.setAttribute('aria-label', s ? 'إخفاء كلمة المرور' : 'إظهار كلمة المرور');
  });

  /* ---------- تلميحات المخططات ---------- */
  var tip = null;
  function showTip(el) {
    var t = el.getAttribute('data-tip'); if (!t) return;
    if (!tip) { tip = document.createElement('div'); tip.className = 'tip'; document.body.appendChild(tip); }
    var r = el.getBoundingClientRect(); tip.textContent = t; tip.hidden = false;
    tip.style.left = (r.left + r.width / 2) + 'px'; tip.style.top = r.top + 'px';
  }
  var hideTip = function () { if (tip) tip.hidden = true; };
  document.addEventListener('mouseover', function (e) { var el = e.target.closest ? e.target.closest('[data-tip]') : null; el ? showTip(el) : hideTip(); });
  document.addEventListener('focusin', function (e) { var el = e.target.closest ? e.target.closest('[data-tip]') : null; if (el) showTip(el); });
  document.addEventListener('focusout', hideTip);
  window.addEventListener('scroll', hideTip, { passive: true });
  /* صورة ناقصة → الصورة الافتراضية */
  document.addEventListener('error', function (e) {
    var im = e.target; if (!im || im.tagName !== 'IMG' || im.dataset.fb) return;
    var ic = document.querySelector('link[rel=icon]'); if (!ic || im.src.indexOf('/uploads/') < 0) return;
    im.dataset.fb = 1; im.src = ic.getAttribute('href').replace('favicon.svg', 'css/avatar.svg');
  }, true);
})();
