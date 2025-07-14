document.addEventListener('DOMContentLoaded', function () {
    class GlobtradeApp {
        constructor() {
    this.currentUser = null;
    this.sections = document.querySelectorAll('.section-content'); 
    this.navItems = document.querySelectorAll('.nav-item'); 
    this.profileForm = document.getElementById('profile-form');
    this.addRequestBtn = document.getElementById('add-request-btn');
    this.addRequestModal = document.getElementById('add-request-modal');
    this.addRequestForm = document.getElementById('request-form');
    this.requestModalTitle = document.getElementById('request-modal-title');
    this.detailsModal = document.getElementById('details-modal');
    this.detailsModalTitle = document.getElementById('details-modal-title');
    this.detailsModalContent = document.getElementById('details-modal-content');
    this.detailsModalActions = document.getElementById('details-modal-actions');
    this.offerModal = document.getElementById('add-offer-modal');
    this.offerForm = document.getElementById('offer-form');
    this.offerRequestInfo = document.getElementById('offer-modal-request-info');
    this.offerModalTitle = document.getElementById('offer-modal-title');
    this.quantityUnits = ['Piece', 'Ton', 'KG', 'Box', 'Carton', 'Container', 'Set'];
    this.paymentMethods = ['Cash In Advance (CIA)', 'Letter of Credit (LC)', 'Documents Against Payment (D/P)', 'Documents Against Acceptance (D/A)'];
    this.shippingMethods = ['Ex Works (EXW)', 'Free On Board (FOB)', 'Cost and Freight (CFR)', 'Cost, Insurance and Freight (CIF)'];
    this.tradeCategories = [ 'Electronics', 'Textiles', 'Food', 'Machinery', 'Chemicals', 'Automotive', 'Construction', 'Medical', 'Agriculture', 'Energy', 'Other' ];
    this.changeLogoBtn = document.getElementById('change-logo-btn');
    this.logoUploader = document.getElementById('logo-uploader');
    this.profileLogoImg = document.getElementById('profile-logo-img');
    this.profileLogoIcon = document.getElementById('profile-logo-icon');
    this.countriesData = [];
    this.offerTypes = ['Instant Purchase', 'Sustainable Purchase', 'Custom Purchase', 'Government Tender'];
}

        async init() {
            await this.loadCountriesForSelect2();
            await this.loadUserProfile();
            this.populateProfileForm();
            this.setupEventListeners();
        }

        setupEventListeners() {
            const fabButton = document.getElementById('fabButton');
if (fabButton) {
    fabButton.addEventListener('click', () => {
        if (this.currentUser.role === 'importer') {
            this.showAddRequestModal();
        } else if (this.currentUser.role === 'exporter') {
            this.navigateToSection('importer-requests-for-exporter');
        }
    });
}
const allRequestsBtn = document.getElementById('filter-all-requests-exporter');
const myActivityBtn = document.getElementById('filter-my-activity-requests-exporter');

if (allRequestsBtn && myActivityBtn) {
    allRequestsBtn.addEventListener('click', () => {
        this.loadAllRequests();
        allRequestsBtn.classList.add('bg-primary', 'text-white');
        myActivityBtn.classList.remove('bg-primary', 'text-white');
    });

    myActivityBtn.addEventListener('click', () => {
        const myCategory = this.currentUser.business_category;
        this.loadAllRequests(myCategory);
        myActivityBtn.classList.add('bg-primary', 'text-white');
        allRequestsBtn.classList.remove('bg-primary', 'text-white');
    });
}
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const closeSidebar = () => { if (sidebar && sidebarOverlay) { sidebar.classList.remove('show'); sidebarOverlay.classList.remove('show'); } };

    if (sidebarToggle) sidebarToggle.addEventListener('click', () => { if (sidebar && sidebarOverlay) { sidebar.classList.add('show'); sidebarOverlay.classList.add('show'); } });
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);

    this.navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            this.handleNavigation(e);
            if (window.innerWidth <= 1024) closeSidebar();
        });
    });

    if (this.profileForm) this.profileForm.addEventListener('submit', (e) => this.handleProfileUpdate(e));
    if (this.addRequestBtn) this.addRequestBtn.addEventListener('click', () => this.showAddRequestModal());
    if (this.addRequestForm) this.addRequestForm.addEventListener('submit', (e) => this.handleAddOrUpdateRequest(e));
    if (this.offerForm) this.offerForm.addEventListener('submit', (e) => this.handleAddOrUpdateOffer(e));
    if (document.getElementById('marketing-offer-form')) {
        document.getElementById('marketing-offer-form').addEventListener('submit', (e) => this.handleCreateMarketingOffer(e));
    }
    
    document.getElementById('send-message-btn')?.addEventListener('click', () => this.sendMessage());
    document.querySelectorAll('.modal-close-btn').forEach(btn => btn.addEventListener('click', () => this.closeModals()));
    
    document.getElementById('my-requests-importer-list')?.addEventListener('click', (e) => this.handleListAction(e, 'importer'));
    document.getElementById('importer-requests-for-exporter-list')?.addEventListener('click', (e) => this.handleListAction(e, 'exporter'));
    document.getElementById('my-submitted-offers-exporter-list')?.addEventListener('click', (e) => this.handleListAction(e, 'exporter_offer'));
    if (this.detailsModalActions) this.detailsModalActions.addEventListener('click', (e) => this.handleModalAction(e));

    document.getElementById('offers-on-my-requests-importer-list')?.addEventListener('click', (e) => {
        const target = e.target;
        const offerId = target.closest('button')?.dataset.id;
        if (target.classList.contains('accept-offer-btn')) {
            if (confirm('Are you sure you want to accept this offer?')) {
                this.handleAcceptOffer(offerId);
            }
        } else if (target.classList.contains('reject-offer-btn')) {
            this.showRejectOfferModal(offerId);
        }
    });
    
    document.getElementById('conversations-list')?.addEventListener('click', (e) => {
        const conversationItem = e.target.closest('.conversation-item');
        if (conversationItem) {
            const { conversationId, username, usertype, avatarUrl, requestId } = conversationItem.dataset;
            this.openConversation(conversationId, username, usertype, avatarUrl, requestId);
        }
    });

    document.getElementById('agreements-list')?.addEventListener('click', (e) => {
        const downloadBtn = e.target.closest('.download-agreement-btn');
        if (downloadBtn) {
            const agreementData = JSON.parse(downloadBtn.dataset.agreement);
            this.generateAgreementPDF(agreementData);
        }
    });

    const rejectForm = document.getElementById('reject-offer-form');
    if(rejectForm) {
        rejectForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const offerId = rejectForm.dataset.offerId;
            const reason = document.getElementById('rejection-reason').value;
            this.handleRejectOffer(offerId, reason);
        });
    }

    document.body.addEventListener('click', (event) => {
    const button = event.target.closest('button[data-action]');
    if (!button) return;
    const { action, id, requestId } = button.dataset;

    if (action === 'view-request-for-offer') this.handleViewRequest(id, 'exporter');
    else if (action === 'view-offer-details') this.showOfferDetailsModal(id);
    else if (action === 'view-agreement') this.navigateToSection('agreements');
    else if (action === 'edit-offer') this.handleEditOffer(id);
    else if (action === 'delete-offer') this.handleDeleteOffer(id);
    else if (action === 'submit-another-offer') this.showOfferModal(requestId);
    else if (action === 'view-request') this.handleViewRequest(id, 'importer');
    else if (action === 'edit-request') this.handleEditRequest(id);
    else if (action === 'delete-request') this.handleDeleteRequest(id);
    else if (action === 'view-profile') this.showUserDetailsModal(id);
    else if (action === 'start-chat') this.startConversationWithUser(id);
});

    document.querySelectorAll('#community-tabs .community-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const tabName = tab.dataset.tab;
            document.querySelectorAll('#community-tabs .community-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            document.querySelectorAll('.community-tab-content').forEach(content => content.classList.add('section-hidden'));
            const activeContent = document.getElementById(`${tabName}-content`);
            if (activeContent) activeContent.classList.remove('section-hidden');
        });
    });

    jQuery('#marketing-offer-categories').on('change', () => this.handleMarketingCategoryChange());

    const managePackageBtn = document.getElementById('profile-upgrade-package-btn');
    if (managePackageBtn) {
        managePackageBtn.addEventListener('click', () => this.navigateToSection('subscription'));
    }

    const upgradeBtn = document.getElementById('community-upgrade-btn');
    if (upgradeBtn) {
        upgradeBtn.addEventListener('click', () => this.navigateToSection('subscription'));
    }

    const changeLogoBtn = document.getElementById('change-logo-btn');
    const logoUploader = document.getElementById('logo-uploader');
    if (changeLogoBtn && logoUploader) {
        changeLogoBtn.addEventListener('click', () => logoUploader.click());
        logoUploader.addEventListener('change', (event) => {
            if (event.target.files && event.target.files[0]) this.handleLogoUpload(event.target.files[0]);
        });
    }

    document.getElementById('add-social-link-btn')?.addEventListener('click', () => this.addSocialMediaInput());
    document.getElementById('complete-verification-btn')?.addEventListener('click', () => this.showVerificationModal());
    document.getElementById('save-verification-docs-btn')?.addEventListener('click', () => this.handleVerificationDocsSubmit());

    const verificationModal = document.getElementById('verification-modal');
    if (verificationModal) {
        const fileInputs = verificationModal.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', (event) => {
                const file = event.target.files[0];
                const filenameSpan = document.getElementById(`${event.target.id.replace('-input', '')}-filename`);
                if (file && filenameSpan) {
                    filenameSpan.textContent = file.name;
                } else if (filenameSpan) {
                    filenameSpan.textContent = 'No file chosen';
                }
            });
        });
    }
}

        handleListAction(e, context) {
            const targetButton = e.target.closest('button');
            if (!targetButton) return;
            const id = targetButton.dataset.id;

            if (context === 'importer' || context === 'exporter') {
                if (targetButton.classList.contains('delete-request-btn')) this.handleDeleteRequest(id);
                if (targetButton.classList.contains('edit-request-btn')) this.handleEditRequest(id);
                if (targetButton.classList.contains('view-request-btn')) this.handleViewRequest(id, 'importer');
                if (targetButton.classList.contains('view-request-for-offer-btn')) this.handleViewRequest(id, 'exporter');
            } else if (context === 'exporter_offer') {
                if (targetButton.classList.contains('edit-offer-btn')) this.handleEditOffer(id);
            }
        }
        
        showRejectOfferModal(offerId) {
    const modal = document.getElementById('reject-offer-modal');
    if (modal) {
        modal.querySelector('form').dataset.offerId = offerId;
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('show'), 10);
    }
}

        handleModalAction(e) {
    const button = e.target.closest('button[data-action]');
    if (!button) return;

    const { action, id } = button.dataset;
    this.closeModals();

    setTimeout(() => {
        if (action === 'delete-request') this.handleDeleteRequest(id);
        else if (action === 'edit-request') this.handleEditRequest(id);
        else if (action === 'submit-offer') this.showOfferModal(id);
        else if (action === 'edit-offer') this.handleEditOffer(id);
        else if (action === 'delete-offer') this.handleDeleteOffer(id);
    }, 300);
}

        async loadUserProfile() {
            try {
                const response = await fetch(`${globtrade_data.api_url}users/me`, { method: 'GET', headers: { 'X-WP-Nonce': globtrade_data.nonce } });
                if (!response.ok) throw new Error('Could not fetch user data.');
                this.currentUser = await response.json();
                this.updateUI();
                this.populateProfileForm();
                this.navigateToSection('dashboard');
            } catch (error) {
                console.error(error);
            }
        }

        updateUI() {
            if (!this.currentUser) return;
            document.getElementById('user-name').textContent = this.currentUser.company_name;
            document.getElementById('user-type-badge').textContent = this.currentUser.role;
            const avatar = document.getElementById('user-profile-avatar');
            avatar.src = this.currentUser.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(this.currentUser.company_name.charAt(0))}&background=dc2626&color=fff`;
        }

        populateProfileForm() {
            if (!this.currentUser || !this.profileForm) return;

            document.getElementById('profile-company').value = this.currentUser.company_name || '';
            document.getElementById('profile-email').value = this.currentUser.email || '';
            document.getElementById('profile-phone').value = this.currentUser.phone || '';
            document.getElementById('profile-package-credits').textContent = this.currentUser.credits;
            document.getElementById('profile-package-days-remaining').textContent = this.currentUser.days_remaining;

            const profileCountrySelect = jQuery('#profile-country');
            if (profileCountrySelect.length) {
                if (this.currentUser.country_code && this.countriesData.some(c => c.id === this.currentUser.country_code)) {
                    profileCountrySelect.val(this.currentUser.country_code).trigger('change');
                } else if (profileCountrySelect.val()) {
                    profileCountrySelect.val(null).trigger('change');
                }
            }

            const profileCategorySelect = jQuery('#profile-business-category');
            if (profileCategorySelect.length) {
                this.populateSelect('#profile-business-category', this.tradeCategories, 'Select Category');
                if (this.currentUser.business_category) {
                    profileCategorySelect.val(this.currentUser.business_category).trigger('change');
                } else if (profileCategorySelect.val()) {
                    profileCategorySelect.val(null).trigger('change');
                }
            }
            
            document.getElementById('profile-registration').value = this.currentUser.commercial_registration_no || '';
            document.getElementById('profile-website').value = this.currentUser.website || '';
            document.getElementById('profile-address').value = this.currentUser.address || '';
            document.getElementById('profile-description').value = this.currentUser.company_description || '';

            document.getElementById('profile-company-display').textContent = this.currentUser.company_name || 'Global Trade Company';
            document.getElementById('profile-user-type-display').textContent = this.currentUser.role ? this.currentUser.role.charAt(0).toUpperCase() + this.currentUser.role.slice(1) : 'User';

            const profileLogoImg = document.getElementById('profile-logo-img');
            const profileLogoIcon = document.getElementById('profile-logo-icon');
            const logoToDisplay = this.currentUser.profile_logo_url || this.currentUser.avatar_url;

            if (logoToDisplay) {
                profileLogoImg.src = logoToDisplay;
                profileLogoImg.classList.remove('hidden');
                profileLogoIcon.classList.add('hidden');
            } else {
                profileLogoImg.classList.add('hidden');
                profileLogoIcon.classList.remove('hidden');
            }

            document.getElementById('profile-package-name').textContent = this.currentUser.package || 'N/A';
            this.renderSocialMediaInputs();
            this.renderVerificationStatus();
        }

    async handleProfileUpdate(e) {
    e.preventDefault();
    const submitButton = e.target.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.innerHTML = `<span class="spinner"></span> Saving...`;

    const socialLinks = [];
    document.querySelectorAll('.social-media-item').forEach(item => {
        const type = item.querySelector('.social-type').value;
        const url = item.querySelector('.social-url').value.trim();
        if (url) {
            socialLinks.push({ type, url });
        }
    });

    const data = {
        company_name: document.getElementById('profile-company').value,
        phone: document.getElementById('profile-phone').value,
        country: jQuery('#profile-country').find('option:selected').text(),
        country_code: jQuery('#profile-country').val(),
        business_category: jQuery('#profile-business-category').val(),
        commercial_registration_no: document.getElementById('profile-registration').value,
        website: document.getElementById('profile-website').value,
        address: document.getElementById('profile-address').value,
        company_description: document.getElementById('profile-description').value,
        socials: socialLinks
    };

    try {
        const response = await fetch(`${globtrade_data.api_url}users/me`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': globtrade_data.nonce },
            body: JSON.stringify(data)
        });
        if (!response.ok) throw new Error('Failed to update profile.');
        this.showNotification('Profile updated successfully.', 'success');
        await this.loadUserProfile();
    } catch (error) {
        this.showNotification(error.message, 'error');
    } finally {
        submitButton.disabled = false;
        submitButton.innerHTML = 'Save Changes';
    }
}

       handleNavigation(e) {
    const sectionId = e.currentTarget.dataset.section;
    this.navigateToSection(sectionId);
}

        showNotification(message, type = 'error') {
            const container = document.getElementById('notificationContainer');
            if (!container) return;
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `<p>${message}</p>`;
            container.appendChild(notification);
            setTimeout(() => notification.classList.add('show'), 10);
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        populateSelect(selector, options, placeholder) {
            const selectElement = document.querySelector(selector);
            if (!selectElement) return;
            selectElement.innerHTML = `<option value="">${placeholder || 'Select an option'}</option>`;
            options.forEach(option => selectElement.add(new Option(option, option)));
            if (jQuery(selectElement).data('select2')) {
                jQuery(selectElement).trigger('change');
            } else {
                jQuery(selectElement).select2({
                    placeholder: placeholder,
                    allowClear: true,
                    tags: true
                });
            }
        }

        async loadMyRequests() {
    const container = document.getElementById('my-requests-importer-list');
    if (!container) return;
    container.innerHTML = `<div class="p-8 text-center text-gray-500">Loading your requests...</div>`;
    try {
        const response = await fetch(`${globtrade_data.api_url}requests/me`, { 
            headers: { 'X-WP-Nonce': globtrade_data.nonce } 
        });
        if (!response.ok) throw new Error('Failed to fetch your requests.');
        const requests = await response.json();
        this.renderMyRequestsList(requests);
    } catch (error) {
        console.error(error);
        container.innerHTML = `<div class="p-8 text-center text-red-500">Error loading your requests.</div>`;
    }
}

renderMyRequestsList(requests) {
    const container = document.getElementById('my-requests-importer-list');
    if (!container) return;
    if (requests.length === 0) {
        container.innerHTML = `<div class="p-8 text-center text-gray-500">You haven't added any requests yet.</div>`;
        return;
    }
    container.innerHTML = requests.map(request => {
        const budget = request.meta['request-budget']?.[0];
        const currency = request.meta['request-currency']?.[0];
        const quantity = request.meta['request-quantity-value']?.[0];
        const unit = request.meta['request-quantity-unit']?.[0];
        
        let status = request.status;
        if (status === 'publish') status = 'Active';
        status = status.charAt(0).toUpperCase() + status.slice(1);

        let statusClass = 'warning';
        if (status === 'Active' || status === 'Completed') {
            statusClass = 'success';
        }
        
        let detailsHtml = '';
        if (budget) detailsHtml += `<span>Budget: <strong class="text-gray-700">${budget} ${currency || ''}</strong></span>`;
        if (quantity) { if (detailsHtml) detailsHtml += `<span class="mx-2">|</span>`; detailsHtml += `<span>Quantity: <strong class="text-gray-700">${quantity} ${unit || ''}</strong></span>`; }

        let actionButtons = '';
        if (status !== 'Completed') {
            actionButtons = `
                <button data-action="edit-request" data-id="${request.id}" class="px-3 py-1 bg-gray-200 text-gray-700 text-xs rounded hover:bg-gray-300">Edit</button>
                <button data-action="delete-request" data-id="${request.id}" class="px-3 py-1 bg-red-100 text-red-700 text-xs rounded hover:bg-red-200">Delete</button>
            `;
        }

        return `
        <div class="p-4 border-b border-gray-200 last:border-b-0">
            <div class="flex justify-between items-start gap-4">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-primary">${request.title}</h3>
                    <div class="flex items-center text-sm text-gray-500 mt-1 flex-wrap">${detailsHtml}</div>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="badge ${statusClass}">${status}</span>
                    <div class="flex items-center gap-2 mt-2">
                        <button data-action="view-request" data-id="${request.id}" class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded hover:bg-blue-200">View</button>
                        ${actionButtons}
                    </div>
                </div>
            </div>
        </div>`;
    }).join('');
}

   async loadAllRequests(category = '') {
    const container = document.getElementById('importer-requests-for-exporter-list');
    if (!container) return;
    container.innerHTML = `<div class="p-8 text-center text-gray-500">Loading requests...</div>`;
    
    let url = `${globtrade_data.api_url}requests`;
    if (category) {
        url += `?category=${encodeURIComponent(category)}`;
    }

    try {
        const [requestsResponse, myOffersResponse] = await Promise.all([
            fetch(url, { headers: { 'X-WP-Nonce': globtrade_data.nonce } }),
            fetch(`${globtrade_data.api_url}offers/me`, { headers: { 'X-WP-Nonce': globtrade_data.nonce } })
        ]);

        if (!requestsResponse.ok) throw new Error('Failed to fetch importer requests.');
        
        const requests = await requestsResponse.json();
        const myOffers = myOffersResponse.ok ? await myOffersResponse.json() : [];
        
        this.renderAllRequestsList(requests, myOffers);
    } catch (error) {
        console.error(error);
        container.innerHTML = `<div class="p-8 text-center text-red-500">Error loading requests. Please check your subscription or try again later.</div>`;
    }
}

