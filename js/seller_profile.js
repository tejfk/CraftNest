document.addEventListener('DOMContentLoaded', () => {

    // --- A robust function to open a modal by its ID ---
    const openModal = (modalId) => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
        } else {
            console.error(`Modal with ID "${modalId}" not found.`);
        }
    };

    // --- A robust function to close a modal by its ID ---
    const closeModal = (modalId) => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
    };

    // --- Attach event listeners to all buttons that OPEN modals ---
    document.getElementById('edit-profile-link')?.addEventListener('click', (e) => {
        e.preventDefault();
        openModal('edit-profile-modal');
    });

    document.getElementById('switch-account-btn')?.addEventListener('click', () => {
        openModal('switch-account-modal');
    });

    document.getElementById('add-product-choice-btn')?.addEventListener('click', () => {
        openModal('add-choice-modal');
    });

    document.getElementById('open-market-modal-btn')?.addEventListener('click', () => {
        closeModal('add-choice-modal');
        openModal('add-market-modal');
    });

    document.getElementById('open-bidding-modal-btn')?.addEventListener('click', () => {
        closeModal('add-choice-modal');
        openModal('add-bidding-modal');
    });

    // --- Attach event listeners to all buttons that CLOSE modals ---
    document.querySelectorAll('.close-btn').forEach(btn => {
        btn.addEventListener('click', () => closeModal(btn.dataset.modalId));
    });

    // Close modal if user clicks on the dark background
    window.addEventListener('click', (event) => {
        if (event.target.classList.contains('modal')) {
            closeModal(event.target.id);
        }
    });

    // --- A generic, reusable function to handle form submissions via AJAX ---
    const setupFormHandler = (formId, url, messageDivId) => {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const messageDiv = document.getElementById(messageDivId);
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;
            
            submitButton.disabled = true;
            submitButton.textContent = 'Processing...';

            fetch(url, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(res => res.json())
            .then(data => {
                messageDiv.style.display = 'block';
                messageDiv.className = `form-message ${data.status}`;
                messageDiv.textContent = data.message || (data.errors ? data.errors.join(', ') : 'An unknown error occurred.');

                if (data.status === 'success') {
                    // Redirect or reload based on which form was submitted
                    const redirectUrl = formId === 'switch-account-form' ? 'home.php' : window.location.href;
                    setTimeout(() => { window.location.href = redirectUrl; }, 1500);
                } else {
                    submitButton.disabled = false;
                    submitButton.textContent = originalButtonText;
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                messageDiv.style.display = 'block';
                messageDiv.className = 'form-message error';
                messageDiv.textContent = 'A network error occurred. Please try again.';
                submitButton.disabled = false;
                submitButton.textContent = originalButtonText;
            });
        });
    };

    // --- Initialize all the form handlers on the page ---
    setupFormHandler('edit-profile-form', 'ajax_update_profile.php', 'edit-form-message');
    setupFormHandler('switch-account-form', 'ajax_switch_account.php', 'switch-form-message');
    setupFormHandler('add-market-form', 'ajax_add_product.php', 'market-form-message');
    setupFormHandler('add-bidding-form', 'add_bidding.php', 'bidding-form-message');
});