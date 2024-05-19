
const copyToClipboardExec = (value) => {
    const textArea = document.createElement("textarea");
    textArea.value = value;

    document.body.appendChild(textArea);

    textArea.focus();
    textArea.select();

    document.execCommand('copy');
    document.body.removeChild(textArea);
};

window.copyToClipboard = async (value, message = 'Copied') => {
    try {
        if (navigator?.clipboard?.writeText) {
            await navigator.clipboard.writeText(value);
        }
    } catch (_) {
        copyToClipboardExec(value);
    } finally {
        alert(message);
    }
};