renderAllRequestsList(requests, myOffers) {
    const container = document.getElementById('importer-requests-for-exporter-list');
    if (!container) return;
    if (!requests || requests.length === 0) {
        container.innerHTML = `<div class="p-8 text-center text-gray-500">No importer requests available currently.</div>`;
        return;
    }
    container.innerHTML = requests.map(request => {
        const budget = request.meta['request-budget']?.[0];
        const currency = request.meta['request-currency']?.[0];
        const quantity = request.meta['request-quantity-value']?.[0];
        const unit = request.meta['request-quantity-unit']?.[0];
        
        const existingOffer = myOffers.find(o => o.meta.request_id?.[0] == request.id);
        const status = existingOffer ? (existingOffer.meta.status?.[0] || 'active').toUpperCase() : null;

        const importerName = request.importer?.company_name || 'N/A';
        const country = request.importer?.country || 'N/A';
        const countryCode = request.importer?.country_code?.toLowerCase() || '';
        const postDate = request.date;
        const countryFlag = countryCode ? `<img src="https://flagcdn.com/w20/${countryCode}.png" alt="${country}" class="w-5 mr-1.5 border border-gray-200">` : '';

        let actionHtml = '';
        let statusHtml = '';
        const buttonWrapperClass = 'w-48';

        if (existingOffer) {
            let badgeClass = '';
            if (status === 'ACTIVE' || status === 'PENDING') {
                badgeClass = 'info';
                actionHtml = `<div class="flex items-center gap-2 ${buttonWrapperClass}">
                    <button data-action="edit-offer" data-id="${existingOffer.id}" class="btn-primary-outline text-xs px-3 py-2 flex-1"><i class="fas fa-pencil-alt mr-1"></i>Edit</button>
                    <button data-action="delete-offer" data-id="${existingOffer.id}" class="btn-primary-outline text-xs px-3 py-2 flex-1"><i class="fas fa-trash-alt mr-1"></i>Delete</button>
                </div>`;
            } else if (status === 'REJECTED') {
                badgeClass = 'error';
                actionHtml = `<div class="${buttonWrapperClass}"><button data-action="submit-another-offer" data-request-id="${request.id}" class="btn-primary text-xs px-4 py-2 w-full"><i class="fas fa-paper-plane mr-1"></i>Submit Another Offer</button></div>`;
            } else if (status === 'ACCEPTED') {
                badgeClass = 'success';
            }
            statusHtml = `<span class="badge ${badgeClass} mb-2">${status}</span>`;
        } else {
            actionHtml = `<div class="${buttonWrapperClass}"><button data-action="view-request-for-offer" data-id="${request.id}" class="btn-primary text-sm px-5 py-2 w-full"><i class="fas fa-paper-plane mr-1"></i>Details & Submit</button></div>`;
        }

        return `
        <div class="p-4 border-b border-gray-200 last:border-b-0 importer-request-item">
            <div class="importer-request-container">
                <div class="request-main-details text-center">
                    <h3 class="text-lg font-semibold text-primary">${request.title}</h3>
                    <div class="request-meta-details">
                        <span class="flex items-center"><i class="fas fa-building mr-1.5 opacity-70"></i>${importerName}</span>
                        <span class="flex items-center">${countryFlag}${country}</span>
                        <span class="flex items-center"><i class="fas fa-box-open mr-1.5 opacity-70"></i>${quantity || 'N/A'} ${unit || ''}</span>
                        <span class="flex items-center"><i class="fas fa-calendar-alt mr-1.5 opacity-70"></i>${postDate}</span>
                    </div>
                </div>
                <div class="request-actions">
                    <div class="text-lg font-bold text-primary">${budget ? `${budget} ${currency}` : ''}</div>
                    <div class="action-buttons-container">
                        ${statusHtml}
                        <div class="h-9 flex items-center">${actionHtml}</div>
                    </div>
                </div>
            </div>
        </div>`;
    }).join('');
}

        showAddRequestModal() {
            this.addRequestForm.reset();
            this.addRequestForm.removeAttribute('data-editing-id');
            this.requestModalTitle.textContent = 'Add New Purchase Request';
            this.addRequestForm.querySelector('button[type="submit"]').textContent = 'Add Request';
            
            this.populateSelect('#request-category', this.tradeCategories, 'Select a Category');
            this.populateSelect('#request-quantity-unit', this.quantityUnits, 'Select a Unit');
            this.populateSelect('#request-payment', this.paymentMethods, 'Select Method');
            this.populateSelect('#request-shipping', this.shippingMethods, 'Select Incoterm');
            this.populateSelect('#request-offer-type', this.offerTypes, 'Select Offer Type');

            if (this.addRequestModal) {
                this.addRequestModal.classList.remove('hidden');
                setTimeout(() => this.addRequestModal.classList.add('show'), 10);
            }
        }

        closeModals() {
            document.querySelectorAll('.modal.show').forEach(modal => {
                modal.classList.remove('show');
                setTimeout(() => modal.classList.add('hidden'), 300);
            });
        }

        async handleAddOrUpdateRequest(e) {
            e.preventDefault();
            const submitButton = this.addRequestForm.querySelector('button[type="submit"]');
            const editingId = this.addRequestForm.dataset.editingId;
            const isEditing = !!editingId;
            const originalButtonText = isEditing ? 'Update Request' : 'Add Request';
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner"></span> Saving...`;
            const formData = new FormData(this.addRequestForm);
            const data = Object.fromEntries(formData.entries());
            const url = isEditing ? `${globtrade_data.api_url}requests/${editingId}` : `${globtrade_data.api_url}requests`;
            const method = isEditing ? 'PUT' : 'POST';
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': globtrade_data.nonce },
                    body: JSON.stringify(data)
                });
                if (!response.ok) throw new Error(`Failed to ${isEditing ? 'update' : 'create'} request.`);
                this.showNotification(`Request ${isEditing ? 'updated' : 'added'} successfully.`, 'success');
                this.closeModals();
                this.loadMyRequests();
            } catch (error) {
                this.showNotification(error.message, 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
                this.addRequestForm.removeAttribute('data-editing-id');
            }
        }

    async handleViewRequest(requestId, viewContext) {
    this.detailsModalContent.innerHTML = `<div class="text-center p-6">Loading details...</div>`;
    this.detailsModalActions.innerHTML = '';
    this.detailsModal.classList.remove('hidden');
    setTimeout(() => this.detailsModal.classList.add('show'), 10);

    try {
        const requestResponse = await fetch(`${globtrade_data.api_url}requests/${requestId}`, { headers: { 'X-WP-Nonce': globtrade_data.nonce } });
        if (!requestResponse.ok) throw new Error('Could not fetch request details.');
        const request = await requestResponse.json();

        this.detailsModalTitle.textContent = `Request Details: ${request.title}`;
        this.renderRequestDetailsContent(request);

        if (viewContext === 'importer') {
            if (request.status !== 'completed') {
                this.detailsModalActions.innerHTML = `
                    <button data-action="delete-request" data-id="${requestId}" class="px-4 py-2 bg-red-100 text-red-700 rounded-lg">Delete</button>
                    <button data-action="edit-request" data-id="${requestId}" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Edit</button>`;
            }
        } else if (viewContext === 'exporter') {
            const allOffers = await this.getMyOffers();
            const existingOffer = allOffers.find(offer => offer.meta.request_id && offer.meta.request_id[0] == requestId);
            const offerStatus = existingOffer ? (existingOffer.meta.status?.[0] || 'active').toLowerCase() : null;

            if (existingOffer && (offerStatus === 'active' || offerStatus === 'pending')) {
                this.detailsModalActions.innerHTML = `
                    <button data-action="delete-offer" data-id="${existingOffer.id}" class="px-4 py-2 bg-red-100 text-red-700 rounded-lg">Delete Offer</button>
                    <button data-action="edit-offer" data-id="${existingOffer.id}" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Edit Offer</button>`;
            } else if (existingOffer && offerStatus === 'rejected') {
                this.detailsModalActions.innerHTML = `<button data-action="submit-offer" data-id="${requestId}" class="btn-primary">Submit Another Offer</button>`;
            } else if (!existingOffer) {
                this.detailsModalActions.innerHTML = `<button data-action="submit-offer" data-id="${requestId}" class="btn-primary">Submit New Offer</button>`;
            }
        }
    } catch (error) {
        this.detailsModalContent.innerHTML = `<p class="text-red-500">${error.message}</p>`;
    }
}
async getMyOffers() {
    if (this._myOffersCache) {
        return this._myOffersCache;
    }
    try {
        const response = await fetch(`${globtrade_data.api_url}offers/me`, { headers: { 'X-WP-Nonce': globtrade_data.nonce } });
        if (!response.ok) return [];
        const offers = await response.json();
        this._myOffersCache = offers;
        return offers;
    } catch (error) {
        return [];
    }
}
async handleDeleteOffer(offerId) {
    if (!confirm('Are you sure you want to delete this offer?')) return;
    try {
        const response = await fetch(`${globtrade_data.api_url}offers/${offerId}`, {
            method: 'DELETE',
            headers: { 'X-WP-Nonce': globtrade_data.nonce }
        });
        if (!response.ok) throw new Error('Failed to delete offer.');
        this.showNotification('Offer deleted successfully.', 'success');
        this._myOffersCache = null; // Clear cache
        this.closeModals();
        this.loadMySubmittedOffers();
    } catch (error) {
        this.showNotification(error.message, 'error');
    }
}

        async handleEditRequest(requestId) {
            try {
                const response = await fetch(`${globtrade_data.api_url}requests/${requestId}`, { headers: { 'X-WP-Nonce': globtrade_data.nonce } });
                if (!response.ok) throw new Error('Could not fetch request details for editing.');
                const request = await response.json();
                this.addRequestForm.reset();
                this.addRequestForm.dataset.editingId = requestId;
                this.requestModalTitle.textContent = 'Edit Purchase Request';
                this.addRequestForm.querySelector('button[type="submit"]').textContent = 'Update Request';
                document.getElementById('request-product').value = request.title;
                document.getElementById('request-description').value = request.content;
                for (const key in request.meta) {
                    const formElement = this.addRequestForm.querySelector(`[name="${key}"]`);
                    if (formElement) formElement.value = request.meta[key][0];
                }
                this.addRequestModal.classList.remove('hidden');
                setTimeout(() => this.addRequestModal.classList.add('show'), 10);
            } catch (error) {
                this.showNotification(error.message, 'error');
            }
        }

        async handleDeleteRequest(requestId) {
            if (!confirm('Are you sure you want to delete this request permanently?')) return;
            try {
                const response = await fetch(`${globtrade_data.api_url}requests/${requestId}`, {
                    method: 'DELETE', headers: { 'X-WP-Nonce': globtrade_data.nonce }
                });
                if (!response.ok) throw new Error('Failed to delete request.');
                this.showNotification('Request deleted successfully.', 'success');
                this.loadMyRequests();
            } catch (error) {
                this.showNotification(error.message, 'error');
            }
        }

        async showOfferModal(requestId) {
    if (!this.offerModal || !this.offerForm) return;

    this.offerForm.reset();
    this.offerForm.dataset.requestId = requestId;
    this.offerForm.removeAttribute('data-editing-id');
    this.offerModalTitle.textContent = 'Submit New Offer';
    this.offerForm.querySelector('button[type="submit"]').innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Submit Offer';

    try {
        const response = await fetch(`${globtrade_data.api_url}requests/${requestId}`, { 
            headers: { 'X-WP-Nonce': globtrade_data.nonce } 
        });
        if (!response.ok) throw new Error('Could not fetch request details.');
        
        const request = await response.json();
        const requestInfoDiv = document.getElementById('offer-modal-request-info');
        if (requestInfoDiv) {
            requestInfoDiv.innerHTML = `<p class="font-semibold">Submitting offer on request: <strong class="text-gray-800">${request.title}</strong></p>`;
        }

        const shippingPref = request.meta['request-shipping']?.[0];
        const paymentPref = request.meta['request-payment']?.[0];
        const preferencesP = document.getElementById('importer-preferences');

        if (shippingPref || paymentPref) {
            preferencesP.parentElement.classList.remove('hidden');
            let prefText = "<strong>Importer's Preference:</strong>";
            if (shippingPref) prefText += ` Shipping: ${shippingPref}`;
            if (paymentPref) prefText += `${shippingPref ? ',' : ''} Payment: ${paymentPref}`;
            preferencesP.innerHTML = prefText;
        } else {
            preferencesP.parentElement.classList.add('hidden');
        }

    } catch (error) {
        console.error("Error fetching request preferences:", error);
        document.getElementById('importer-preferences').parentElement.classList.add('hidden');
    }

    this.populateSelect('#offer-quantity-unit', this.quantityUnits, 'Select a Unit');
    this.populateSelect('#offer-shipping', this.shippingMethods, 'Select Proposed Shipping');
    this.populateSelect('#offer-payment', this.paymentMethods, 'Select Proposed Payment');
    
    this.offerModal.classList.remove('hidden');
    setTimeout(() => this.offerModal.classList.add('show'), 10);
}

        async handleAddOrUpdateOffer(e) {
            e.preventDefault();
            const submitButton = this.offerForm.querySelector('button[type="submit"]');
            const editingId = this.offerForm.dataset.editingId;
            const isEditing = !!editingId;
            const originalButtonText = isEditing ? 'Update Offer' : 'Submit Offer';
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner"></span> Saving...`;
            const offerData = Object.fromEntries(new FormData(this.offerForm).entries());
            const requestId = this.offerForm.dataset.requestId;
            const url = isEditing ? `${globtrade_data.api_url}offers/${editingId}` : `${globtrade_data.api_url}offers`;
            const method = isEditing ? 'PUT' : 'POST';
            const body = isEditing ? JSON.stringify({ offerData: offerData }) : JSON.stringify({ requestId: requestId, offerData: offerData });
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': globtrade_data.nonce },
                    body: body
                });
                if (!response.ok) throw new Error(`Failed to ${isEditing ? 'update' : 'submit'} offer.`);
                this.showNotification(`Offer ${isEditing ? 'updated' : 'submitted'} successfully.`, 'success');
                this.closeModals();
                this.handleNavigation({ currentTarget: document.querySelector('.nav-item[data-section="my-submitted-offers-exporter"]') });
            } catch (error) {
                this.showNotification(error.message, 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
                this.offerForm.removeAttribute('data-editing-id');
            }
        }

        async handleEditOffer(offerId) {
            try {
                const response = await fetch(`${globtrade_data.api_url}offers/${offerId}`, { headers: { 'X-WP-Nonce': globtrade_data.nonce } });
                if (!response.ok) throw new Error('Could not fetch offer details.');
                const offer = await response.json();
                this.showOfferModal(offer.meta.request_id[0], offer.id);
                setTimeout(() => {
                    for (const key in offer.meta) {
                        const formElement = this.offerForm.querySelector(`[name="${key}"]`);
                        if (formElement) formElement.value = offer.meta[key][0];
                    }
                }, 100);
            } catch (error) {
                this.showNotification(error.message, 'error');
            }
        }

        async loadMySubmittedOffers() {
            const container = document.getElementById('my-submitted-offers-exporter-list');
            if (!container) return;
            container.innerHTML = `<div class="p-8 text-center text-gray-500">Loading your offers...</div>`;
            try {
                const response = await fetch(`${globtrade_data.api_url}offers/me`, { headers: { 'X-WP-Nonce': globtrade_data.nonce } });
                if (!response.ok) throw new Error('Failed to fetch submitted offers.');
                const offers = await response.json();
                this.renderMySubmittedOffers(offers);
            } catch (error) {
                console.error(error);
                container.innerHTML = `<div class="p-8 text-center text-red-500">Error loading offers.</div>`;
            }
        }

