document.addEventListener('DOMContentLoaded', function () {
    class GlobtradeApp {
        constructor() {
            this.currentUser = null;
            this.navItems = document.querySelectorAll('.nav-item');
            this.contentSections = document.querySelectorAll('.section-content');
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
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
            const closeSidebar = () => { if (sidebar && sidebarOverlay) { sidebar.classList.remove('show'); sidebarOverlay.classList.remove('show'); } };
            const openSidebar = () => { if (sidebar && sidebarOverlay) { sidebar.classList.add('show'); sidebarOverlay.classList.add('show'); } };

            if (sidebarToggle) sidebarToggle.addEventListener('click', openSidebar);
            if (sidebarCloseBtn) sidebarCloseBtn.addEventListener('click', closeSidebar);
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

            document.querySelectorAll('.modal-close-btn').forEach(btn => btn.addEventListener('click', () => this.closeModals()));

            document.getElementById('my-requests-importer-list')?.addEventListener('click', (e) => this.handleListAction(e, 'importer'));
            document.getElementById('importer-requests-for-exporter-list')?.addEventListener('click', (e) => this.handleListAction(e, 'exporter'));
            document.getElementById('my-submitted-offers-exporter-list')?.addEventListener('click', (e) => this.handleListAction(e, 'exporter_offer'));
            if (this.detailsModalActions) this.detailsModalActions.addEventListener('click', (e) => this.handleModalAction(e));

            if (this.changeLogoBtn) {
                this.changeLogoBtn.addEventListener('click', () => {
                    this.logoUploader.click();
                });
            }
            if (this.logoUploader) {
                this.logoUploader.addEventListener('change', (e) => {
                    this.handleLogoUpload(e.target.files[0]);
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

        handleModalAction(e) {
            const targetButton = e.target.closest('button');
            if (!targetButton) return;
            const id = targetButton.dataset.id;
            this.closeModals();
            setTimeout(() => {
                if (targetButton.classList.contains('delete-request-btn')) this.handleDeleteRequest(id);
                if (targetButton.classList.contains('edit-request-btn')) this.handleEditRequest(id);
                if (targetButton.classList.contains('submit-offer-from-modal-btn')) this.showOfferModal(id);
            }, 300);
        }

        async loadUserProfile() {
            try {
                const response = await fetch(`${globtrade_data.api_url}users/me`, { method: 'GET', headers: { 'X-WP-Nonce': globtrade_data.nonce } });
                if (!response.ok) throw new Error('Could not fetch user data.');
                this.currentUser = await response.json();
                this.updateUI();
                this.populateProfileForm();
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
        }

        async handleProfileUpdate(e) {
            e.preventDefault();
            const submitButton = e.target.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            try {
                const response = await fetch(`${globtrade_data.api_url}users/me`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': globtrade_data.nonce },
                    body: JSON.stringify(data)
                });
                if (!response.ok) throw new Error('Failed to update profile.');
                this.showNotification('Profile updated successfully.', 'success');
                this.loadUserProfile();
            } catch (error) {
                this.showNotification(error.message, 'error');
            } finally {
                submitButton.disabled = false;
            }
        }

        handleNavigation(e) {
            const section = e.currentTarget.dataset.section;
            this.navItems.forEach(item => item.classList.remove('active'));
            e.currentTarget.classList.add('active');
            this.contentSections.forEach(content => content.classList.toggle('section-hidden', content.id !== `${section}-section`));
            if (section === 'my-requests-importer') this.loadMyRequests();
            if (section === 'importer-requests-for-exporter') this.loadAllRequests();
            if (section === 'my-submitted-offers-exporter') this.loadMySubmittedOffers();
            if (section === 'profile') this.loadUserProfile();
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
            container.innerHTML = `<div class="p-8 text-center text-gray-500">Loading...</div>`;
            try {
                const response = await fetch(`${globtrade_data.api_url}requests/me`, { headers: { 'X-WP-Nonce': globtrade_data.nonce } });
                if (!response.ok) throw new Error('Failed to fetch requests.');
                const requests = await response.json();
                this.renderMyRequestsList(requests);
            } catch (error) {
                console.error(error);
                container.innerHTML = `<div class="p-8 text-center text-red-500">Error loading requests.</div>`;
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
                const status = request.status === 'publish' ? 'Active' : request.status;
                const statusClass = status === 'Active' ? 'success' : 'warning';
                let detailsHtml = '';
                if (budget) detailsHtml += `<span>Budget: <strong class="text-gray-700">${budget} ${currency || ''}</strong></span>`;
                if (quantity) { if (detailsHtml) detailsHtml += `<span class="mx-2">|</span>`; detailsHtml += `<span>Quantity: <strong class="text-gray-700">${quantity} ${unit || ''}</strong></span>`; }
                return `
                <div class="p-4 border-b border-gray-200 last:border-b-0">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1"><h3 class="text-lg font-semibold text-primary">${request.title}</h3><div class="flex items-center text-sm text-gray-500 mt-1 flex-wrap">${detailsHtml}</div></div>
                        <div class="flex flex-col items-end gap-2"><span class="badge ${statusClass}">${status}</span><div class="flex items-center gap-2 mt-2"><button data-id="${request.id}" class="view-request-btn px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded hover:bg-blue-200">View</button><button data-id="${request.id}" class="edit-request-btn px-3 py-1 bg-gray-200 text-gray-700 text-xs rounded hover:bg-gray-300">Edit</button><button data-id="${request.id}" class="delete-request-btn px-3 py-1 bg-red-100 text-red-700 text-xs rounded hover:bg-red-200">Delete</button></div></div>
                    </div>
                </div>`;
            }).join('');
        }

        async loadAllRequests() {
            const container = document.getElementById('importer-requests-for-exporter-list');
            if (!container) return;
            container.innerHTML = `<div class="p-8 text-center text-gray-500">Loading...</div>`;
            try {
                const response = await fetch(`${globtrade_data.api_url}requests`, { headers: { 'X-WP-Nonce': globtrade_data.nonce } });
                if (!response.ok) throw new Error('Failed to fetch requests.');
                const requests = await response.json();
                this.renderAllRequestsList(requests);
            } catch (error) {
                console.error(error);
                container.innerHTML = `<div class="p-8 text-center text-red-500">Error loading requests.</div>`;
            }
        }

        renderAllRequestsList(requests) {
            const container = document.getElementById('importer-requests-for-exporter-list');
            if (!container) return;

            if (requests.length === 0) {
                container.innerHTML = `<div class="p-8 text-center text-gray-500">No importer requests available currently.</div>`;
                return;
            }

            container.innerHTML = requests.map(request => {
                const budget = request.meta['request-budget']?.[0];
                const currency = request.meta['request-currency']?.[0];
                const quantity = request.meta['request-quantity-value']?.[0];
                const unit = request.meta['request-quantity-unit']?.[0];
                const status = request.status === 'publish' ? 'Active' : request.status;
                const statusClass = status === 'Active' ? 'success' : 'warning';
                const importerName = request.importer?.company_name || 'N/A';
                const description = request.content ? request.content.substring(0, 100) + '...' : 'No description';

                let detailsHtml = '';
                if (importerName !== 'N/A') {
                    detailsHtml += `<span>By: <strong class="text-gray-700">${importerName}</strong></span>`;
                }
                if (quantity) {
                    if (detailsHtml) detailsHtml += `<span class="mx-2">|</span>`;
                    detailsHtml += `<span>Quantity: <strong class="text-gray-700">${quantity} ${unit || ''}</strong></span>`;
                }

                return `
                <div class="p-4 border-b border-gray-200 last:border-b-0">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-primary">${request.title}</h3>
                            <p class="text-sm text-gray-600 mt-1">${description}</p>
                            <div class="flex items-center text-sm text-gray-500 mt-2 flex-wrap">
                                ${detailsHtml}
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-2 text-right">
                            ${budget ? `<p class="text-lg font-bold text-primary">${budget} ${currency || ''}</p>` : ''}
                            <span class="badge ${statusClass}">${status}</span>
                            <button data-id="${request.id}" class="view-request-for-offer-btn px-4 py-2 bg-blue-500 text-white text-xs rounded-lg hover:bg-blue-600 transition-colors mt-1">
                                Details & Submit Offer
                            </button>
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
            
            // تهيئة حقول الاختيار في نموذج إضافة الطلب بخاصية الكتابة الحرة
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
                const response = await fetch(`${globtrade_data.api_url}requests/${requestId}`, { headers: { 'X-WP-Nonce': globtrade_data.nonce } });
                if (!response.ok) throw new Error('Could not fetch request details.');
                const request = await response.json();
                this.detailsModalTitle.textContent = `Request Details: ${request.title}`;
                const fields = [
                    { section: 'Product Details', icon: 'fa-box', data: [ { label: 'Required Product', value: request.title }, { label: 'Description', value: request.content }, { label: 'Category', value: request.meta['request-category']?.[0] }, { label: 'Required Quantity', value: `${request.meta['request-quantity-value']?.[0]} ${request.meta['request-quantity-unit']?.[0]}` }, { label: 'Required Specifications', value: request.meta['request-specs']?.[0] } ]},
                    { section: 'Budget & Payment', icon: 'fa-money-bill-wave', data: [ { label: 'Estimated Budget', value: `${request.meta['request-budget']?.[0]} ${request.meta['request-currency']?.[0]}` }, { label: 'Preferred Payment Method', value: request.meta['request-payment']?.[0] } ]},
                    { section: 'Shipping & Logistics', icon: 'fa-truck', data: [ { label: 'Preferred Shipping Method', value: request.meta['request-shipping']?.[0] }, { label: 'Required Port/Destination', value: request.meta['request-port']?.[0] } ]}
                ];
                let detailsHtml = '';
                fields.forEach(section => {
                    const sectionData = section.data.filter(field => field.value && !field.value.includes('N/A') && !field.value.includes('undefined'));
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

                if (viewContext === 'importer') {
                    this.detailsModalActions.innerHTML = `<button data-id="${requestId}" class="delete-request-btn px-4 py-2 bg-red-100 text-red-700 rounded-lg ..."><i class="fas fa-trash"></i> Delete</button><button data-id="${requestId}" class="edit-request-btn px-4 py-2 bg-blue-500 text-white ..."><i class="fas fa-edit"></i> Edit</button>`;
                } else if (viewContext === 'exporter') {
                    this.detailsModalActions.innerHTML = `<button data-id="${requestId}" class="submit-offer-from-modal-btn btn-primary"><i class="fas fa-tags mr-2"></i>Submit Offer</button>`;
                }
            } catch (error) {
                this.detailsModalContent.innerHTML = `<p class="text-red-500">${error.message}</p>`;
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

        showOfferModal(requestId) {
            if (!this.offerModal || !this.offerForm) return;
            this.offerForm.reset();
            this.offerForm.dataset.requestId = requestId;
            this.offerForm.removeAttribute('data-editing-id');
            this.offerModalTitle.textContent = 'Submit New Offer';
            this.offerForm.querySelector('button[type="submit"]').innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Submit Offer';
            const requestInfoDiv = document.getElementById('offer-modal-request-info');
            if(requestInfoDiv) requestInfoDiv.innerHTML = `<p class="font-semibold">Submitting offer on request ID: ${requestId}</p>`;
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
                const status = offer.status === 'publish' ? 'ACTIVE' : (offer.meta['status']?.[0] || 'ACTIVE').toUpperCase();
                let badgeClass = 'info';
                if (status === 'ACCEPTED') badgeClass = 'success';
                if (status === 'REJECTED') badgeClass = 'error';
                return `
                <div class="p-4 border-b border-gray-200 last:border-b-0">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-primary">Offer on Request: ${offer.request.title}</h3>
                            <p class="text-sm text-gray-600 mt-1">My Price: <strong class="text-gray-800">${price} ${currency}</strong> | Quantity: <strong class="text-gray-800">${quantity} ${unit}</strong></p>
                            <div class="flex items-center text-xs text-gray-500 mt-2">
                                <span>To: ${offer.importer.company_name}</span><span class="mx-2">|</span><span>Submission Date: ${offer.date}</span>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-2 text-right">
                            <span class="badge ${badgeClass}">${status}</span>
                            <button data-id="${offer.id}" class="edit-offer-btn px-4 py-2 bg-blue-500 text-white text-xs rounded-lg hover:bg-blue-600 transition-colors mt-1">Edit Offer</button>
                        </div>
                    </div>
                </div>`;
            }).join('');
        }
        
    async handleLogoUpload(file) {
    if (!file) {
        this.showNotification('Please select a file to upload.', 'error');
        return;
    }

    this.showNotification('Uploading logo...', 'info');

    const formData = new FormData();
    formData.append('profile_logo', file);

    try {
        const response = await fetch(`${globtrade_data.api_url}users/me/logo-upload`, {
            method: 'POST',
            headers: {
                'X-WP-Nonce': globtrade_data.nonce
            },
            body: formData
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ message: 'No JSON response for error.' }));
            throw new Error(errorData.message || 'Failed to upload logo.');
        }

        const result = await response.json();
        this.showNotification('Logo uploaded successfully!', 'success');
        
        if (result.logo_url) {
            this.profileLogoImg.src = result.logo_url;
            this.profileLogoImg.classList.remove('hidden');
            this.profileLogoIcon.classList.add('hidden');
            document.getElementById('user-profile-avatar').src = result.logo_url;
        }

        this.loadUserProfile();

    } catch (error) {
        this.showNotification(error.message, 'error');
        console.error('Logo upload error:', error);
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

}

const app = new GlobtradeApp();
app.init();
});