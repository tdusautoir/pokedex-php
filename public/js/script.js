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

document.getElementById('select-type-id').addEventListener('change', function () {
    const selectType = document.getElementById('select-type-id');

    const selectTypeValue = selectType.options[selectType.selectedIndex].value;

    window.location.href = selectTypeValue;
})

document.querySelector('.filterby').addEventListener('click', function () {
    document.querySelector('.filterBar').classList.toggle('open');
})
