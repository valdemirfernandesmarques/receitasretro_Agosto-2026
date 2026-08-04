setTimeout(function() {
    var notif = document.querySelector('.notificacao');
    if (notif) {
        notif.style.transition = "opacity 0.8s ease";
        notif.style.opacity = "0";
        setTimeout(() => notif.remove(), 800);
    }
}, 4000);