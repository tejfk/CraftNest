document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('product-modal');
    const closeModalBtn = document.querySelector('.close-modal');
    const productPreviews = document.querySelectorAll('.product-preview');
    let countdownInterval;

    // --- Open Modal and Populate Data ---
    productPreviews.forEach(preview => {
        preview.addEventListener('click', () => {
            // Get data from the clicked product card
            const name = preview.dataset.name;
            const image = preview.dataset.image;
            const description = preview.dataset.description;
            const startBid = parseFloat(preview.dataset.startBid);
            const buyNowPrice = parseFloat(preview.dataset.buyNow);
            const endTime = preview.dataset.endTime;

            // Populate the modal with the data
            document.getElementById('modal-title').textContent = name;
            document.getElementById('modal-image').src = image;
            document.getElementById('modal-description').textContent = description;
            document.getElementById('modal-start-bid').textContent = `₱${startBid.toLocaleString()}`;
            document.getElementById('current-bid').value = `₱${startBid.toLocaleString()}`;
            document.getElementById('modal-buy-now-price').textContent = `₱${buyNowPrice.toLocaleString()}`;

            // Handle Countdown Timer
            if (countdownInterval) clearInterval(countdownInterval);
            startCountdown(endTime);

            // Handle Bid Controls
            setupBidControls(startBid);
            
            modal.style.display = 'block';
        });
    });

    // --- Close Modal ---
    closeModalBtn.addEventListener('click', () => modal.style.display = 'none');
    window.addEventListener('click', (e) => {
        if (e.target === modal) modal.style.display = 'none';
    });

    // --- Countdown Timer Logic ---
    function startCountdown(endTimeString) {
        const countdownElement = document.getElementById('countdown-timer');
        const endTime = new Date(endTimeString).getTime();

        countdownInterval = setInterval(() => {
            const now = new Date().getTime();
            const distance = endTime - now;

            if (distance < 0) {
                clearInterval(countdownInterval);
                countdownElement.textContent = "AUCTION ENDED";
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownElement.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s`;
        }, 1000);
    }

    // --- Bid Controls Logic ---
    function setupBidControls(startBid) {
        const increaseBtn = document.getElementById('increase-bid');
        const decreaseBtn = document.getElementById('decrease-bid');
        const currentBidInput = document.getElementById('current-bid');
        let currentBid = startBid;
        const bidIncrement = 100; // You can change this value

        const updateBidDisplay = () => {
            currentBidInput.value = `₱${currentBid.toLocaleString()}`;
        };

        increaseBtn.onclick = () => {
            currentBid += bidIncrement;
            updateBidDisplay();
        };

        decreaseBtn.onclick = () => {
            if (currentBid - bidIncrement >= startBid) {
                currentBid -= bidIncrement;
                updateBidDisplay();
            }
        };
        
        updateBidDisplay();
    }
});