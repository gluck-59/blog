// вспомогательная фича для сидера, на бою не нужна
document.addEventListener('DOMContentLoaded', function() {
    const link = document.querySelector('#seedUrl');
    if (link !== null) link.href = link.textContent = window.location.href+'?route=seed';
});