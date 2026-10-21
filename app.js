document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('[data-confirm]');

    buttons.forEach(function (button) {
        button.addEventListener('click', function (event) {
            const message =
                button.getAttribute('data-confirm') ||
                'Are you sure?';

            if (!confirm(message)) {
                event.preventDefault();
            }
        });
    });
});