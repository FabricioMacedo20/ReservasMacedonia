// resources/js/app.js

document.addEventListener('DOMContentLoaded', function () {
    // Initialize any JavaScript functionality here
    const reservationForm = document.getElementById('reservation-form');
    
    if (reservationForm) {
        reservationForm.addEventListener('submit', function (event) {
            event.preventDefault();
            // Handle form submission logic
            const formData = new FormData(reservationForm);
            fetch(reservationForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Reserva criada com sucesso!');
                    // Optionally redirect or update the UI
                } else {
                    alert('Erro ao criar reserva: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
            });
        });
    }

    // Additional JavaScript for handling table map interactions can be added here
});