<script>
    (function () {
        var body = document.getElementById('booking-order-body');
        var add = document.getElementById('add-template-item');
        var proto = document.getElementById('booking-template-item-prototype');
        if (!body || !add || !proto) {
            return;
        }

        var nextIndex = body.querySelectorAll('.booking-order-row').length;

        function inheritedText(select) {
            var option = select.options[select.selectedIndex];
            if (!option || option.value === '') {
                return '';
            }

            return option.getAttribute('data-summary') || '';
        }

        function refreshRow(row) {
            var select = row.querySelector('.booking-template-field');
            var inherited = row.querySelector('.booking-template-inherited');
            if (!select || !inherited) {
                return;
            }

            inherited.textContent = inheritedText(select);
        }

        function refreshTaken() {
            var selects = body.querySelectorAll('.booking-template-field');
            var taken = {};

            selects.forEach(function (select) {
                if (select.value !== '') {
                    taken[select.value] = true;
                }
            });

            selects.forEach(function (select) {
                Array.prototype.forEach.call(select.options, function (option) {
                    if (option.value === '') {
                        option.disabled = false;
                        return;
                    }

                    option.disabled = !!taken[option.value] && option.value !== select.value;
                });
            });
        }

        function refreshAll() {
            body.querySelectorAll('.booking-order-row').forEach(refreshRow);
            refreshTaken();
        }

        add.addEventListener('click', function () {
            var html = proto.innerHTML.split('__INDEX__').join(String(nextIndex));
            nextIndex += 1;
            body.insertAdjacentHTML('beforeend', html);
            if (window.feather && typeof window.feather.replace === 'function') {
                window.feather.replace();
            }
            body.dispatchEvent(new Event('booking-order-refresh'));
            refreshAll();
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
            refreshTaken();
        });

        body.addEventListener('change', function (event) {
            var select = event.target.closest('.booking-template-field');
            if (!select) {
                return;
            }
            var row = select.closest('.booking-order-row');
            if (row) {
                refreshRow(row);
            }
            refreshTaken();
        });

        refreshAll();
    })();
</script>
