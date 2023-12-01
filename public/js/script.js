let notif = document.querySelector('.notif');
if (notif) {
    setTimeout(function () {
        notif.classList.toggle('close');
    }, 4000);
}

window.addEventListener('scroll', function () {
    const header = document.querySelector('header');
    header.classList.toggle("sticky", window.scrollY > 0);
});