renderMySubmittedOffers(offers) {
    const container = document.getElementById('my-submitted-offers-exporter-list');
    if (!container) return;
    if (offers.length === 0) {
        container.innerHTML = `<div class="p-8 text-center text-gray-500">You haven't submitted any offers yet.</div>`;
        return;
    }
    container.innerHTML = offers.map(offer => {
        const price = offer.meta['offer-price']?.[0] || 'N/A';
        const currency = offer.meta['offer-currency']?.[0] || 'USD';
        const quantity = offer.meta['offer-quantity-value']?.[0] || 'N/A';
        const unit = offer.meta['offer-quantity-unit']?.[0] || '';
        const status = (offer.meta['status']?.[0] || 'Active').toUpperCase();
        
        let badgeClass = '';
        let actionButtons = '';
        const buttonWrapperClass = 'w-44'; 

        if (status === 'ACTIVE' || status === 'PENDING') {
            badgeClass = 'info';
            actionButtons = `<div class="flex items-center gap-2 ${buttonWrapperClass}"><button data-action="edit-offer" data-id="${offer.id}" class="btn-primary-outline text-xs px-3 py-2 flex-1"><i class="fas fa-pencil-alt mr-1"></i>Edit</button><button data-action="delete-offer" data-id="${offer.id}" class="btn-primary-outline text-xs px-3 py-2 flex-1"><i class="fas fa-trash-alt mr-1"></i>Delete</button></div>`;
        } else if (status === 'REJECTED') {
            badgeClass = 'error';
            const requestId = offer.meta.request_id?.[0];
            if (requestId) {
                actionButtons = `<div class="${buttonWrapperClass}"><button data-action="submit-another-offer" data-request-id="${requestId}" class="btn-primary text-xs px-4 py-2 w-full"><i class="fas fa-paper-plane mr-1"></i>Submit Another</button></div>`;
            }
        } else if (status === 'ACCEPTED') {
    badgeClass = 'success';
    actionButtons = `<div class="${buttonWrapperClass}">
        <button data-action="view-offer-details" data-id="${offer.id}" class="btn-primary-outline text-xs px-4 py-2 w-full"><i class="fas fa-eye mr-1"></i>View Offer</button>
    </div>`;
}

        return `<div class="p-4 border-b border-gray-200 last:border-b-0 offer-list-item"><div class="offer-item-container flex justify-between items-center gap-4"><div class="offer-details-column flex-1"><h3 class="text-lg font-semibold text-primary">Offer on Request: ${offer.request.title || 'N/A'}</h3><p class="text-sm text-gray-600 mt-1">My Price: <strong class="text-gray-800">${price} ${currency}</strong> | Quantity: <strong class="text-gray-800">${quantity} ${unit}</strong></p><div class="flex items-center text-xs text-gray-500 mt-2"><span>To: ${offer.importer.company_name || 'N/A'}</span><span class="mx-2">|</span><span>Submission Date: ${offer.date}</span></div></div><div class="offer-actions-column flex flex-col items-center justify-center" style="min-width: 180px;"><span class="badge ${badgeClass} mb-2">${status}</span><div class="h-9 flex items-center">${actionButtons}</div></div></div></div>`;
    }).join('');
}
    async handleLogoUpload(file) {
    if (!file) return;
    this.showNotification('Uploading logo...', 'info');

    const formData = new FormData();
    formData.append('profile_logo', file);

    try {
        const response = await fetch(`${globtrade_data.api_url}users/me/logo-upload`, {
            method: 'POST',
            headers: { 'X-WP-Nonce': globtrade_data.nonce },
            body: formData
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Failed to upload logo.');
        }

        const result = await response.json();
        this.showNotification('Logo uploaded successfully!', 'success');
        
        if (result.logo_url) {
            document.getElementById('profile-logo-img').src = result.logo_url;
            document.getElementById('user-profile-avatar').src = result.logo_url;
        }
    } catch (error) {
        this.showNotification(error.message, 'error');
    }
}

async loadCountriesForSelect2() {
    try {
        const response = await fetch('https://flagcdn.com/en/codes.json');
        if (!response.ok) throw new Error('Network response was not ok for countries.');
        const countries = await response.json();
        this.countriesData = Object.entries(countries).map(([code, name]) => ({
            id: code.toUpperCase(),
            text: name
        }));

        jQuery('.country-select').select2({
            placeholder: 'Select a Country',
            data: this.countriesData,
            templateResult: this.formatCountryOption,
            templateSelection: this.formatCountryOption,
            allowClear: true,
            tags: true
        });
    } catch (error) {
        console.error('Failed to load countries for select2:', error);
    }
}

formatCountryOption(country) {
    if (!country.id) { return country.text; }
    const flagUrl = `https://flagcdn.com/w20/${country.id.toLowerCase()}.png`;
    return jQuery(`<span><img src="${flagUrl}" class="inline-block mr-2" /> ${country.text}</span>`);
}


async loadOffersOnMyRequests() {
        const container = document.getElementById('offers-on-my-requests-importer-list');
        if (!container) return;
        container.innerHTML = `<div class="p-8 text-center text-gray-500">Loading offers...</div>`;

        try {
            const myRequestsResponse = await fetch(`${globtrade_data.api_url}requests/me`, { headers: { 'X-WP-Nonce': globtrade_data.nonce } });
            if (!myRequestsResponse.ok) throw new Error('Failed to fetch your requests.');
            const myRequests = await myRequestsResponse.json();

            let allOffers = [];
            for (const request of myRequests) {
                const offersResponse = await fetch(`${globtrade_data.api_url}requests/${request.id}/offers`, { headers: { 'X-WP-Nonce': globtrade_data.nonce } });
                if (offersResponse.ok) {
                    const offers = await offersResponse.json();
                    allOffers = allOffers.concat(offers);
                }
            }
            this.renderOffersOnMyRequests(allOffers);

        } catch (error) {
            console.error(error);
            container.innerHTML = `<div class="p-8 text-center text-red-500">Error loading offers.</div>`;
        }
    }

    renderOffersOnMyRequests(offers) {
        const container = document.getElementById('offers-on-my-requests-importer-list');
        if (!container) return;

        if (!offers || offers.length === 0) {
            container.innerHTML = `<div class="p-8 text-center text-gray-500">You haven't received any offers yet.</div>`;
            return;
        }

        container.innerHTML = offers.map(offer => {
            const status = offer.meta.status ? offer.meta.status[0] : 'active';
            let actionButtons = '';

            if (status === 'active') {
                actionButtons = `
                    <button data-id="${offer.id}" class="accept-offer-btn px-3 py-1 bg-green-500 text-white text-xs rounded-lg hover:bg-green-600 transition-colors">Accept</button>
                    <button data-id="${offer.id}" class="reject-offer-btn px-3 py-1 bg-red-500 text-white text-xs rounded-lg hover:bg-red-600 transition-colors">Reject</button>
                `;
            } else {
                actionButtons = `<span class="badge ${status === 'accepted' ? 'success' : 'error'}">${status}</span>`;
            }

            return `
            <div class="p-4 border-b border-gray-200 last:border-b-0 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-primary">Offer on: ${offer.request_title}</h3>
                    <p class="text-sm text-gray-600 mt-1">From: <strong class="text-gray-800">${offer.exporter.company_name}</strong></p>
                    <p class="text-sm text-gray-600">Price: <strong class="text-gray-800">${offer.meta['offer-price'][0]} ${offer.meta['offer-currency'][0]}</strong></p>
                </div>
                <div class="flex flex-col items-end gap-2 text-right">
                    ${actionButtons}
                </div>
            </div>`;
        }).join('');
    }

   async handleAcceptOffer(offerId) {
    try {
        const response = await fetch(`${globtrade_data.api_url}offers/${offerId}/accept`, {
            method: 'POST',
            headers: { 'X-WP-Nonce': globtrade_data.nonce },
        });
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'Failed to accept offer.');

        this.showNotification('Offer accepted successfully. Opening chat...', 'success');
        this.navigateToSection('messages');
        setTimeout(() => this.openConversation(result.conversation_id, 'Loading...'), 300);

    } catch (error) {
        this.showNotification(error.message, 'error');
    }
}

