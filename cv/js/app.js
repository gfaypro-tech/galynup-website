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
