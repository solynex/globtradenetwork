document.addEventListener('DOMContentLoaded', function () {
    class PublicRequestsApp {
        constructor() {
            this.container = document.getElementById('public-requests-list');
            this.allRequests = [];
            this.init();
        }

        injectStyles() {
            const styles = `
            .btn-primary { 
    background-image: linear-gradient(135deg, #b0162f 0%, #7d1020 100%);
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 8px 20px rgba(150, 19, 39, 0.25);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
.btn-primary:hover { 
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(150, 19, 39, 0.35);
}
                :root { --primary: #961327; --primary-dark: #7d1020; }
                #globtrade-public-requests-app *, .globtrade-modal-public * { font-family: 'Cairo', sans-serif !important; box-sizing: border-box; }
                #globtrade-public-requests-app .card { background: white; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.07); transition: all 0.3s ease; border: 1px solid #e5e7eb; }
                #globtrade-public-requests-app .request-card { padding: 0; display: flex; flex-direction: column; justify-content: space-between; cursor: pointer; animation: fadeIn 0.5s ease-out forwards; opacity: 0; }
                #globtrade-public-requests-app .request-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
                #globtrade-public-requests-app .request-card-header { display: flex; justify-content: space-between; font-size: 0.75rem; color: #6b7280; margin-bottom: 0.5rem; }
                #globtrade-public-requests-app .request-card-title { font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem; }
                #globtrade-public-requests-app .request-card-meta { display: flex; align-items: center; font-size: 0.875rem; color: #4b5563; margin-bottom: 1rem; }
                #globtrade-public-requests-app .request-card-footer { margin-top: auto; padding: 1rem 1.25rem; border-top: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center; background-color: #f9fafb; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;}
                #globtrade-public-requests-app .request-card-budget { font-size: 1.25rem; font-weight: 700; color: var(--primary); }
                #globtrade-public-requests-app .btn-primary { background-image: linear-gradient(135deg, #b0162f 0%, #7d1020 100%); color: white; padding: 10px 20px; border-radius: 8px; border: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 8px 20px rgba(150, 19, 39, 0.25); text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
                #globtrade-public-requests-app .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(150, 19, 39, 0.35); }
                .globtrade-modal-public { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(29, 35, 49, 0.7); backdrop-filter: blur(5px); display: flex; align-items: center; justify-content: center; z-index: 9999; }
                .globtrade-modal-public .modal-content { background: white; border-radius: 1rem; padding: 2rem; width: 90%; max-width: 700px; box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1); }
                .blurred-container { position: relative; display: inline-block; }
                .blurred-text { filter: blur(4px); user-select: none; pointer-events: none; }
                .blurred-container:hover .show-text-overlay { opacity: 1; }
                .show-text-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.8); color: var(--primary); font-weight: bold; cursor: pointer; border-radius: 0.25rem; opacity: 0; transition: opacity 0.3s ease; font-size: 0.8rem; text-align: center; }
                @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
            `;
            const styleSheet = document.createElement("style");
            styleSheet.innerText = styles;
            document.head.appendChild(styleSheet);

            const fontLink = document.createElement('link');
            fontLink.href = 'https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap';
            fontLink.rel = 'stylesheet';
            document.head.appendChild(fontLink);
        }

        renderRequests() {
            if (!this.container) return;
            this.container.className = 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6';

            if (this.allRequests.length === 0) {
                this.container.innerHTML = `<p class="p-8 text-center text-gray-500 col-span-full">No requests found.</p>`;
                return;
            }

            this.container.innerHTML = this.allRequests.map(request => {
                const budget = request.meta['request-budget']?.[0];
                const currency = request.meta['request-currency']?.[0] || 'USD';
                const category = request.meta['request-category']?.[0] || 'Uncategorized';
                const country = request.importer?.country || 'N/A';
                const countryCode = request.importer?.country_code?.toLowerCase() || '';
                const description = request.content ? request.content.substring(0, 80) : 'No description.';
                const countryFlag = countryCode ? `<img src="https://flagcdn.com/w20/${countryCode}.png" alt="${country}" class="w-5 mr-1.5 border border-gray-200">` : '';

                return `
                <div class="card request-card" data-id="${request.id}">
                    <div class="p-5 flex-grow">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-primary bg-red-50 px-2 py-0.5 rounded-full">${category}</span>
                            <span class="text-xs text-gray-500">Posted: ${request.date}</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 hover:text-primary transition-colors cursor-pointer">${request.title}</h3>
                        <div class="flex items-center text-sm text-gray-500 mt-2">
                            ${countryFlag} <span>${country}</span>
                        </div>
                        <p class="text-gray-600 mt-3 text-sm h-12 overflow-hidden">${description}</p>
                    </div>
                    <div class="bg-gray-50 p-4 border-t flex justify-between items-center">
                        <div class="font-bold text-primary text-lg">${budget ? `${budget} ${currency}` : 'Price on offer'}</div>
                        <button class="btn-primary text-sm py-2 px-4">Submit Offer</button>
                    </div>
                </div>`;
            }).join('');
        }

        showDetailsModal(requestId) {
            const request = this.allRequests.find(r => r.id == requestId);
            if (!request) return;

            const existingModal = document.querySelector('.globtrade-modal-public');
            if (existingModal) existingModal.remove();

            const modal = document.createElement('div');
            modal.className = 'globtrade-modal-public';
            
            const userRole = globtrade_data.user_role;
            const isLoggedIn = globtrade_data.is_logged_in;
            
            let companyHtml = '';
            let actionHtml = '';

            if (isLoggedIn && (userRole === 'exporter' || userRole === 'administrator')) {
                companyHtml = `<p><strong>Company:</strong> ${request.importer.company_name}</p>`;
                actionHtml = `<a href="/dashboard" class="btn-primary"><i class="fas fa-tags mr-2"></i>Submit Offer in Dashboard</a>`;
            } else {
               actionHtml = `<a href="${globtrade_data.register_url}" class="btn-primary"></i>Register to Submit Offer</a>`;
            }
             if (isLoggedIn && userRole === 'importer') {
                companyHtml = `<p><strong>Company:</strong> ${request.importer.company_name}</p>`;
                actionHtml = `<p class="text-sm font-semibold text-red-700 p-3 bg-red-50 rounded-lg">You are logged in as an importer and cannot submit offers.</p>`;
            }

            modal.innerHTML = `
                <div class="modal-content">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-800">${request.title}</h3>
                        <button class="text-gray-400 hover:text-gray-600 text-2xl js-modal-close-public" style="background:none; border:none; cursor:pointer;">&times;</button>
                    </div>
                    <div class="space-y-2 text-gray-700">
                        <p><strong>Description:</strong> ${request.content || 'N/A'}</p> <hr class="my-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <p><strong>Category:</strong> <span class="font-medium">${request.meta['request-category']?.[0] || 'N/A'}</span></p>
                            <p><strong>Quantity Required:</strong> <span class="font-medium">${request.meta['request-quantity-value']?.[0]} ${request.meta['request-quantity-unit']?.[0]}</span></p>
                        </div>
                        <div class="pt-3"><p class="text-lg"><strong>Estimated Budget:</strong> <span class="font-bold text-green-600">${request.meta['request-budget']?.[0] ? `${request.meta['request-budget'][0]} ${request.meta['request-currency']?.[0]}` : 'N/A'}</span></p></div> <hr class="my-3">
                        ${companyHtml}
                    </div>
                    <div class="flex justify-center mt-6">${actionHtml}</div>
                </div>`;

            document.body.appendChild(modal);
            modal.querySelector('.js-modal-close-public').addEventListener('click', () => modal.remove());
            modal.addEventListener('click', (e) => { if (e.target === modal) modal.remove(); });
        }

        setupEventListeners() {
            this.container?.addEventListener('click', (e) => {
                const card = e.target.closest('.request-card');
                if (card) {
                    e.preventDefault();
                    this.showDetailsModal(card.dataset.id);
                }
            });
        }

        async init() {
            this.injectStyles();
            if (!this.container) return;
            this.container.innerHTML = '<p class="text-center text-gray-500 col-span-full">Loading latest requests...</p>';
            try {
                const response = await fetch(`${globtrade_data.api_url}public/requests`);
                if (!response.ok) throw new Error('Could not fetch requests.');
                this.allRequests = await response.json();
                this.renderRequests();
                this.setupEventListeners();
            } catch (error) {
                console.error(error);
                this.container.innerHTML = '<p class="text-center text-red-500 col-span-full">Failed to load requests.</p>';
            }
        }
    }
    new PublicRequestsApp();
});