async handleRejectOffer(offerId, reason) {
    this.closeModals();
    try {
        const response = await fetch(`${globtrade_data.api_url}offers/${offerId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': globtrade_data.nonce
            },
            body: JSON.stringify({ reason: reason })
        });
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'Failed to reject offer.');
        
        this.showNotification('Offer rejected. Opening chat to inform exporter...', 'info');
        this.navigateToSection('messages');
        setTimeout(() => {
            this.openConversation(result.conversation_id, 'Loading...');
        }, 300);

    } catch (error) {
        this.showNotification(error.message, 'error');
    }
}

navigateToSection(sectionId) {
    this.sections.forEach(sec => sec.classList.add('section-hidden'));
    const activeSection = document.getElementById(`${sectionId}-section`);
    if (activeSection) {
        activeSection.classList.remove('section-hidden');
    }

    this.navItems.forEach(item => {
        item.classList.toggle('active', item.dataset.section === sectionId);
    });
    
    if (sectionId === 'dashboard') this.loadDashboard();
    if (sectionId === 'agreements') this.loadAgreements();
    if (sectionId === 'subscription') this.loadSubscriptionPage();
    if (sectionId === 'my-requests-importer') this.loadMyRequests();
    if (sectionId === 'importer-requests-for-exporter') this.loadAllRequests();
    if (sectionId === 'offers-on-my-requests-importer') this.loadOffersOnMyRequests();
    if (sectionId === 'my-submitted-offers-exporter') this.loadMySubmittedOffers();
    if (sectionId === 'messages') this.loadConversations();
    if (sectionId === 'users') this.loadCommunitySection();
}


       async openConversation(conversationId, otherUserName, otherUserType, avatarUrl, requestId) {
    this.currentConversationId = conversationId;
    const chatHeader = document.getElementById('chat-header');
    const chatMessages = document.getElementById('chat-messages');
    const chatInputArea = document.getElementById('chat-input-area');

    const avatarInitial = otherUserName.charAt(0).toUpperCase();
    const avatar = avatarUrl 
        ? `<img src="${avatarUrl}" class="w-10 h-10 rounded-full object-cover">` 
        : `<div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center font-bold text-gray-600">${avatarInitial}</div>`;

    chatHeader.innerHTML = `
        <div class="flex items-center space-x-3 rtl:space-x-reverse">
            ${avatar}
            <div>
                <h3 class="text-xl font-semibold text-gray-800">${otherUserName}</h3>
                <p class="text-sm text-gray-500 font-semibold">${otherUserType}</p>
            </div>
        </div>`;

    document.querySelector('.chat-request-info')?.remove();
    if (requestId) {
        const request = this.db.getRequests().find(r => r.id === requestId);
        if (request) {
            const requestInfoHtml = `
                <div class="text-center text-xs text-gray-500 p-2 bg-gray-100 rounded-md my-2 border-b chat-request-info">
                    Conversation regarding request: <strong class="text-primary">${request.product}</strong>
                </div>`;
            chatHeader.insertAdjacentHTML('afterend', requestInfoHtml);
        }
    }

    chatMessages.innerHTML = 'Loading messages...';
    chatInputArea.classList.remove('hidden');
    chatInputArea.dataset.conversationId = conversationId;

    try {
        const response = await fetch(`${globtrade_data.api_url}conversations/${conversationId}/messages`, { headers: { 'X-WP-Nonce': globtrade_data.nonce } });
        if (!response.ok) throw new Error('Could not load messages.');
        const messages = await response.json();
        this.renderChatMessages(messages, otherUserName);
    } catch (error) {
        chatMessages.innerHTML = `<p class="text-red-500 p-4">${error.message}</p>`;
    }
}

        renderChatMessages(messages, otherUserName) {
    const chatMessagesElem = document.getElementById('chat-messages');
    if(!messages || messages.length === 0) {
        chatMessagesElem.innerHTML = `<div class="p-4 text-center text-gray-500">No messages yet.</div>`;
        return;
    }
    chatMessagesElem.innerHTML = messages.map(msg => {
        const isCurrentUser = msg.sender_id == this.currentUser.id;
        const senderName = isCurrentUser ? 'You' : otherUserName;
        const messageDate = new Date(msg.timestamp * 1000).toLocaleString('en-US', { month: 'numeric', day: 'numeric', year: '2-digit', hour: 'numeric', minute: '2-digit' });
        return `
        <div class="flex ${isCurrentUser ? 'justify-end' : 'justify-start'}">
            <div class="chat-bubble ${isCurrentUser ? 'sent' : 'received'}">
                <p class="font-semibold text-sm mb-1">${senderName}</p>
                <p>${msg.content}</p>
                <span class="text-xs opacity-75 block mt-2 text-right">${messageDate}</span>
            </div>
        </div>`;
    }).join('');
    chatMessagesElem.scrollTop = chatMessagesElem.scrollHeight;
}

        async sendMessage() {
            const messageInput = document.getElementById('message-input');
            const content = messageInput.value.trim();
            const conversationId = this.currentConversationId;

            if (!content || !conversationId) return;
            const originalInput = messageInput.value;
            messageInput.value = 'Sending...';
            messageInput.disabled = true;

            try {
                const response = await fetch(`${globtrade_data.api_url}conversations/${conversationId}/messages`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': globtrade_data.nonce },
                    body: JSON.stringify({ content: content })
                });
                if (!response.ok) throw new Error('Message could not be sent.');
                messageInput.value = '';
                this.openConversation(conversationId, document.getElementById('chat-header').textContent);
            } catch (error) {
                this.showNotification(error.message, 'error');
                messageInput.value = originalInput;
            } finally {
                messageInput.disabled = false;
            }
        }
        
        async loadAgreements() {
        const container = document.getElementById('agreements-list');
        if (!container) return;
        container.innerHTML = `<div class="p-8 text-center text-gray-500">Loading agreements...</div>`;
        try {
            const response = await fetch(`${globtrade_data.api_url}agreements`, { headers: { 'X-WP-Nonce': globtrade_data.nonce } });
            if (!response.ok) throw new Error('Could not load agreements.');
            const agreements = await response.json();
            this.renderAgreementsList(agreements);
        } catch (error) {
            container.innerHTML = `<p class="text-red-500 p-4">${error.message}</p>`;
        }
    }

   renderAgreementsList(agreements) {
    const container = document.getElementById('agreements-list');
    if (!agreements || agreements.length === 0) {
        container.innerHTML = '<div class="p-8 text-center text-gray-500">No completed agreements to display.</div>';
        return;
    }
    const agreementsHtml = agreements.map(agreement => `
        <div class="p-4 hover:bg-gray-50 transition-colors">
            <div class="flex justify-between items-center">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-primary-light">Agreement: ${agreement.request_details.product_name}</h3>
                    <p class="text-gray-600 mt-1">Between ${agreement.importer.company_name} and ${agreement.exporter.company_name}</p>
                </div>
                <div>
                    <button class="btn-primary text-sm px-4 py-2 download-agreement-btn" data-agreement='${JSON.stringify(agreement)}'>
                        <i class="fas fa-file-pdf mr-1"></i>Download
                    </button>
                </div>
            </div>
        </div>`).join('');
    container.innerHTML = agreementsHtml;
}

    async generateAgreementPDF(agreement) {
    const logoUrl = globtrade_data.logo_url;
    const sealUrl = globtrade_data.seal_url;

    const agreementHTML = `
        <div style="background: white; width: 21cm; min-height: 29.7cm; margin: 0; padding: 2cm; font-family: 'Cairo', sans-serif; display: flex; flex-direction: column;">
            <header style="text-align: center; margin-bottom: 2rem;">
                <img src="${logoUrl}" style="height: 64px; margin: 0 auto 0.5rem auto; object-fit: contain;">
                <h1 style="font-family: 'Cairo', sans-serif; font-weight: 900; color: #111827; font-size: 1.875rem;">GLOBTRADE</h1>
                <p style="font-family: 'Cairo', sans-serif; color: #4b5563; font-weight: 600; letter-spacing: 1.5px; font-size: 0.9rem;">GLOBAL DEALS MAKERS</p>
            </header>
            <main style="flex-grow: 1;">
                <div style="text-align: center; margin-bottom: 2.5rem;">
                    <h2 style="font-size: 1.5rem; font-weight: 700; letter-spacing: 0.05em; color: #1f2937;">Transaction Completion Confirmation</h2>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem; font-size: 0.9rem; line-height: 1.8;">
                    <div>
                        <h3 style="font-weight: 700; color: #9f1239; margin-bottom: 1rem; font-size: 1.1rem;">Importer Information:</h3>
                        <p><strong>Company Name:</strong> <span>${agreement.importer.company_name}</span></p>
                        <p><strong>Email:</strong> <span>${agreement.importer.email}</span></p>
                        <p><strong>Country:</strong> <span>${agreement.importer.country}</span></p>
                        <p><strong>Commercial Reg. No.:</strong> <span>${agreement.importer.reg_no}</span></p>
                    </div>
                    <div>
                        <h3 style="font-weight: 700; color: #9f1239; margin-bottom: 1rem; font-size: 1.1rem;">Exporter Information:</h3>
                        <p><strong>Company Name:</strong> <span>${agreement.exporter.company_name}</span></p>
                        <p><strong>Email:</strong> <span>${agreement.exporter.email}</span></p>
                        <p><strong>Country:</strong> <span>${agreement.exporter.country}</span></p>
                        <p><strong>Commercial Reg. No.:</strong> <span>${agreement.exporter.reg_no}</span></p>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; font-size: 0.9rem; line-height: 1.8;">
                    <div>
                        <h3 style="font-weight: 700; color: #9f1239; margin-bottom: 1rem; font-size: 1.1rem;">Purchase Request Details:</h3>
                        <p><strong>Request ID:</strong> <span>${agreement.request_id}</span></p>
                        <p><strong>Product Name:</strong> <span>${agreement.request_details.product_name}</span></p>
                        <p><strong>Required Quantity:</strong> <span>${agreement.request_details.quantity}</span></p>
                        <p><strong>Specifications:</strong> <span>${agreement.request_details.specifications}</span></p>
                    </div>
                    <div>
                        <h3 style="font-weight: 700; color: #9f1239; margin-bottom: 1rem; font-size: 1.1rem;">Accepted Offer Details:</h3>
                        <p><strong>Offer ID:</strong> <span>${agreement.offer_id}</span></p>
                        <p><strong>Agreed Price:</strong> <span>${agreement.offer_details.price}</span></p>
                        <p><strong>Agreed Quantity:</strong> <span>${agreement.offer_details.quantity}</span></p>
                        <p><strong>Payment Method:</strong> <span>${agreement.offer_details.payment_method}</span></p>
                        <p><strong>Shipping (Incoterms):</strong> <span>${agreement.offer_details.shipping_method}</span></p>
                        <p><strong>Port/Destination:</strong> <span>${agreement.offer_details.port_destination}</span></p>
                    </div>
                </div>
            </main>
            <footer style="margin-top: auto; padding-top: 2rem; text-align: center;">
                ${sealUrl ? `<div style="position: relative; width: 160px; height: 160px; margin: 0 auto 1rem auto;"><img src="${sealUrl}" style="width: 100%; height: 100%; object-fit: contain; opacity: 0.8;"><p style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-12deg); font-family: 'Dancing Script', cursive; font-size: 2.5rem; color: #9f1239; opacity: 0.9;">Globtrade</p></div>` : ''}
                <div style="width: 100%; border-top: 2px solid #374151; padding-top: 1rem; font-size: 0.8rem; color: #4b5563;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <span><strong>Document ID:</strong> ${agreement.document_id}</span>
                        <span><strong>Date of Issue:</strong> ${agreement.issue_date}</span>
                        <span><strong>Transaction Status:</strong> Completed</span>
                    </div>
                    <p style="font-size: 0.75rem; color: #4b5563; max-width: 90%; margin: 0 auto;">
                        <strong style="color: #9f1239;">Note:</strong> This transaction is a preliminary agreement on the deal until implementation begins and contracts are signed directly outside the platform.
                    </p>
                    <p style="font-weight: 700; color: #111827; margin-top: 1rem; letter-spacing: 0.05em;">www.globtradenet.com</p>
                </div>
            </footer>
        </div>`;
    
    const container = document.createElement('div');
    container.style.position = 'fixed';
    container.style.left = '-30cm';
    container.innerHTML = agreementHTML;
    document.body.appendChild(container);
    
    try {
        const { jsPDF } = window.jspdf;
        const canvas = await html2canvas(container.firstElementChild, { scale: 2, useCORS: true });
        const pdf = new jsPDF({ orientation: 'p', unit: 'mm', format: 'a4' });
        pdf.addImage(canvas.toDataURL('image/png'), 'PNG', 0, 0, 210, 297);
        pdf.save(`Agreement-${agreement.request_id}.pdf`);
    } catch (err) {
        console.error(err);
        this.showNotification('Error generating PDF.', 'error');
    } finally {
        document.body.removeChild(container);
    }
}
    
   
async loadSubscriptionPage() {
        const currentSubContainer = document.getElementById('current-subscription-container');
        const availablePackagesContainer = document.getElementById('available-packages-container');
        if (!currentSubContainer || !availablePackagesContainer) return;

        currentSubContainer.innerHTML = `<p class="text-center text-gray-500">Loading...</p>`;
        availablePackagesContainer.innerHTML = `<p class="text-center text-gray-500 col-span-full">Loading...</p>`;

        try {
            const [subResponse, packagesResponse] = await Promise.all([
                fetch(`${globtrade_data.api_url}users/me/subscription`, { headers: { 'X-WP-Nonce': globtrade_data.nonce } }),
                fetch(`${globtrade_data.api_url}packages`, { headers: { 'X-WP-Nonce': globtrade_data.nonce } })
            ]);

            if (!subResponse.ok) throw new Error('Could not load your subscription details.');
            if (!packagesResponse.ok) throw new Error('Could not load available packages.');

            const currentSub = await subResponse.json();
            const availablePackages = await packagesResponse.json();

            this.renderCurrentSubscription(currentSub);
            this.renderAvailablePackages(availablePackages, currentSub);

        } catch (error) {
            currentSubContainer.innerHTML = `<p class="text-center text-red-500">${error.message}</p>`;
            availablePackagesContainer.innerHTML = '';
        }
    }

    renderCurrentSubscription(sub, availablePackages) {
    const container = document.getElementById('current-subscription-container');
    const packageNames = {'exporter-package-1': 'Exporter Package 1','exporter-package-2': 'Exporter Package 2','exporter-package-3': 'Exporter Package 3','importer-package': 'Importer Package'};
    const credits = (this.currentUser.role === 'importer') ? 'Unlimited' : (sub.credits || '0');

    if (sub.package) {
        container.innerHTML = `
            <div class="p-6 bg-green-50 rounded-lg text-center"><p class="text-gray-600 text-sm font-semibold">YOUR CURRENT PLAN</p><p class="text-3xl font-bold text-primary my-2">${packageNames[sub.package] || sub.package}</p></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-center"><div class="p-4 bg-gray-50 rounded-lg"><p class="text-gray-600 text-sm font-semibold">REMAINING CREDITS / OFFERS</p><p class="text-2xl font-bold">${credits}</p></div><div class="p-4 bg-gray-50 rounded-lg"><p class="text-gray-600 text-sm font-semibold">EXPIRES ON</p><p class="text-2xl font-bold">${sub.end_date}</p></div></div>`;
    } else if (sub.pending_package_id) {
        const pendingPackage = availablePackages.find(p => p.id == sub.pending_package_id);
        const packageName = pendingPackage ? pendingPackage.name : 'Selected Package';
        container.innerHTML = `
            <div class="p-6 bg-yellow-50 text-yellow-800 rounded-lg text-center">
                <p class="font-bold">Your selected package "${packageName}" is awaiting payment.</p>
                <p class="text-sm mt-2">Please complete the payment to activate your account features.</p>
                <button class="btn-primary mt-4 pay-now-btn" data-product-id="${sub.pending_package_id}">Pay Now</button>
            </div>`;
    } else {
         container.innerHTML = `<div class="p-6 bg-red-50 text-red-700 rounded-lg text-center"><p class="font-bold">You do not have an active subscription.</p></div>`;
    }
}

   renderAvailablePackages(packages, currentSub) {
    const container = document.getElementById('available-packages-container');
    if (!packages || packages.length === 0) {
        container.innerHTML = `<p class="col-span-full text-center text-gray-500">No packages available for your account type.</p>`;
        return;
    }
    container.innerHTML = packages.map(pkg => {
        const isCurrentPackage = pkg.name.toLowerCase().replace(/\s/g, '-') === currentSub.package;
        let buttonHtml = isCurrentPackage
            ? `<a href="${currentSub.my_account_url}" target="_blank" class="w-full mt-4 px-4 py-3 bg-gray-500 text-white rounded-lg font-semibold block text-center">Your Current Plan</a>`
            : `<a href="${pkg.add_to_cart_url}" class="w-full mt-4 btn-primary">Purchase / Upgrade</a>`;

        return `
        <div class="card p-6 border-2 ${isCurrentPackage ? 'border-primary' : 'border-gray-200'}">
            <h4 class="text-xl font-bold text-center">${pkg.name}</h4>
            <div class="text-3xl font-bold my-4 text-center">${pkg.price_html}</div>
            <div class="text-gray-600 text-sm space-y-2">${pkg.description || ''}</div>
            ${buttonHtml}
        </div>`;
    }).join('');
}
async loadSubscriptionPage() {
    const currentSubContainer = document.getElementById('current-subscription-container');
    const availablePackagesContainer = document.getElementById('available-packages-container');
    currentSubContainer.innerHTML = `<p class="text-center">Loading...</p>`;
    availablePackagesContainer.innerHTML = `<p class="col-span-full text-center">Loading...</p>`;
    try {
        const [subResponse, packagesResponse] = await Promise.all([
            fetch(`${globtrade_data.api_url}users/me/subscription`, { headers: { 'X-WP-Nonce': globtrade_data.nonce } }),
            fetch(`${globtrade_data.api_url}packages`, { headers: { 'X-WP-Nonce': globtrade_data.nonce } })
        ]);
        const currentSub = await subResponse.json();
        const availablePackages = await packagesResponse.json();
        this.renderCurrentSubscription(currentSub, availablePackages);
        this.renderAvailablePackages(availablePackages, currentSub);
    } catch (error) {
        currentSubContainer.innerHTML = `<p class="text-center text-red-500">${error.message}</p>`;
    }
}

