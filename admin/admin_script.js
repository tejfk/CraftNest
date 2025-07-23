document.addEventListener('DOMContentLoaded', () => {
    const mainContent = document.getElementById('main-content');
    const navLinks = document.querySelectorAll('.nav-link');

    // Function to Load Page Content via AJAX
    const loadPage = (page, pushState = true) => {
        mainContent.innerHTML = '<div class="text-center p-10 font-semibold">Loading...</div>';
        if (pushState) {
            history.pushState({page: page}, '', `?page=${page.replace('_content.php', '')}`);
        }
        fetch(`pages/${page}`)
            .then(response => response.ok ? response.text() : Promise.reject('Failed to load'))
            .then(html => {
                mainContent.innerHTML = html;
                initializeContentEventListeners(); 
            })
            .catch(error => {
                mainContent.innerHTML = '<div class="text-center p-10 text-red-500">Error: Could not load content.</div>';
            });
    };

    // Sidebar Navigation Logic
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            navLinks.forEach(l => l.classList.remove('text-white', 'bg-primary'));
            link.classList.add('text-white', 'bg-primary');
            const pageToLoad = link.dataset.page;
            if (pageToLoad) {
                loadPage(pageToLoad);
            }
        });
    });
    
    // Handle browser back/forward buttons
    window.onpopstate = function(event) {
        if(event.state && event.state.page) {
            loadPage(event.state.page, false);
        } else {
            loadPage('dashboard_content.php', false); // Fallback to dashboard
        }
    };

    // Function to Initialize Event Listeners on Dynamically Loaded Content
    const initializeContentEventListeners = () => {
        const tableActionHandler = (e) => {
            if (e.target.classList.contains('action-btn')) {
                const button = e.target;
                const row = button.closest('tr');
                const action = button.dataset.action;
                let apiUrl = '', formData = new FormData();
                if (row.dataset.productId) {
                    apiUrl = 'api/update_product_status.php';
                    formData.append('product_id', row.dataset.productId);
                    formData.append('status', action === 'approve' ? 'active' : 'rejected');
                } else if (row.dataset.userId) {
                    apiUrl = 'api/update_user_status.php';
                    formData.append('user_id', row.dataset.userId);
                    formData.append('action', action);
                }
                if (!apiUrl || !confirm(`Are you sure you want to ${action} this item?`)) return;
                fetch(apiUrl, { method: 'POST', body: formData })
                    .then(res => res.json()).then(data => {
                        if (data.success) {
                            if (apiUrl.includes('product')) { row.style.opacity = '0'; setTimeout(() => row.remove(), 300); } 
                            else if (apiUrl.includes('user')) { row.querySelector('.status-cell').innerHTML = data.new_status_html; row.querySelector('.actions-cell').innerHTML = data.new_actions_html; }
                        } else { alert('Action failed.'); }
                    });
            }
        };
        document.getElementById('products-table')?.addEventListener('click', tableActionHandler);
        document.getElementById('users-table')?.addEventListener('click', tableActionHandler);
        const searchInput = document.getElementById('table-search');
        if (searchInput) {
            const table = searchInput.closest('div').querySelector('table');
            if(table) {
                searchInput.addEventListener('input', () => {
                    const query = searchInput.value.trim().toLowerCase();
                    table.querySelectorAll('tbody tr').forEach(row => {
                        row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
                    });
                });
            }
        }
    };

    // Initial Page Load
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page');
    const initialPage = page ? `${page}_content.php` : 'dashboard_content.php';
    loadPage(initialPage, false);
    document.querySelector(`.nav-link[data-page="${initialPage}"]`)?.classList.add('text-white', 'bg-primary');
    if(page) { document.querySelector('.nav-link[data-page="dashboard_content.php"]')?.classList.remove('text-white', 'bg-primary'); }
});