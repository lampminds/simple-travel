<script>
    (function () {
        var tbody = document.getElementById('booking-order-body');
        if (!tbody) {
            return;
        }

        function refresh() {
            var rows = tbody.querySelectorAll('.booking-order-row');
            rows.forEach(function (row, index) {
                var input = row.querySelector('.booking-order-input');
                if (input) {
                    input.value = String((index + 1) * 10);
                }
                var up = row.querySelector('.booking-order-up');
                var down = row.querySelector('.booking-order-down');
                if (up) {
                    up.disabled = index === 0;
                }
                if (down) {
                    down.disabled = index === rows.length - 1;
                }
            });
        }

        function moveRow(row, direction) {
            var sibling = direction < 0 ? row.previousElementSibling : row.nextElementSibling;
            if (!sibling || !sibling.classList.contains('booking-order-row')) {
                return;
            }
            if (direction < 0) {
                row.parentNode.insertBefore(row, sibling);
            } else {
                row.parentNode.insertBefore(sibling, row);
            }
            refresh();
        }

        tbody.addEventListener('click', function (event) {
            var up = event.target.closest('.booking-order-up');
            if (up) {
                var upRow = up.closest('.booking-order-row');
                if (upRow) {
                    moveRow(upRow, -1);
                }
                return;
            }
            var down = event.target.closest('.booking-order-down');
            if (down) {
                var downRow = down.closest('.booking-order-row');
                if (downRow) {
                    moveRow(downRow, 1);
                }
            }
        });

        tbody.addEventListener('booking-order-refresh', refresh);
        refresh();
    })();
</script>