async handlePayNow(productId) {
    try {
        const response = await fetch(`${globtrade_data.api_url}initiate-payment`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': globtrade_data.nonce },
            body: JSON.stringify({ product_id: productId })
        });
        const result = await response.json();
        if (!response.ok || !result.checkout_url) throw new Error(result.message || 'Could not initiate payment.');
        window.location.href = result.checkout_url;
    } catch (error) {
        this.showNotification(error.message, 'error');
    }
}

async loadCommunitySection() {
    const wrapper = document.getElementById('community-content-wrapper');
    const upgradeMsg = document.getElementById('community-upgrade-message');
    const userIsImporter = this.currentUser.role === 'importer';
    const userIsP3Exporter = this.currentUser.role === 'exporter' && this.currentUser.package === 'exporter-package-3';

    if (userIsImporter || userIsP3Exporter) {
        wrapper.classList.remove('section-hidden');
        upgradeMsg.classList.add('section-hidden');
        this.loadCommunityUsers();
        this.loadMarketingOffers();
    } else {
        wrapper.classList.add('section-hidden');
        upgradeMsg.classList.remove('section-hidden');
    }
}

async loadCommunityUsers() {
    const container = document.getElementById('users-list');
    container.innerHTML = `<div class="p-8 text-center text-gray-500">Loading community users...</div>`;
    try {
        const response = await fetch(`${globtrade_data.api_url}users`, {
            headers: { 'X-WP-Nonce': globtrade_data.nonce }
        });
        if (!response.ok) {
            throw new Error('Failed to fetch community users.');
        }
        const users = await response.json();
        this.renderUsersList(users);
    } catch (error) {
        console.error(error);
        container.innerHTML = `<div class="p-8 text-center text-red-500">Error loading users. You may not have permission to view this section.</div>`;
    }
}

