// incarca numar membri din API pe dashboard
document.addEventListener('DOMContentLoaded', function () {
    var box = document.getElementById('api-live-content');
    if (!box) return;

    fetch('api/microservices.php?service=members&action=list')
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var n = (data.data || []).length;
            box.textContent = 'Membri in baza de date (via API): ' + n;
        })
        .catch(function () {
            box.textContent = 'Eroare la incarcarea datelor.';
        });
});
