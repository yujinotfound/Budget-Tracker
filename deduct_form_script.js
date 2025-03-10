document.getElementById('deductForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('deduct_budget_process.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            const messageDiv = document.getElementById('message');
            messageDiv.className = 'message ' + (data.status === 'success' ? 'success' : 'error');
            messageDiv.textContent = data.message;

            if (data.status === 'success') {
                setTimeout(() => location.reload(), 1500);
            }
        })
        .catch(error => {
            const messageDiv = document.getElementById('message');
            messageDiv.className = 'message error';
            messageDiv.textContent = 'An unexpected error occurred. Please try again.';
        });
});
