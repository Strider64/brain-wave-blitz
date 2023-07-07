document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('add_to_db_table');

    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        const formData = new FormData(form);
        console.log('form data', formData);
        const response = await fetch('save_new_questions.php', {
            method: 'POST',
            body: formData
        });

        if (response.ok) {
            const result = await response.json();
            console.log(result);
            // If the response has a 'success' property and its value is true, clear the form
            if (result.success) {
                form.reset();
            }
        } else {
            console.error('Error submitting the form:', response.status, response.statusText);
            // Handle error response
        }
    });
});

