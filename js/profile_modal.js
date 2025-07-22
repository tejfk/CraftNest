// js/profile_modal.js

document.addEventListener('DOMContentLoaded', function () {
    // --- Get All Modal Elements ---
    const editModal = document.getElementById('edit-modal');
    const choiceModal = document.getElementById('add-choice-modal');
    const addProductModal = document.getElementById('add-product-modal');
    const addBiddingModal = document.getElementById('add-bidding-modal');
    const addStoryModal = document.getElementById('add-story-modal');

    // --- Get All Buttons That OPEN Modals ---
    const openEditBtn = document.getElementById('open-modal-btn');
    const openChoiceBtn = document.getElementById('add-product-choice-btn');
    
    // --- Get All Buttons WITHIN the Choice Modal ---
    const choiceMarketBtn = document.getElementById('choice-market-product');
    const choiceBiddingBtn = document.getElementById('choice-bidding-product');
    const choiceStoryBtn = document.getElementById('choice-story');

    // --- Get All Forms ---
    const editProfileForm = document.getElementById('edit-profile-form');
    const addProductForm = document.getElementById('add-product-form');
    const addBiddingForm = document.getElementById('add-bidding-form');
    const addStoryForm = document.getElementById('add-story-form');

    // --- Helper Functions to Open and Close Modals ---
    const openModal = (modal) => { if (modal) modal.style.display = 'block'; };
    const closeModal = (modal) => { if (modal) modal.style.display = 'none'; };

    // --- Event Listeners for Opening Initial Modals ---
    if (openEditBtn) openEditBtn.addEventListener('click', () => openModal(editModal));
    if (openChoiceBtn) openChoiceBtn.addEventListener('click', () => openModal(choiceModal));

    // --- Event Listeners for Choices WITHIN the Choice Modal ---
    if (choiceMarketBtn) choiceMarketBtn.addEventListener('click', () => {
        closeModal(choiceModal);
        openModal(addProductModal);
    });
    if (choiceBiddingBtn) choiceBiddingBtn.addEventListener('click', () => {
        closeModal(choiceModal);
        openModal(addBiddingModal);
    });
    if (choiceStoryBtn) choiceStoryBtn.addEventListener('click', () => {
        closeModal(choiceModal);
        openModal(addStoryModal);
    });

    // --- Universal Close Button and Outside-Click Logic ---
    document.querySelectorAll('.modal').forEach(modal => {
        const closeBtn = modal.querySelector('.close-btn');
        if (closeBtn) {
            closeBtn.onclick = () => closeModal(modal);
        }
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal(modal);
            }
        });
    });

    // --- AJAX FORM SUBMISSION LOGIC (Explicit for each form) ---
    // NOTE: This part handles what happens when you click "Save Changes" or "Add Product" inside the modals.
    // Make sure your ajax PHP files exist and are correct.

    // 1. Edit Profile Form
    if (editProfileForm) {
        editProfileForm.addEventListener('submit', function (e) {
            e.preventDefault();
            // Your AJAX logic for editing the profile goes here
        });
    }

    // 2. Add Market Product Form
    if (addProductForm) {
        addProductForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const messageArea = document.getElementById('add-product-message-area');
            const formData = new FormData(this);

            fetch('ajax_add_product.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    messageArea.className = 'message success';
                    messageArea.textContent = data.message;
                    this.reset();
                    setTimeout(() => closeModal(addProductModal), 2000);
                } else {
                    messageArea.className = 'message error';
                    messageArea.innerHTML = '<ul>' + data.errors.map(err => `<li>${err}</li>`).join('') + '</ul>';
                }
            }).catch(console.error);
        });
    }

    // 3. Add Bidding Product Form
    if (addBiddingForm) {
        addBiddingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const messageArea = document.getElementById('add-bidding-message-area');
            const formData = new FormData(this);

            fetch('ajax_add_bidding.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    messageArea.className = 'message success';
                    messageArea.textContent = data.message;
                    this.reset();
                    setTimeout(() => closeModal(addBiddingModal), 2000);
                } else {
                    messageArea.className = 'message error';
                    messageArea.innerHTML = '<ul>' + data.errors.map(err => `<li>${err}</li>`).join('') + '</ul>';
                }
            }).catch(console.error);
        });
    }

    // 4. Add Story Form
    if (addStoryForm) {
        addStoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const messageArea = document.getElementById('add-story-message-area');
            const formData = new FormData(this);

            fetch('ajax_add_story.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    messageArea.className = 'message success';
                    messageArea.textContent = data.message;
                    this.reset();
                    setTimeout(() => closeModal(addStoryModal), 2000);
                } else {
                    messageArea.className = 'message error';
                    messageArea.innerHTML = '<ul>' + data.errors.map(err => `<li>${err}</li>`).join('') + '</ul>';
                }
            }).catch(console.error);
        });
    }
});