const triggers = [...document.querySelectorAll('[data-dialog-trigger="true"]')];
const closeButtons = [...document.querySelectorAll('[data-dialog-close="true"]')];

triggers.forEach((trigger) => {
    trigger.addEventListener('click', (e) => {
        const el = (e.currentTarget ?? e.target);
        const asModal = el.dataset.asModal;
        const dialogToOpen = document.getElementById(el.dataset.dialog);
        if (!dialogToOpen) return;

        disableBodyScroll();

        dialogToOpen.addEventListener('close', () => {
            restoreBodyScroll();
        });

        if (asModal)
            dialogToOpen.showModal();
        else
            dialogToOpen.show();
    });
});

closeButtons.forEach((button) => {
    button.addEventListener('click', (e) => {
        const el = (e.currentTarget ?? e.target);
        const dialogToOpen = document.getElementById(el.dataset.dialog);
        dialogToOpen.close();
    });
});

const disableBodyScroll = () => {
    document.body.classList.add('disable-scroll');
}

const restoreBodyScroll = () => {
    document.body.classList.remove('disable-scroll');
}