renderUsersList(users) {
    const container = document.getElementById('users-list');
    if (!users || users.length === 0) {
        container.innerHTML = `<div class="p-8 text-center text-gray-500">No other users found in the community.</div>`;
        return;
    }
    container.innerHTML = users.map(user => {
        const avatar = user.logoUrl ? `<img src="${user.logoUrl}" class="w-10 h-10 rounded-full object-cover">` : `<div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center font-bold text-gray-600">${user.company.charAt(0).toUpperCase()}</div>`;
        const countryFlag = user.countryCode ? `<img src="https://flagcdn.com/w20/${user.countryCode.toLowerCase()}.png" alt="${user.country}" class="w-5 mr-2 border border-gray-200 rounded-sm">` : '';

        return `
        <div class="p-4 hover:bg-gray-50 transition-colors">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    ${avatar}
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-primary">${user.company}</h3>
                        <div class="flex items-center space-x-4 rtl:space-x-reverse mt-1 text-sm text-gray-500">
                            <span class="flex items-center">${countryFlag}${user.country}</span>
                            <span><i class="fas fa-user-tag mr-1"></i>${user.type === 'exporter' ? 'Exporter' : 'Importer'}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button data-action="view-profile" data-id="${user.id}" class="px-3 py-1 bg-gray-500 text-white rounded-lg text-sm hover:bg-gray-600">Profile</button>
                    <button data-action="start-chat" data-id="${user.id}" class="px-3 py-1 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600">Chat</button>
                </div>
            </div>
        </div>`;
    }).join('');
}
async showUserDetailsModal(userId) {
    this.detailsModal.classList.remove('hidden');
    setTimeout(() => this.detailsModal.classList.add('show'), 10);
    this.detailsModalTitle.textContent = 'Loading Profile...';
    this.detailsModalContent.innerHTML = '<div class="text-center p-4">...</div>';
    this.detailsModalActions.innerHTML = '';

    try {
        const response = await fetch(`${globtrade_data.api_url}users/${userId}`, {
            headers: { 'X-WP-Nonce': globtrade_data.nonce }
        });
        if (!response.ok) throw new Error('Could not fetch user profile.');
        const user = await response.json();
        
        this.detailsModalTitle.textContent = `Profile: ${user.company}`;
        const countryFlag = user.countryCode ? `<img src="https://flagcdn.com/w20/${user.countryCode.toLowerCase()}.png" alt="${user.country}" class="inline-block mr-2 border">` : '';
        
        this.detailsModalContent.innerHTML = `
            <div class="space-y-3">
                <p><strong>Company Name:</strong> ${user.company}</p>
                <p><strong>Account Type:</strong> ${user.type}</p>
                <p><strong>Business Category:</strong> ${user.category || 'N/A'}</p>
                <p><strong>Country:</strong> ${countryFlag}${user.country}</p>
                <p><strong>Description:</strong> ${user.description}</p>
            </div>
        `;
        this.detailsModalActions.innerHTML = `<button data-action="start-chat" data-id="${user.id}" class="btn-primary">Message</button>`;
    } catch (error) {
        this.detailsModalContent.innerHTML = `<p class="text-red-500">${error.message}</p>`;
    }
}
async loadDashboard() {
    if (!this.currentUser) return;

    // First, fetch the main stats
    try {
        const statsResponse = await fetch(`${globtrade_data.api_url}dashboard-stats`, {
            headers: { 'X-WP-Nonce': globtrade_data.nonce }
        });
        if (statsResponse.ok) {
            const stats = await statsResponse.json();
            document.getElementById('total-my-submitted-offers').textContent = stats.my_submitted_offers;
            document.getElementById('total-my-requests-dashboard').textContent = stats.my_requests;
            document.getElementById('total-new-messages').textContent = stats.new_messages;
            document.getElementById('total-registered-users').textContent = stats.registered_users;
        }
    } catch (error) {
        console.error("Failed to load dashboard stats:", error);
    }

    // Next, fetch the recent items list based on user role
    try {
        if (this.currentUser.role === 'exporter') {
            const offersResponse = await fetch(`${globtrade_data.api_url}offers/me`, { 
                headers: { 'X-WP-Nonce': globtrade_data.nonce } 
            });
            if (offersResponse.ok) {
                const offers = await offersResponse.json();
                this.renderDashboardRecentItems(offers.slice(-3).reverse(), 'recent-my-submitted-offers-dashboard', 'offer');
            }
            document.getElementById('recent-my-requests-dashboard-card').classList.add('section-hidden');
            document.getElementById('recent-my-submitted-offers-dashboard').parentElement.classList.remove('section-hidden');

        } else if (this.currentUser.role === 'importer') {
            const requestsResponse = await fetch(`${globtrade_data.api_url}requests/me`, { 
                headers: { 'X-WP-Nonce': globtrade_data.nonce } 
            });
            if (requestsResponse.ok) {
                const requests = await requestsResponse.json();
                this.renderDashboardRecentItems(requests.slice(-3).reverse(), 'recent-my-requests-dashboard', 'request');
            }
            document.getElementById('recent-my-requests-dashboard-card').classList.remove('section-hidden');
            document.getElementById('recent-my-submitted-offers-dashboard').parentElement.classList.add('section-hidden');
        }
    } catch (error) {
        console.error("Failed to load recent items for dashboard:", error);
    }
}

