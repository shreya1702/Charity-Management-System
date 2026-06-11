// main.js

// Auto-dismiss alerts after 4 seconds
document.querySelectorAll('.alert').forEach(function(alert) {
    setTimeout(function() {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(function() { alert.remove(); }, 500);
    }, 4000);
});

// Confirm before delete
document.querySelectorAll('.confirm-delete').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        if (!confirm('Are you sure you want to delete this? This action cannot be undone.')) {
            e.preventDefault();
        }
    });
});

// Highlight active nav link
(function() {
    var path = window.location.pathname;
    document.querySelectorAll('.nav-links a').forEach(function(a) {
        if (a.getAttribute('href') && path.endsWith(a.getAttribute('href').split('/').pop())) {
            a.style.background = '#2e86c1';
            a.style.color = '#fff';
        }
    });
})();
