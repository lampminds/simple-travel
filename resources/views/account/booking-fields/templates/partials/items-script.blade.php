<script>
    (function () {
        var body = document.getElementById('booking-order-body');
        var add = document.getElementById('add-template-item');
        var proto = document.getElementById('booking-template-item-prototype');
        if (!body || !add || !proto) {
            return;
        }

        var nextIndex = body.querySelectorAll('.booking-order-row').length;

        add.addEventListener('click', function () {
            var html = proto.innerHTML.split('__INDEX__').join(String(nextIndex));
            nextIndex += 1;
            body.insertAdjacentHTML('beforeend', html);
            if (window.feather && typeof window.feather.replace === 'function') {
                window.feather.replace();
            }
            body.dispatchEvent(new Event('booking-order-refresh'));
        });

        body.addEventListener('click', function (event) {
            var remove = event.target.closest('.booking-template-item-remove');
            if (!remove) {
                return;
            }
            var row = remove.closest('.booking-order-row');
            if (row) {
                row.remove();
            }
            body.dispatchEvent(new Event('booking-order-refresh'));
        });
    })();
</script>