renderDashboardRecentItems(items, containerId, type) {
    const container = document.getElementById(containerId);
    if (!container) return;

    if (items.length === 0) {
        container.innerHTML = '<div class="p-3 text-center text-gray-500">No items to display.</div>';
        return;
    }

    container.innerHTML = items.map(item => {
        let title = '';
        let details = '';
        let iconClass = 'fa-question-circle';

        if (type === 'offer') {
            title = `Offer on: ${item.request.title}`;
            details = `To: ${item.importer.company_name}`;
            iconClass = 'fa-tags';
        } else if (type === 'request') {
            title = item.title;
            const budget = item.meta['request-budget']?.[0] || 'N/A';
            details = `Budget: ${budget}`;
            iconClass = 'fa-shopping-cart';
        }

        return `
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center text-lg text-primary">
                    <i class="fas ${iconClass}"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800">${title}</h4>
                    <p class="text-sm text-gray-600">${details}</p>
                </div>
            </div>
            <span class="text-xs text-gray-500">${item.date || new Date().toLocaleDateString()}</span>
        </div>`;
    }).join('');
}
async startConversationWithUser(userId) {
    try {
        const response = await fetch(`${globtrade_data.api_url}conversations/initiate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': globtrade_data.nonce
            },
            body: JSON.stringify({ user_id: userId })
        });

        if (!response.ok) {
            throw new Error('Could not start conversation.');
        }

        const data = await response.json();
        if (data.status === 'success') {
            this.navigateToSection('messages');
            setTimeout(() => {
                this.openConversation(data.conversation_id, data.other_user.company_name, data.other_user.type, data.other_user.avatar_url);
            }, 100);
        }
    } catch (error) {
        console.error('Error starting conversation:', error);
        this.showNotification('Failed to start conversation. Please try again.', 'error');
    }
}

async loadConversations() {
    const container = document.getElementById('conversations-list');
    if (!container) return;
    container.innerHTML = `<div class="p-4 text-center text-gray-500">Loading conversations...</div>`;
    try {
        const response = await fetch(`${globtrade_data.api_url}conversations`, {
            headers: { 'X-WP-Nonce': globtrade_data.nonce }
        });
        if (!response.ok) throw new Error('Failed to load conversations.');
        const conversations = await response.json();
        this.renderConversations(conversations);
    } catch (error) {
        console.error(error);
        container.innerHTML = `<div class="p-4 text-center text-red-500">Could not load conversations.</div>`;
    }
}

renderConversations(conversations) {
    const container = document.getElementById('conversations-list');
    if (!conversations || conversations.length === 0) {
        container.innerHTML = `<div class="p-4 text-center text-gray-500">No conversations.</div>`;
        return;
    }
    container.innerHTML = conversations.map(conv => {
        const avatarInitial = conv.other_user.company_name.charAt(0).toUpperCase();
        const avatar = conv.other_user.avatar_url
            ? `<img src="${conv.other_user.avatar_url}" class="w-10 h-10 rounded-full object-cover">`
            : `<div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center font-bold text-gray-600">${avatarInitial}</div>`;

        return `
        <div class="conversation-item flex items-center space-x-3 rtl:space-x-reverse p-3 hover:bg-gray-50 rounded-lg cursor-pointer" 
             data-conversation-id="${conv.id}" 
             data-username="${conv.other_user.company_name}"
             data-usertype="${conv.other_user.type}"
             data-avatar-url="${conv.other_user.avatar_url || ''}"
             data-request-id="${conv.request_id || ''}">
            ${avatar}
            <div class="flex-1 overflow-hidden">
                <h4 class="font-semibold text-primary-light truncate">${conv.other_user.company_name}</h4>
                <p class="text-sm text-gray-600 truncate">${conv.last_message}</p>
            </div>
            <span class="text-xs text-gray-400 self-start">${conv.last_message_date}</span>
        </div>`;
    }).join('');
}

async openConversation(conversationId, otherUserName, otherUserType, avatarUrl, requestId = null) {
    this.currentConversationId = conversationId;
    const chatHeader = document.getElementById('chat-header');
    const chatMessages = document.getElementById('chat-messages');
    const chatInputArea = document.getElementById('chat-input-area');

    const avatarInitial = otherUserName.charAt(0).toUpperCase();
    const avatar = avatarUrl 
        ? `<img src="${avatarUrl}" class="w-10 h-10 rounded-full object-cover">` 
        : `<div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center font-bold text-gray-600">${avatarInitial}</div>`;

    chatHeader.innerHTML = `
        <div class="flex items-center space-x-3 rtl:space-x-reverse">
            ${avatar}
            <div>
                <h3 class="text-xl font-semibold text-gray-800">${otherUserName}</h3>
                <p class="text-sm text-gray-500 font-semibold">${otherUserType}</p>
            </div>
        </div>`;

    chatMessages.innerHTML = 'Loading messages...';
    chatInputArea.classList.remove('hidden');

    try {
        const response = await fetch(`${globtrade_data.api_url}conversations/${conversationId}/messages`, { 
            headers: { 'X-WP-Nonce': globtrade_data.nonce } 
        });
        if (!response.ok) throw new Error('Could not load messages.');
        const messages = await response.json();
        this.renderChatMessages(messages, otherUserName);
    } catch (error) {
        chatMessages.innerHTML = `<p class="text-red-500 p-4">${error.message}</p>`;
    }
}

renderChatMessages(messages, otherUserName) {
    const chatMessagesElem = document.getElementById('chat-messages');
    if(!messages || messages.length === 0) {
        chatMessagesElem.innerHTML = `<div class="p-4 text-center text-gray-500">No messages yet. Say hello!</div>`;
        return;
    }
    chatMessagesElem.innerHTML = messages.map(msg => {
        const isCurrentUser = msg.sender_id == this.currentUser.id;
        const senderName = isCurrentUser ? 'You' : otherUserName;
        const messageDate = new Date(msg.timestamp * 1000).toLocaleString('en-US', { month: 'numeric', day: 'numeric', hour: 'numeric', minute: '2-digit' });
        return `
        <div class="flex ${isCurrentUser ? 'justify-end' : 'justify-start'}">
            <div class="chat-bubble ${isCurrentUser ? 'sent' : 'received'}">
                <p class="font-semibold text-sm mb-1">${senderName}</p>
                <p>${msg.content}</p>
                <span class="text-xs opacity-75 block mt-2 text-right">${messageDate}</span>
            </div>
        </div>`;
    }).join('');
    chatMessagesElem.scrollTop = chatMessagesElem.scrollHeight;
}

