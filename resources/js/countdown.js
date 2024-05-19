const createCountdown = (el) => {
    const timestamp = el.dataset.value;
    const countDownDate = new Date(timestamp * 1000).getTime();

    const interval = setInterval(function() {
        const now = new Date().getTime();
        const distance = countDownDate - now;

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        el.innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";

        if (distance < 0) {
            clearInterval(interval);
            el.classList.remove('status-warning');
            el.classList.add('status-danger');
            el.innerHTML = el.dataset.expiredLabel ?? 'Expired';
        }
    }, 1000);
}

[...document.querySelectorAll('.countdown')].forEach(createCountdown);




