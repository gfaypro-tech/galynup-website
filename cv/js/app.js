// CV Builder — app.js

// Confirmer avant de quitter une page avec un formulaire modifié
(function() {
  let formDirty = false;
  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('input', () => formDirty = true);
    form.addEventListener('submit', () => formDirty = false);
  });
  window.addEventListener('beforeunload', e => {
    if (formDirty) {
      e.preventDefault();
      e.returnValue = '';
    }
  });
})();

// Auto-resize des textareas
document.querySelectorAll('textarea.form-control').forEach(ta => {
  ta.addEventListener('input', function() {
    if (this.scrollHeight > 300) return; // ne pas agrandir indéfiniment
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
  });
});

// Raccourci Ctrl+Enter pour soumettre un formulaire ou enregistrer
document.addEventListener('keydown', function(e) {
  if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
    const btn = document.querySelector('button[type=submit], .btn-primary');
    if (btn && !btn.disabled) btn.click();
  }
});

// ── Mobile sidebar toggle ──────────────────────────
(function() {
  var btn     = document.getElementById('hamburger-btn');
  var sidebar = document.getElementById('sidebar');
  var overlay = document.getElementById('sidebar-overlay');
  if (!btn || !sidebar || !overlay) return;

  function openSidebar() {
    sidebar.classList.add('sidebar--open');
    overlay.classList.add('sidebar-overlay--visible');
    btn.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  }
  function closeSidebar() {
    sidebar.classList.remove('sidebar--open');
    overlay.classList.remove('sidebar-overlay--visible');
    btn.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  }

  btn.addEventListener('click', function() {
    sidebar.classList.contains('sidebar--open') ? closeSidebar() : openSidebar();
  });
  overlay.addEventListener('click', closeSidebar);

  sidebar.querySelectorAll('.nav-item').forEach(function(link) {
    link.addEventListener('click', function() {
      if (window.innerWidth <= 768) closeSidebar();
    });
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeSidebar();
  });
})();