async sendMessage() {
    const messageInput = document.getElementById('message-input');
    const content = messageInput.value.trim();
    if (!content || !this.currentConversationId) return;

    const originalInput = messageInput.value;
    messageInput.value = '';
    messageInput.disabled = true;

    try {
        const response = await fetch(`${globtrade_data.api_url}conversations/${this.currentConversationId}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': globtrade_data.nonce
            },
            body: JSON.stringify({ content: content })
        });
        if (!response.ok) throw new Error('Message could not be sent.');
        
        const chatHeaderDiv = document.getElementById('chat-header');
        const username = chatHeaderDiv.querySelector('h3').textContent;
        const usertype = chatHeaderDiv.querySelector('p').textContent;
        const avatarUrl = chatHeaderDiv.querySelector('img')?.src || '';

        this.openConversation(this.currentConversationId, username, usertype, avatarUrl);
    } catch (error) {
        this.showNotification(error.message, 'error');
        messageInput.value = originalInput;
    } finally {
        messageInput.disabled = false;
        messageInput.focus();
    }
}
async loadMarketingOffers() {
    const container = document.getElementById('marketing-offers-content');
    if (!container) return;
    container.innerHTML = `<div class="p-8 text-center text-gray-500">Loading offers...</div>`;

    try {
        const response = await fetch(`${globtrade_data.api_url}marketing-offers`, {
            headers: { 'X-WP-Nonce': globtrade_data.nonce }
        });
        if (!response.ok) throw new Error('Failed to load marketing offers.');
        const offers = await response.json();
        this.renderMarketingOffers(offers);
    } catch (error) {
        console.error(error);
        container.innerHTML = `<div class="p-8 text-center text-red-500">Could not load marketing offers.</div>`;
    }
}

renderMarketingOffers(offers) {
    const container = document.getElementById('marketing-offers-content');
    if (this.currentUser.role === 'importer') {
        const offersHtml = offers.length > 0 ? offers.map(offer => {
            let buttonsHtml = '';
            const hasRejected = offer.rejected_by.includes(this.currentUser.id);

            if (offer.status === 'completed') {
                buttonsHtml = `<span class="badge success">Deal Closed</span>`;
                if(offer.accepted_by == this.currentUser.id) {
                     buttonsHtml = `<span class="badge success">You Accepted This</span>`;
                }
            } else if (hasRejected) {
                buttonsHtml = `<span class="badge error">You Rejected This</span>`;
            } else {
                buttonsHtml = `
                    <button data-action="accept-marketing-offer" data-id="${offer.id}" class="px-3 py-1 bg-green-500 text-white rounded-lg text-sm">Accept</button>
                    <button data-action="reject-marketing-offer" data-id="${offer.id}" class="px-3 py-1 bg-red-500 text-white rounded-lg text-sm">Reject</button>
                `;
            }
            return `
                <div class="p-4 border rounded-lg mb-4 hover:bg-gray-50">
                    <h4 class="font-bold text-lg">${offer.product}</h4>
                    <p class="text-sm text-gray-500 mb-2">From: ${offer.exporter.company_name}</p>
                    <p>${offer.description}</p>
                    <p class="mt-2"><strong>Available Quantity:</strong> ${offer.quantity}</p>
                    <div class="flex justify-end space-x-2 mt-4">${buttonsHtml}</div>
                </div>`;
        }).join('') : '<div class="p-8 text-center text-gray-500">No marketing offers available for you.</div>';
        container.innerHTML = `<h3 class="text-xl font-bold mb-4">Incoming Marketing Offers</h3>${offersHtml}`;
    } else {
         const myOffersHtml = offers.length > 0 ? offers.map(offer => {
            const isCompleted = offer.status === 'completed';
            return `
            <div class="p-4 border rounded-lg mb-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-bold text-lg">${offer.product}</h4>
                         ${isCompleted ? `<p class="text-sm font-bold text-green-600 mt-1">Accepted</p>` : `<p class="text-sm font-bold text-yellow-600 mt-1">Status: ${offer.status}</p>`}
                    </div>
                </div>
            </div>`;
        }).join('') : '<p class="text-center text-gray-500">You have not created any marketing offers.</p>';
        container.innerHTML = `
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">My Marketing Offers</h3>
                <button data-action="show-create-marketing-offer" class="btn-primary">Create New Offer</button>
            </div>
            <div>${myOffersHtml}</div>`;
    }
}

showCreateMarketingOfferModal() {
    const modal = document.getElementById('create-marketing-offer-modal');
    modal.classList.remove('hidden');
    setTimeout(() => modal.classList.add('show'), 10);
    document.getElementById('marketing-offer-form').reset();

    jQuery('#marketing-offer-categories').select2({
        placeholder: 'Select target categories...',
        allowClear: true,
        data: this.tradeCategories
    }).val(null).trigger('change');

    jQuery('#marketing-offer-quantity-unit').select2({
        placeholder: 'Select or type a unit',
        allowClear: true,
        tags: true,
        data: this.quantityUnits
    }).val(null).trigger('change');

    document.getElementById('marketing-offer-audience-details').classList.add('hidden');
}

async handleCreateMarketingOffer(event) {
    event.preventDefault();
    const form = event.target;
    const selectedImporterIds = [];
    form.querySelectorAll('.marketing-importer-checkbox:checked').forEach(checkbox => {
        selectedImporterIds.push(checkbox.dataset.importerId);
    });

    if (selectedImporterIds.length === 0) {
        this.showNotification('You must select at least one importer to target.', 'warning');
        return;
    }

    const offerData = {
        product: form.querySelector('#marketing-offer-product').value,
        description: form.querySelector('#marketing-offer-description').value,
        quantity: `${form.querySelector('#marketing-offer-quantity-value').value} ${jQuery('#marketing-offer-quantity-unit').val()}`,
        target_importer_ids: selectedImporterIds
    };

    try {
        const response = await fetch(`${globtrade_data.api_url}marketing-offers`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': globtrade_data.nonce },
            body: JSON.stringify(offerData)
        });
        if (!response.ok) throw new Error('Failed to create marketing offer.');
        
        this.showNotification('Marketing offer created successfully!', 'success');
        this.closeModals();
        this.loadMarketingOffers();
    } catch (error) {
        this.showNotification(error.message, 'error');
    }
}

async getAllImporters() {
    try {
        const response = await fetch(`${globtrade_data.api_url}users?role=importer`, {
            headers: { 'X-WP-Nonce': globtrade_data.nonce }
        });
        if (!response.ok) return [];
        return await response.json();
    } catch (error) {
        return [];
    }
}

async handleMarketingOfferAction(offerId, action) {
    try {
        await fetch(`${globtrade_data.api_url}marketing-offers/${offerId}/handle`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': globtrade_data.nonce },
            body: JSON.stringify({ action: action })
        });
        this.showNotification(`Offer successfully ${action}ed.`, 'success');
        this.loadMarketingOffers();
    } catch (error) {
        this.showNotification(`Failed to ${action} offer.`, 'error');
    }
}
async handleMarketingCategoryChange() {
    const selectedCategories = jQuery('#marketing-offer-categories').val();
    const audienceDetailsContainer = document.getElementById('marketing-offer-audience-details');
    const countContainer = document.getElementById('marketing-offer-importer-count');
    const listContainer = document.getElementById('marketing-offer-importer-list');

    if (!selectedCategories || selectedCategories.length === 0) {
        audienceDetailsContainer.classList.add('hidden');
        return;
    }

    const allImporters = await this.getAllImporters();
    const matchingImporters = allImporters.filter(importer => {
        return importer.category && selectedCategories.includes(importer.category);
    });

    countContainer.textContent = `Found ${matchingImporters.length} matching importers.`;
    
    if (matchingImporters.length > 0) {
        listContainer.innerHTML = matchingImporters.map(importer => `
            <div class="flex items-center justify-between p-2">
                <label for="importer-${importer.id}" class="flex items-center cursor-pointer">
                    <input type="checkbox" id="importer-${importer.id}" data-importer-id="${importer.id}" class="mr-2 marketing-importer-checkbox" checked>
                    <span>${importer.company} (${importer.country || 'N/A'})</span>
                </label>
            </div>
        `).join('');
    } else {
        listContainer.innerHTML = '<p class="text-sm text-gray-500 p-2">No importers found for the selected categories.</p>';
    }
    
    audienceDetailsContainer.classList.remove('hidden');
}
renderSocialMediaInputs() {
    const container = document.getElementById('profile-socials-container');
    if (!container) return;
    container.innerHTML = '';
    const socials = this.currentUser.socials || [];
    if (socials.length > 0) {
        socials.forEach((social) => {
            this.addSocialMediaInput(social.type, social.url);
        });
    }
}

addSocialMediaInput(type = 'website', url = '') {
    const container = document.getElementById('profile-socials-container');
    if (!container) return;
    const socialLinkDiv = document.createElement('div');
    socialLinkDiv.className = 'grid grid-cols-[auto_1fr_auto] items-center gap-4 social-media-item';
    socialLinkDiv.innerHTML = `
        <div>
            <select class="w-full px-4 py-3 border rounded-lg social-type">
                <option value="website">Website</option>
                <option value="facebook">Facebook</option>
                <option value="linkedin">LinkedIn</option>
                <option value="twitter">Twitter/X</option>
                <option value="instagram">Instagram</option>
                <option value="youtube">YouTube</option>
            </select>
        </div>
        <div>
            <input type="url" class="w-full px-4 py-3 border rounded-lg social-url" placeholder="URL" value="${url}">
        </div>
        <button type="button" class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600 remove-social-link-btn">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(socialLinkDiv);
    socialLinkDiv.querySelector('.social-type').value = type;
    socialLinkDiv.querySelector('.remove-social-link-btn').addEventListener('click', (e) => {
        e.target.closest('.social-media-item').remove();
    });
}


renderVerificationStatus() {
    const container = document.getElementById('verification-status-container');
    if (!container) return;
    const docs = this.currentUser.verificationDocs || {};
    const docTypes = [
        { key: 'commercial_register', name: 'Commercial Register' },
        { key: 'tax_card', name: 'Tax Card' },
        { key: 'iban_doc', name: 'IBAN Document' }
    ];

    container.innerHTML = docTypes.map(docType => {
        const doc = docs[docType.key];
        let status = 'Not Uploaded';
        let badgeClass = 'error';
        let iconClass = 'fa-times-circle text-red-500';

        if (doc && doc.status === 'Verified') {
            status = 'Verified'; badgeClass = 'success'; iconClass = 'fa-check-circle text-green-500';
        } else if (doc && doc.status === 'Pending') {
            status = 'Pending Review'; badgeClass = 'warning'; iconClass = 'fa-clock text-yellow-500';
        }

        return `
        <div class="flex items-center justify-between">
            <span class="flex items-center text-sm"><i class="fas ${iconClass} mr-2"></i> ${docType.name}</span>
            <span class="badge ${badgeClass}">${status}</span>
        </div>`;
    }).join('');
}


showVerificationModal() {
    const modal = document.getElementById('verification-modal');
    if(modal) {
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('show'), 10);
    }
}

async handleVerificationDocsSubmit() {
    const submitButton = document.getElementById('save-verification-docs-btn');
    submitButton.disabled = true;
    submitButton.innerHTML = `<span class="spinner"></span> Preparing...`;

    const formData = new FormData();
    const fileInputs = [
        { id: 'commercial-register-input', key: 'commercial_register' },
        { id: 'tax-card-input', key: 'tax_card' },
        { id: 'iban-doc-input', key: 'iban_doc' }
    ];
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 MB
    let hasFiles = false;

    for (const inputInfo of fileInputs) {
        const input = document.getElementById(inputInfo.id);
        if (input.files[0]) {
            if (input.files[0].size > MAX_FILE_SIZE) {
                this.showNotification(`File "${input.files[0].name}" is too large. Maximum size is 5MB.`, 'error');
                submitButton.disabled = false;
                submitButton.innerHTML = 'Submit for Review';
                return;
            }
            formData.append(inputInfo.key, input.files[0]);
            hasFiles = true;
        }
    }

    if (!hasFiles) {
        this.showNotification('Please select at least one file to upload.', 'warning');
        submitButton.disabled = false;
        submitButton.innerHTML = 'Submit for Review';
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', `${globtrade_data.api_url}users/me/verification-docs`, true);
    xhr.setRequestHeader('X-WP-Nonce', globtrade_data.nonce);

    xhr.upload.onprogress = (event) => {
        if (event.lengthComputable) {
            const percentComplete = (event.loaded / event.total) * 100;
            submitButton.innerHTML = `Uploading... ${Math.round(percentComplete)}%`;
            fileInputs.forEach(inputInfo => {
                const progressContainer = document.getElementById(`${inputInfo.id.replace('-input', '')}-progress`)?.parentElement;
                if (document.getElementById(inputInfo.id).files[0] && progressContainer) {
                    progressContainer.classList.remove('hidden');
                    progressContainer.firstElementChild.style.width = `${percentComplete}%`;
                }
            });
        }
    };

    xhr.onload = () => {
        submitButton.disabled = false;
        submitButton.innerHTML = 'Submit for Review';
        if (xhr.status >= 200 && xhr.status < 300) {
            this.showNotification('Documents uploaded successfully for review.', 'success');
            this.loadUserProfile();
            this.closeModals();
        } else {
            const error = JSON.parse(xhr.responseText);
            this.showNotification(error.message || 'An error occurred during upload.', 'error');
        }
    };

    xhr.onerror = () => {
        this.showNotification('An error occurred during the transaction.', 'error');
        submitButton.disabled = false;
        submitButton.innerHTML = 'Submit for Review';
    };

    xhr.send(formData);
}
addSocialMediaInput() {
    const container = document.getElementById('profile-socials-container');
    if (!container) return;
    const socialLinkDiv = document.createElement('div');
    socialLinkDiv.className = 'grid grid-cols-[auto_1fr_auto] items-center gap-4 social-media-item';
    socialLinkDiv.innerHTML = `
        <div>
            <select class="w-full px-4 py-3 border rounded-lg social-type">
                <option value="website">Website</option>
                <option value="facebook">Facebook</option>
                <option value="linkedin">LinkedIn</option>
                <option value="twitter">Twitter/X</option>
                <option value="instagram">Instagram</option>
                <option value="youtube">YouTube</option>
            </select>
        </div>
        <div>
            <input type="url" class="w-full px-4 py-3 border rounded-lg social-url" placeholder="URL">
        </div>
        <button type="button" class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600 remove-social-link-btn">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(socialLinkDiv);
    socialLinkDiv.querySelector('.remove-social-link-btn').addEventListener('click', (e) => {
        e.target.closest('.social-media-item').remove();
    });
}
renderRequestDetailsContent(request) {
    const fields = [
        { 
            section: 'Product Details', 
            icon: 'fa-box', 
            data: [
                { label: 'Required Product', value: request.title },
                { label: 'Description', value: request.content },
                { label: 'Category', value: request.meta['request-category']?.[0] },
                { label: 'Required Quantity', value: `${request.meta['request-quantity-value']?.[0] || ''} ${request.meta['request-quantity-unit']?.[0] || ''}`.trim() },
                { label: 'Required Specifications', value: request.meta['request-specs']?.[0] }
            ]
        },
        { 
            section: 'Budget & Payment', 
            icon: 'fa-money-bill-wave', 
            data: [
                { label: 'Estimated Budget', value: `${request.meta['request-budget']?.[0] || ''} ${request.meta['request-currency']?.[0] || ''}`.trim() },
                { label: 'Preferred Payment Method', value: request.meta['request-payment']?.[0] }
            ]
        },
        { 
            section: 'Shipping & Logistics', 
            icon: 'fa-truck', 
            data: [
                { label: 'Preferred Shipping Method', value: request.meta['request-shipping']?.[0] },
                { label: 'Required Port/Destination', value: request.meta['request-port']?.[0] }
            ]
        }
    ];

    let detailsHtml = '';
    fields.forEach(section => {
        const sectionData = section.data.filter(field => field.value && field.value.trim() !== 'N/A' && field.value.trim() !== '');
        if (sectionData.length > 0) {
            detailsHtml += `<div class="modal-section-title text-lg font-bold"><i class="fas ${section.icon} text-primary"></i> ${section.section}</div>`;
            detailsHtml += '<div class="space-y-2">';
            sectionData.forEach(field => {
                detailsHtml += `<p class="py-1 border-b border-gray-100 last:border-b-0 text-sm"><strong class="font-semibold text-gray-600">${field.label}:</strong><span class="text-gray-800 ml-2">${field.value}</span></p>`;
            });
            detailsHtml += '</div>';
        }
    });

    this.detailsModalContent.innerHTML = detailsHtml;
}
async showOfferDetailsModal(offerId) {
    this.detailsModal.classList.remove('hidden');
    setTimeout(() => this.detailsModal.classList.add('show'), 10);
    this.detailsModalTitle.textContent = 'Loading Offer Details...';
    this.detailsModalContent.innerHTML = '<div class="text-center p-4">...</div>';
    this.detailsModalActions.innerHTML = '';

    try {
        const response = await fetch(`${globtrade_data.api_url}offers/${offerId}`, {
            headers: { 'X-WP-Nonce': globtrade_data.nonce }
        });
        if (!response.ok) throw new Error('Could not fetch offer details.');
        const offer = await response.json();
        
        this.detailsModalTitle.textContent = `My Offer on: ${offer.request_title}`;
        
        const price = offer.meta['offer-price']?.[0] || 'N/A';
        const currency = offer.meta['offer-currency']?.[0] || 'USD';
        const quantity = offer.meta['offer-quantity-value']?.[0] || 'N/A';
        const unit = offer.meta['offer-quantity-unit']?.[0] || '';
        const specs = offer.meta['offer-specs']?.[0] || 'No additional specifications.';
        const shipping = offer.meta['offer-shipping']?.[0] || 'As per request';
        const payment = offer.meta['offer-payment']?.[0] || 'As per request';
        
        this.detailsModalContent.innerHTML = `
            <div class="space-y-3">
                <p><strong>Proposed Price:</strong> ${price} ${currency}</p>
                <p><strong>Quantity Provided:</strong> ${quantity} ${unit}</p>
                <p><strong>Proposed Shipping:</strong> ${shipping}</p>
                <p><strong>Proposed Payment:</strong> ${payment}</p>
                <hr>
                <p><strong>Specifications:</strong></p>
                <p class="text-sm text-gray-600 pl-2">${specs}</p>
            </div>
        `;
        this.detailsModalActions.innerHTML = '';
    } catch (error) {
        this.detailsModalContent.innerHTML = `<p class="text-red-500">${error.message}</p>`;
    }
}
}
const app = new GlobtradeApp();
app.init();
});