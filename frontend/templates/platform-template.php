<div id="notificationContainer"></div>

    <header id="main-header" class="glassmorphism fixed top-0 left-0 right-0 z-50 px-6 py-4 shadow-md">
        <div class="flex items-center justify-between container mx-auto">
            <div class="flex items-center space-x-4 rtl:space-x-reverse">
    <button id="sidebarToggle" class="lg:hidden text-2xl text-gray-700 hover:text-primary transition-colors">
        <i class="fas fa-bars"></i>
    </button>
    <div class="flex items-center space-x-3 rtl:space-x-reverse">
        <?php
            $logo_url = get_option( 'globtrade_logo_url' );
            $default_logo = GLOBTRADE_PLUGIN_URL . 'frontend/assets/images/logo.png';
            $logo_to_display = !empty( $logo_url ) ? esc_url( $logo_url ) : $default_logo;
        ?>
        <img src="<?php echo $logo_to_display; ?>" alt="Globtrade Logo" class="w-10 h-10 object-contain">
        <h1 class="text-2xl font-bold text-gray-800">
            GLOBTRADE
        </h1>
    </div>
    <span id="user-type-badge" class="px-3 py-1 bg-primary text-white rounded-full text-sm font-semibold"></span>
</div>

            <div class="flex items-center space-x-4 rtl:space-x-reverse">
                <div class="search-box hidden md:block">
                    <div class="relative">
                        <input type="text" id="header-search-input" placeholder="Search..."
                               class="w-64 px-4 py-2 pr-10 rounded-full border border-gray-300 focus-border-primary transition-all bg-gray-50">
                        <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <div id="header-search-results" class="search-results hidden">
                        </div>
                </div>

                <div class="relative">
                    <button id="notifications-btn" class="p-2 rounded-full text-gray-600 hover:text-primary transition-all">
                        <i class="fas fa-bell text-xl"></i>
                        <span id="notifications-count" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center hidden pulse">0</span>
                    </button>
                </div>

                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <img id="user-profile-avatar" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Ccircle cx='20' cy='20' r='20' fill='%23dc2626'/%3E%3Ctext x='20' y='26' text-anchor='middle' fill='white' font-size='16' font-weight='bold'%3EA%3C/text%3E%3C/svg%3E"  alt="Profile" class="w-10 h-10 rounded-full border-2 border-white shadow-sm object-cover">
                    <span id="user-name" class="font-medium text-gray-700 hidden md:block"></span>
                    <a id="logout-btn" href="<?php echo wp_logout_url( home_url('/auth') ); ?>" class="p-2 text-gray-600 hover:text-primary transition-colors">
    <i class="fas fa-sign-out-alt"></i>
</a>
                </div>
            </div>
        </div>
    </header>

    <div id="main-platform" class="">
        <div id="sidebar-overlay" class="sidebar-overlay hidden lg:hidden"></div>

        <div class="flex">
            <aside id="sidebar" class="sidebar w-80 min-h-screen p-6 relative lg:sticky top-0 right-0 z-40">
                <div class="text-center py-4 mb-6 hidden lg:block">
                    <h2 class="text-xl font-bold bg-gradient-to-r from-red-600 to-red-700 bg-clip-text text-transparent">Dashboard</h2>
                </div>
                <nav class="space-y-2">
                    <div class="nav-item" data-section="dashboard">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </div>

                    <?php
$user = wp_get_current_user();
if ( in_array( 'exporter', (array) $user->roles ) ) :
?>
<div id="sidebar-exporter-sections">
    <div class="nav-item" data-section="importer-requests-for-exporter">
        <i class="fas fa-shopping-basket mr-3"></i> Importer Requests
    </div>
    <div class="nav-item" data-section="my-submitted-offers-exporter">
        <i class="fas fa-tags mr-3"></i> My Submitted Offers
    </div>
</div>
<?php
elseif ( in_array( 'importer', (array) $user->roles ) ) :
?>
<div id="sidebar-importer-sections">
    <div class="nav-item" data-section="my-requests-importer">
        <i class="fas fa-shopping-cart mr-3"></i> My Requests
    </div>
    <div class="nav-item" data-section="offers-on-my-requests-importer">
        <i class="fas fa-boxes mr-3"></i> Offers on My Requests
    </div>
</div>
<?php
endif;
?>

                    <div class="nav-item" data-section="agreements">
                        <i class="fas fa-handshake mr-3"></i>
                        Agreements
                    </div>

                    <div class="nav-item" data-section="messages">
                        <i class="fas fa-envelope mr-3"></i>
                        Messages
                        <span id="messages-count" class="bg-red-500 text-white text-xs rounded-full px-2 py-1 float-right hidden"></span>
                    </div>
                    <div class="nav-item" data-section="users">
                        <i class="fas fa-users mr-3"></i>
                        Globtrade Community
                    </div>
                    <div class="nav-item" data-section="profile">
                        <i class="fas fa-user mr-3"></i>
                        Profile
                    </div>
                   <div class="nav-item" data-section="subscription">
    <i class="fas fa-gem mr-3"></i>
    My Subscription
</div>
                    <div class="nav-item" data-section="analytics">
                        <i class="fas fa-chart-line mr-3"></i>
                        Analytics
                    </div>
                    <div class="nav-item" data-section="complaints-suggestions">
                        <i class="fas fa-comment-alt mr-3"></i>
                        Complaints & Suggestions
                    </div>
                </nav>

                <div class="mt-8 p-4 bg-gray-50 rounded-xl">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Profile Completion</span>
                        <span id="profile-completion-percentage" class="text-sm text-primary font-bold">75%</span>
                    </div>
                    <div class="progress-bar">
                        <div id="profile-completion-fill" class="progress-fill" style="width: 75%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Complete your profile for more opportunities</p>
                </div>
            </aside>

            <main class="flex-1 p-6 lg:ml-[var(--sidebar-width)] pt-24 main-content">
                <div id="dashboard-section" class="section-content section-hidden">
                    <h2 class="text-3xl font-bold mb-6 text-primary-light">Dashboard</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="stat-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-red-100 text-sm">Total My Submitted Offers</h3>
                                    <p id="total-my-submitted-offers" class="text-3xl font-bold">0</p>
                                </div>
                                <i class="fas fa-tags text-4xl text-red-200"></i>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-red-100 text-sm">Total My Requests</h3>
                                    <p id="total-my-requests-dashboard" class="text-3xl font-bold">0</p>
                                </div>
                                <i class="fas fa-shopping-cart text-4xl text-red-200"></i>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-red-100 text-sm">New Messages</h3>
                                    <p id="total-new-messages" class="text-3xl font-bold">0</p>
                                </div>
                                <i class="fas fa-envelope text-4xl text-red-200"></i>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-red-100 text-sm">Registered Users</h3>
                                    <p id="total-registered-users" class="text-3xl font-bold">0</p>
                                </div>
                                <i class="fas fa-users text-4xl text-red-200"></i>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="card">
                            <h3 class="text-xl font-bold mb-4">Recent Submitted Offers</h3>
                            <div id="recent-my-submitted-offers-dashboard" class="space-y-3">
                                <div class="p-3 text-center text-gray-500">No items to display.</div>
                            </div>
                        </div>

                        <div class="card" id="recent-my-requests-dashboard-card">
                            <h3 class="text-xl font-bold mb-4">Recent Added Requests</h3>
                            <div id="recent-my-requests-dashboard" class="space-y-3">
                                <div class="p-3 text-center text-gray-500">No items to display.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="importer-requests-for-exporter-section" class="section-content section-hidden">
                    <h2 class="text-3xl font-bold mb-6 text-primary-light">Importer Requests Available for Offers</h2>
                    <div class="card overflow-hidden">
                        <div class="p-4 border-b border-gray-200">
                            <div class="flex space-x-4 mb-4">
                                <button id="filter-all-requests-exporter" class="px-4 py-2 bg-primary text-white rounded-lg font-medium">All Requests</button>
                                <button id="filter-my-activity-requests-exporter" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg font-medium hover:bg-gray-200 transition-all">Requests in My Business Activity</button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <input type="text" id="importer-requests-for-exporter-search" placeholder="Search requests..." class="w-full px-4 py-2 border rounded-lg focus-border-primary">
                                <select id="importer-requests-for-exporter-category-filter" class="w-full px-4 py-2 border rounded-lg focus-border-primary">
                                    <option value="">All Categories</option>
                                    <option value="Electronics">Electronics</option>
                                    <option value="Textiles">Textiles</option>
                                    <option value="Food">Food</option>
                                    <option value="Machinery">Machinery</option>
                                    <option value="Chemicals">Chemicals</option>
                                    <option value="Automotive">Automotive</option>
                                    <option value="Construction">Construction</option>
                                    <option value="Medical">Medical</option>
                                    <option value="Agriculture">Agriculture</option>
                                    <option value="Energy">Energy</option>
                                    <option value="Other">Other</option>
                                </select>
                                <select id="importer-requests-for-exporter-country-filter" class="w-full country-select">
                                    <option value="">Select a Country</option> </select>
                            </div>
                        </div>
                        <div id="importer-requests-for-exporter-list" class="divide-y divide-gray-200">
                             <div class="p-8 text-center text-gray-500">No importer requests available currently.</div>
                        </div>
                    </div>
                </div>

                <div id="my-submitted-offers-exporter-section" class="section-content section-hidden">
                    <h2 class="text-3xl font-bold mb-6 text-primary-light">My Submitted Offers on Importer Requests</h2>
                    <div class="card overflow-hidden">
                        <div class="p-4 border-b border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" id="my-submitted-offers-exporter-search" placeholder="Search my submitted offers..." class="w-full px-4 py-2 border rounded-lg focus-border-primary">
                                <select id="my-submitted-offers-exporter-filter" class="w-full px-4 py-2 border rounded-lg focus-border-primary">
                                    <option value="">All Statuses</option>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="accepted">Accepted</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                        <div id="my-submitted-offers-exporter-list" class="divide-y divide-gray-200">
                            <div class="p-8 text-center text-gray-500">You haven't submitted any offers yet.</div>
                        </div>
                    </div>
                </div>

                <div id="my-requests-importer-section" class="section-content section-hidden">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-3xl font-bold text-primary-light">My Requests</h2>
                        <button id="add-request-btn" class="btn-primary">
                            <i class="fas fa-plus mr-2"></i>
                            Add New Request
                        </button>
                    </div>
                    <div class="card overflow-hidden">
                        <div class="p-4 border-b border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" id="my-requests-importer-search" placeholder="Search my requests..." class="w-full px-4 py-2 border rounded-lg focus-border-primary">
                                <select id="my-requests-importer-filter" class="w-full px-4 py-2 border rounded-lg focus-border-primary">
                                    <option value="">All Statuses</option>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="draft">Draft</option>
                                    <option value="paused">Paused/On Hold</option>
                                </select>
                            </div>
                        </div>
                        <div id="my-requests-importer-list" class="divide-y divide-gray-200">
                            <div class="p-8 text-center text-gray-500">You haven't added any requests yet.</div>
                        </div>
                    </div>
                </div>

                <div id="offers-on-my-requests-importer-section" class="section-content section-hidden">
                    <h2 class="text-3xl font-bold mb-6 text-primary-light">Offers on My Requests</h2>
                    <div class="card overflow-hidden">
                        <div class="p-4 border-b border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" id="offers-on-my-requests-importer-search" placeholder="Search incoming offers..." class="w-full px-4 py-2 border rounded-lg focus-border-primary">
                                <select id="offers-on-my-requests-importer-filter" class="w-full px-4 py-2 border rounded-lg focus-border-primary">
                                    <option value="">All Statuses</option>
                                    <option value="active">Active (New Offers)</option>
                                    <option value="pending">Pending</option>
                                    <option value="accepted">Accepted</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                        <div id="offers-on-my-requests-importer-list" class="divide-y divide-gray-200">
                            <div class="p-8 text-center text-gray-500">You haven't received any offers on your requests yet.</div>
                        </div>
                    </div>
                </div>

                <div id="agreements-section" class="section-content section-hidden">
                    <h2 class="text-3xl font-bold mb-6 text-primary-light">Completed Agreements</h2>
                    <div class="card overflow-hidden">
                        <div id="agreements-list" class="divide-y divide-gray-200">
                            <div class="p-8 text-center text-gray-500">No completed agreements to display.</div>
                        </div>
                    </div>
                </div>

                <div id="messages-section" class="section-content section-hidden">
                    <h2 class="text-3xl font-bold mb-6 text-primary-light">Messages</h2>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[70vh]">
                        <div class="card overflow-y-auto">
                            <h3 class="text-xl font-bold mb-4 border-b pb-4 border-gray-200">Conversations</h3>
                            <div id="conversations-list" class="space-y-2">
                                <div class="p-4 text-center text-gray-500">No conversations.</div>
                            </div>
                        </div>

                        <div class="lg:col-span-2 card flex flex-col">
                            <div id="chat-header" class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200">
                                <h3 class="text-xl font-bold text-gray-800">Select a Conversation</h3>
                            </div>
                            <div id="chat-messages" class="flex-1 p-4 overflow-y-auto space-y-4 rounded-lg bg-gray-50">
                                <div class="p-4 text-center text-gray-500">Select a conversation from the list to start messaging.</div>
                            </div>
                            <div id="chat-input-area" class="p-4 border-t border-gray-200 hidden">
                                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                    <button id="attach-file-btn" class="p-3 text-gray-400 hover:text-primary rounded-full transition-colors" title="Attach File">
                                        <i class="fas fa-paperclip"></i>
                                    </button>
                                    <input type="file" id="file-input" class="hidden" accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx">
                                    <input type="text" id="message-input" placeholder="Type your message here..."
                                           class="flex-1 px-4 py-3 border rounded-lg focus-border-primary">
                                    <button id="share-profile-btn" class="p-3 text-gray-400 hover:text-primary rounded-full transition-colors" title="Share Your Profile">
                                        <i class="fas fa-user-circle"></i>
                                    </button>
                                    <button id="send-offer-in-chat-btn" class="p-3 text-gray-400 hover:text-primary rounded-full transition-colors hidden" title="Send/Update Offer">
                                        <i class="fas fa-tags"></i>
                                    </button>
                                    <button id="send-message-btn" class="p-3 btn-primary rounded-full">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="users-section" class="section-content section-hidden">
                    <h2 class="text-3xl font-bold mb-6 text-primary-light">Globtrade Community</h2>

                    <div id="community-content-wrapper">
                        <div class="mb-4 border-b border-gray-200">
                            <nav id="community-tabs" class="flex space-x-4 rtl:space-x-reverse">
                                <button data-tab="community-users" class="community-tab px-4 py-2 font-semibold text-gray-600 border-b-2 border-transparent hover:border-primary hover:text-primary transition-all active">
                                    Community Users
                                </button>
                                <button data-tab="marketing-offers" class="community-tab px-4 py-2 font-semibold text-gray-600 border-b-2 border-transparent hover:border-primary hover:text-primary transition-all">
                                    Marketing Offers
                                </button>
                            </nav>
                        </div>

                        <div id="community-users-content" class="community-tab-content">
                            <div class="card overflow-hidden">
                                <div class="p-4 border-b border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <input type="text" id="users-search" placeholder="Search by Company Name/Email/Country..." class="w-full px-4 py-2 border rounded-lg focus-border-primary">
                                        <select id="users-type-filter" class="w-full px-4 py-2 border rounded-lg focus-border-primary">
                                            <option value="">All Users</option>
                                            <option value="exporter">Exporters</option>
                                            <option value="importer">Importers</option>
                                        </select>
                                        <select id="users-country-filter" class="w-full country-select">
                                            <option value="">Select a Country</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="users-list" class="divide-y divide-gray-200">
                                    <div class="p-8 text-center text-gray-500">No other users.</div>
                                </div>
                            </div>
                        </div>

                        <div id="marketing-offers-content" class="community-tab-content section-hidden">
                        </div>
                    </div>

                    <div id="community-upgrade-message" class="section-hidden">
                        <div class="card text-center p-8">
                            <i class="fas fa-lock text-5xl text-primary mb-4"></i>
                            <h3 class="text-2xl font-bold mb-2">Exclusive Feature</h3>
                            <p class="text-gray-600 mb-6">Access to the Globtrade Community and Marketing Offers is an exclusive feature for top-tier members. Upgrade your package to connect with more partners and unlock new opportunities.</p>
                            <button id="community-upgrade-btn" class="btn-primary">
                                <i class="fas fa-rocket mr-2"></i>Upgrade to Package 3
                            </button>
                        </div>
                    </div>
                </div>

                <div id="profile-section" class="section-content section-hidden">
                    <h2 class="text-3xl font-bold mb-6 text-primary-light">Profile</h2>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2">
                            <div class="card mb-6">
                                <h3 class="text-xl font-bold mb-4">Company Information</h3>

                                <form id="profile-form" class="space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-gray-700 font-medium mb-2">Company Name</label>
                                            <input type="text" id="profile-company" name="company_name" class="w-full px-4 py-3 border rounded-lg focus-border-primary">
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 font-medium mb-2">Email</label>
                                            <input type="email" id="profile-email" name="email" class="w-full px-4 py-3 border rounded-lg focus-border-primary" readonly>
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 font-medium mb-2">Phone</label>
                                            <input type="tel" id="profile-phone" name="phone" class="w-full px-4 py-3 border rounded-lg focus-border-primary">
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 font-medium mb-2">Country</label>
                                            <select id="profile-country" name="country" class="w-full country-select">
                                                <option value="">Select a Country</option> </select>
                                        </div>
                                        <div id="profile-business-category-group">
                                            <label class="block text-gray-700 font-medium mb-2">Business Category</label>
                                            <select id="profile-business-category" name="business_category" class="w-full px-4 py-3 border rounded-lg focus-border-primary">
                                                <option value="">Select Category</option>
                                                <option value="Electronics">Electronics</option>
                                                <option value="Textiles">Textiles</option>
                                                <option value="Food">Food</option>
                                                <option value="Machinery">Machinery</option>
                                                <option value="Chemicals">Chemicals</option>
                                                <option value="Automotive">Automotive</option>
                                                <option value="Construction">Construction</option>
                                                <option value="Medical">Medical</option>
                                                <option value="Agriculture">Agriculture</option>
                                                <option value="Energy">Energy</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 font-medium mb-2">Commercial Registration No.</label>
                                            <input type="text" id="profile-registration" name="commercial_registration_no" class="w-full px-4 py-3 border rounded-lg focus-border-primary">
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 font-medium mb-2">Website</label>
                                            <input type="url" id="profile-website" name="website" class="w-full px-4 py-3 border rounded-lg focus-border-primary">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 font-medium mb-2">Address</label>
                                        <textarea id="profile-address" name="address" class="w-full px-4 py-3 border rounded-lg focus-border-primary" rows="3"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 font-medium mb-2">Company Description</label>
                                        <textarea id="profile-description" name="company_description" class="w-full px-4 py-3 border rounded-lg focus-border-primary" rows="4"></textarea>
                                    </div>

                                    <div class="modal-section-title"><i class="fas fa-share-alt text-primary"></i> Social Media</div>
                                    <div id="profile-socials-container" class="space-y-4">
                                        </div>
                                    <button type="button" id="add-social-link-btn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors mt-4">
                                        <i class="fas fa-plus mr-2"></i>Add Social Link
                                    </button>

                                    <div class="flex justify-end">
                                        <button type="submit" class="btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div>
                            <div class="card mb-6 text-center">
                                <div class="w-32 h-32 mx-auto bg-gradient-to-r from-red-600 to-red-700 rounded-full flex items-center justify-center mb-4 shadow-lg relative group">
                                    <img id="profile-logo-img" class="w-full h-full rounded-full object-cover hidden">
                                    <i id="profile-logo-icon" class="fas fa-building text-white text-4xl"></i>
                                </div>
                                <input type="file" id="logo-uploader" class="hidden" accept="image/*">

                                <h3 class="text-xl font-bold mb-2 text-primary-light" id="profile-company-display">Global Trade Company</h3>
                                <p class="text-gray-500 mb-4" id="profile-user-type-display">Certified Exporter</p>

                                <button id="change-logo-btn" class="w-full btn-primary mb-3">Change Photo/Logo</button>
                            </div>

                            <div class="card mb-6">
                                <h3 class="text-xl font-bold mb-4">Your Package & Usage</h3>
                                <div class="space-y-4 text-primary-light">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600">Current Package:</span>
                                        <span class="font-bold" id="profile-package-name">N/A</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600">Subscription Ends In:</span>
                                        <span class="font-bold" id="profile-package-days-remaining">N/A</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600">Remaining Credits:</span>
                                        <span class="font-bold" id="profile-package-credits">N/A</span>
                                    </div>
                                    <button id="profile-upgrade-package-btn" class="w-full mt-4 px-4 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all font-semibold">
                                        Manage My Package
                                    </button>
                                </div>
                            </div>

                            <div class="card">
                                <h3 class="text-xl font-bold mb-4">Verification Status</h3>
                                <div id="verification-status-container" class="space-y-3 text-primary-light">
                                </div>
                                <button id="complete-verification-btn" class="w-full mt-4 px-4 py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-all font-semibold">
                                    Complete Verification
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="subscription-section" class="section-content section-hidden">
    <h2 class="text-3xl font-bold mb-6 text-primary-light">My Subscription & Packages</h2>

    <div class="card mb-8">
        <h3 class="text-xl font-bold mb-4 border-b pb-4">Your Current Plan</h3>
        <div id="current-subscription-container" class="space-y-4">
            <p class="text-center text-gray-500">Loading your subscription details...</p>
        </div>
    </div>

    <div class="card">
        <h3 class="text-xl font-bold mb-4 border-b pb-4">Available Packages</h3>
        <div id="available-packages-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
             <p class="text-center text-gray-500 col-span-full">Loading available packages...</p>
        </div>
    </div>
</div>
                <div id="analytics-section" class="section-content section-hidden">
                    <h2 class="text-3xl font-bold mb-6 text-primary-light">Analytics Dashboard</h2>

                    <div class="card mb-6">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <h3 class="text-xl font-bold">Analysis Period</h3>
                            <div id="analytics-filter-container" class="flex space-x-2 rtl:space-x-reverse flex-wrap gap-2">
                                <button data-period="30d" class="analytics-filter-btn active">Last 30 Days</button>
                                <button data-period="90d" class="analytics-filter-btn">Last 90 Days</button>
                                <button data-period="1y" class="analytics-filter-btn">Last Year</button>
                                <button data-period="all" class="analytics-filter-btn">All Time</button>
                            </div>
                        </div>
                    </div>

                    <div id="analytics-exporter-content" class="hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <div class="card kpi-card text-center">
                                <div class="flex items-center justify-center space-x-4">
                                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-2xl shadow-md"><i class="fas fa-eye"></i></div>
                                    <div>
                                        <h3 class="text-3xl font-bold" id="exporter-offer-views">0</h3>
                                        <p class="text-gray-600">Offer Views</p>
                                    </div>
                                </div>
                                <canvas class="sparkline-canvas" id="exporter-views-sparkline"></canvas>
                            </div>
                            <div class="card kpi-card text-center">
                                <div class="flex items-center justify-center space-x-4">
                                    <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-2xl shadow-md"><i class="fas fa-handshake"></i></div>
                                    <div>
                                        <h3 class="text-3xl font-bold" id="exporter-completed-contracts">0</h3>
                                        <p class="text-gray-600">Completed Contracts</p>
                                    </div>
                                </div>
                                <canvas class="sparkline-canvas" id="exporter-contracts-sparkline"></canvas>
                            </div>
                            <div class="card kpi-card text-center">
                                <div class="flex items-center justify-center space-x-4">
                                    <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center text-2xl shadow-md"><i class="fas fa-dollar-sign"></i></div>
                                    <div>
                                        <h3 class="text-3xl font-bold" id="exporter-total-sales">$0</h3>
                                        <p class="text-gray-600">Total Sales</p>
                                    </div>
                                </div>
                                <canvas class="sparkline-canvas" id="exporter-sales-sparkline"></canvas>
                            </div>
                            <div class="card kpi-card text-center">
                                <div class="flex items-center justify-center space-x-4">
                                    <div class="w-16 h-16 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-2xl shadow-md"><i class="fas fa-percentage"></i></div>
                                    <div>
                                        <h3 class="text-3xl font-bold" id="exporter-offer-success-rate">0%</h3>
                                        <p class="text-gray-600">Offer Success Rate</p>
                                    </div>
                                </div>
                                <canvas class="sparkline-canvas" id="exporter-success-sparkline"></canvas>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                            <div class="card"><h3 class="text-xl font-bold mb-4">Monthly Revenue</h3><canvas id="revenueChart"></canvas></div>
                            <div class="card"><h3 class="text-xl font-bold mb-4">Offers Status Distribution</h3><canvas id="offersStatusChart"></canvas></div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                            <div class="lg:col-span-3 card"><h3 class="text-xl font-bold mb-4">Offer Performance by Category</h3><canvas id="exporterCategoryChart"></canvas></div>
                            <div class="lg:col-span-2 card"><h3 class="text-xl font-bold mb-4">Sales by Country</h3><canvas id="exporterGeoChart"></canvas></div>
                        </div>
                    </div>

                    <div id="analytics-importer-content" class="hidden">
                         <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <div class="card kpi-card text-center">
                                <div class="flex items-center justify-center space-x-4">
                                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-2xl shadow-md"><i class="fas fa-eye"></i></div>
                                    <div>
                                        <h3 class="text-3xl font-bold" id="importer-request-views">0</h3>
                                        <p class="text-gray-600">Request Views</p>
                                    </div>
                                </div>
                                <canvas class="sparkline-canvas" id="importer-views-sparkline"></canvas>
                            </div>
                            <div class="card kpi-card text-center">
                                <div class="flex items-center justify-center space-x-4">
                                    <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-2xl shadow-md"><i class="fas fa-hand-holding-usd"></i></div>
                                    <div>
                                        <h3 class="text-3xl font-bold" id="importer-offers-received">0</h3>
                                        <p class="text-gray-600">Offers Received</p>
                                    </div>
                                </div>
                                <canvas class="sparkline-canvas" id="importer-offers-sparkline"></canvas>
                            </div>
                            <div class="card kpi-card text-center">
                                <div class="flex items-center justify-center space-x-4">
                                    <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center text-2xl shadow-md"><i class="fas fa-check-double"></i></div>
                                    <div>
                                        <h3 class="text-3xl font-bold" id="importer-requests-fulfilled">0</h3>
                                        <p class="text-gray-600">Requests Fulfilled</p>
                                    </div>
                                </div>
                                <canvas class="sparkline-canvas" id="importer-fulfilled-sparkline"></canvas>
                            </div>
                            <div class="card kpi-card text-center">
                                <div class="flex items-center justify-center space-x-4">
                                    <div class="w-16 h-16 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-2xl shadow-md"><i class="fas fa-dollar-sign"></i></div>
                                    <div>
                                        <h3 class="text-3xl font-bold" id="importer-total-purchases">$0</h3>
                                        <p class="text-gray-600">Total Purchases</p>
                                    </div>
                                </div>
                                <canvas class="sparkline-canvas" id="importer-purchases-sparkline"></canvas>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                            <div class="card"><h3 class="text-xl font-bold mb-4">Monthly Spending</h3><canvas id="importerSpendingChart"></canvas></div>
                            <div class="card"><h3 class="text-xl font-bold mb-4">Request Status Distribution</h3><canvas id="requestStatusChart"></canvas></div>
                        </div>
                         <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                            <div class="lg:col-span-3 card"><h3 class="text-xl font-bold mb-4">Offers Received by Category</h3><canvas id="importerCategoryChart"></canvas></div>
                            <div class="lg:col-span-2 card"><h3 class="text-xl font-bold mb-4">Purchases by Country</h3><canvas id="importerGeoChart"></canvas></div>
                        </div>
                    </div>
                </div>

                <div id="complaints-suggestions-section" class="section-content section-hidden">
                    <h2 class="text-3xl font-bold mb-6 text-primary-light">Complaints & Suggestions</h2>
                    <div class="card">
                        <p class="text-gray-600 mb-6">We value your feedback! Please use the form below to submit any complaints or suggestions you may have.</p>
                        <form id="complaints-suggestions-form" class="space-y-6">
                            <div>
                                <label for="feedback-name" class="block text-gray-700 font-medium mb-2">Your Name</label>
                                <input type="text" id="feedback-name" class="w-full px-4 py-3 border rounded-lg focus-border-primary" required>
                            </div>
                            <div>
                                <label for="feedback-email" class="block text-gray-700 font-medium mb-2">Your Email</label>
                                <input type="email" id="feedback-email" class="w-full px-4 py-3 border rounded-lg focus-border-primary" required>
                            </div>
                            <div>
                                <label for="feedback-phone" class="block text-gray-700 font-medium mb-2">Your Phone (Optional)</label>
                                <input type="tel" id="feedback-phone" class="w-full px-4 py-3 border rounded-lg focus-border-primary">
                            </div>
                            <div>
                                <label for="feedback-type" class="block text-gray-700 font-medium mb-2">Type of Feedback</label>
                                <select id="feedback-type" class="w-full px-4 py-3 border rounded-lg focus-border-primary">
                                    <option value="suggestion">Suggestion</option>
                                    <option value="complaint">Complaint</option>
                                    <option value="bug">Bug Report</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label for="feedback-details" class="block text-gray-700 font-medium mb-2">Details</label>
                                <textarea id="feedback-details" rows="6" class="w-full px-4 py-3 border rounded-lg focus-border-primary" placeholder="Please provide detailed information..." required></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="btn-primary">Submit Feedback</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>

    <div class="floating-action" id="fabButton">
        <i class="fas fa-plus"></i>
    </div>

    <div class="modal" id="add-offer-modal">
        <div class="modal-content">
            <div class="flex items-center justify-between mb-6">
                <h2 id="offer-modal-title" class="text-2xl font-bold">Submit New Offer</h2>
                <button class="text-gray-400 hover:text-gray-600 text-2xl modal-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="offer-form" class="space-y-4">
    <div class="modal-section-title"><i class="fas fa-info-circle text-primary"></i> Request Information</div>
    <div id="offer-modal-request-info" class="bg-gray-100 p-3 rounded-lg text-sm text-gray-700 mb-4"></div>

    <div class="modal-section-title"><i class="fas fa-tags text-primary"></i> Your Offer Details</div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-gray-700 font-medium mb-2">Proposed Price</label>
            <input type="number" id="offer-price" name="offer-price" class="w-full px-4 py-3 border rounded-lg focus-border-primary" required>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">Proposed Currency</label>
            <select id="offer-currency" name="offer-currency" class="w-full px-4 py-3 border rounded-lg focus-border-primary">
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
                <option value="SAR">SAR</option>
                <option value="AED">AED</option>
            </select>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">Quantity You Can Provide</label>
            <input type="number" id="offer-quantity-value" name="offer-quantity-value" placeholder="e.g., 100" class="w-full px-4 py-3 border rounded-lg focus-border-primary" required>
        </div>
        <div>
            <label class="block text-gray-700 font-medium mb-2">Unit</label>
            <select id="offer-quantity-unit" name="offer-quantity-unit" class="w-full" required></select>
        </div>
    </div>

    <div>
        <label class="block text-gray-700 font-medium mb-2">Your Offer Specifications (Additional Details)</label>
        <textarea id="offer-specs" name="offer-specs" rows="3" placeholder="Detailed description of the product..." class="w-full px-4 py-3 border rounded-lg focus-border-primary"></textarea>
    </div>

    <div class="modal-section-title"><i class="fas fa-truck-moving text-primary"></i> Proposed Shipping & Payment</div>
    <div class="p-3 bg-gray-100 rounded-lg text-sm text-gray-600 mb-4">
        <p id="importer-preferences"></p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-gray-700 font-medium mb-2">Proposed Shipping Method</label>
            <select id="offer-shipping" name="offer-shipping" class="w-full px-4 py-3 border rounded-lg focus-border-primary"></select>
        </div>
        <div>
            <label class="block text-gray-700 font-medium mb-2">Proposed Payment Method</label>
            <select id="offer-payment" name="offer-payment" class="w-full px-4 py-3 border rounded-lg focus-border-primary"></select>
        </div>
    </div>

    <div class="flex items-center justify-end space-x-4 rtl:space-x-reverse pt-4">
        <button type="button" class="px-6 py-3 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors modal-close-btn">
            Cancel
        </button>
        <button type="submit" class="btn-primary">
            <i class="fas fa-paper-plane mr-2"></i>Submit Offer
        </button>
    </div>
</form>
        </div>
    </div>

    <div class="modal" id="add-request-modal">
        <div class="modal-content w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 id="request-modal-title" class="text-xl font-bold text-gray-800">Add New Purchase Request</h3>
                <button class="text-gray-500 hover:text-gray-700 modal-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="request-form" class="space-y-4">
    <div class="modal-section-title"><i class="fas fa-box text-primary"></i> Product & Details</div>
    <div>
        <label class="block text-gray-700 font-medium mb-2">Required Product Name</label>
        <input type="text" id="request-product" name="request-product" class="w-full px-4 py-3 border rounded-lg focus-border-primary" required>
    </div>
    <div>
        <label class="block text-gray-700 font-medium mb-2">Request Description</label>
        <textarea id="request-description" name="request-description" class="w-full px-4 py-3 border rounded-lg focus-border-primary" rows="3" required></textarea>
    </div>
    <div>
        <label class="block text-gray-700 font-medium mb-2">Trade Category</label>
        <select id="request-category" name="request-category" class="w-full px-4 py-3 border rounded-lg focus-border-primary" required>
            <option value="">Select a Category</option>
            <option value="Electronics">Electronics</option>
            <option value="Textiles">Textiles</option>
            <option value="Food">Food</option>
            <option value="Machinery">Machinery</option>
            <option value="Chemicals">Chemicals</option>
            <option value="Automotive">Automotive</option>
            <option value="Construction">Construction</option>
            <option value="Medical">Medical</option>
            <option value="Agriculture">Agriculture</option>
            <option value="Energy">Energy</option>
            <option value="Other">Other</option>
        </select>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-gray-700 font-medium mb-2">Required Quantity</label>
            <input type="number" id="request-quantity-value" name="request-quantity-value" placeholder="e.g., 100" class="w-full px-4 py-3 border rounded-lg focus-border-primary" required>
        </div>
        <div>
            <label class="block text-gray-700 font-medium mb-2">Unit</label>
            <select id="request-quantity-unit" name="request-quantity-unit" class="w-full" required>
                <option value="">Select a Unit</option>
                <option value="Piece">Piece</option>
                <option value="Ton">Ton</option>
                <option value="KG">KG</option>
                <option value="Box">Box</option>
                <option value="Carton">Carton</option>
                <option value="Container">Container</option>
                <option value="Set">Set</option>
            </select>
        </div>
    </div>
    <div>
        <label class="block text-gray-700 font-medium mb-2">Required Specifications (Optional)</label>
        <textarea id="request-specs" name="request-specs" class="w-full px-4 py-3 border rounded-lg focus-border-primary" rows="2" placeholder="e.g., color, size, material, certifications"></textarea>
    </div>

    <div class="modal-section-title"><i class="fas fa-money-bill-wave text-primary"></i> Budget & Payment</div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-gray-700 font-medium mb-2">Estimated Budget (Optional)</label>
            <input type="number" id="request-budget" name="request-budget" class="w-full px-4 py-3 border rounded-lg focus-border-primary">
        </div>
        <div>
            <label class="block text-gray-700 font-medium mb-2">Currency</label>
            <select id="request-currency" name="request-currency" class="w-full px-4 py-3 border rounded-lg focus-border-primary">
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
                <option value="SAR">SAR</option>
                <option value="AED">AED</option>
                <option value="PKR">PKR</option>
                <option value="PEN">PEN</option>
                <option value="GBP">GBP</option>
            </select>
        </div>
    </div>
    <div>
        <label class="block text-gray-700 font-medium mb-2">Preferred Payment Method (Optional)</label>
        <select id="request-payment" name="request-payment" class="w-full px-4 py-3 border rounded-lg focus-border-primary">
            <option value="">Select Method</option>
            <option value="Cash In Advance (CIA)">Cash In Advance (CIA)</option>
            <option value="Letter of Credit (LC)">Letter of Credit (LC)</option>
            <option value="Documents Against Payment (D/P)">Documents Against Payment (D/P)</option>
            <option value="Documents Against Acceptance (D/A)">Documents Against Acceptance (D/A)</option>
            <option value="Open Account (O/A)">Open Account (O/A)</option>
            <option value="Consignment (C/N)">Consignment (C/N)</option>
        </select>
    </div>

    <div class="modal-section-title"><i class="fas fa-truck text-primary"></i> Shipping & Logistics</div>
    <div>
        <label class="block text-gray-700 font-medium mb-2">Origin Country for Exporter (Optional)</label>
        <select id="request-country" name="request-country" class="w-full country-select">
            <option value="">Select a Country</option>
        </select>
    </div>
    <div>
        <label class="block text-gray-700 font-medium mb-2">Preferred Shipping Method (Optional)</label>
        <select id="request-shipping" name="request-shipping" class="w-full px-4 py-3 border rounded-lg focus-border-primary">
            <option value="">Select Incoterm</option>
            <option value="Ex Works (EXW)">Ex Works (EXW)</option>
            <option value="Free On Board (FOB)">Free On Board (FOB)</option>
            <option value="Cost and Freight (CFR)">Cost and Freight (CFR)</option>
            <option value="Cost, Insurance and Freight (CIF)">Cost, Insurance and Freight (CIF)</option>
            <option value="Delivered at Place (DAP)">Delivered at Place (DAP)</option>
            <option value="Delivered Duty Paid (DDP)">Delivered Duty Paid (DDP)</option>
        </select>
    </div>
    <div>
        <label class="block text-gray-700 font-medium mb-2">Required Port/Destination</label>
        <input type="text" id="request-port" name="request-port" class="w-full px-4 py-3 border rounded-lg focus-border-primary" required>
    </div>
    <div>
        <label class="block text-gray-700 font-medium mb-2">Customs Number (Optional)</label>
        <input type="text" id="request-customs" name="request-customs" class="w-full px-4 py-3 border rounded-lg focus-border-primary" placeholder="e.g., HS Code">
    </div>

    <div class="modal-section-title"><i class="fas fa-lightbulb text-primary"></i> Offer Type</div>
    <div>
        <label class="block text-gray-700 font-medium mb-2">Type of Offer Desired (Optional)</label>
        <select id="request-offer-type" name="request-offer-type" class="w-full px-4 py-3 border rounded-lg focus-border-primary">
            <option value="">Select Offer Type</option>
            <option value="Instant Purchase">Instant Purchase</option>
            <option value="Sustainable Purchase">Sustainable Purchase (Long-term)</option>
            <option value="Custom Purchase">Custom Purchase (Tailored)</option>
            <option value="Government Tender">Government Tender</option>
        </select>
    </div>


    <div class="flex justify-end space-x-2 rtl:space-x-reverse">
        <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 modal-close-btn">Cancel</button>
        <button type="submit" class="btn-primary">Add Request</button>
    </div>
</form>
        </div>
    </div>

    <div id="details-modal" class="modal hidden">
        <div class="modal-content w-full max-w-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 id="details-modal-title" class="text-xl font-bold text-gray-800">Details</h3>
                <button class="text-gray-500 hover:text-gray-700 text-2xl modal-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="details-modal-content" class="space-y-3 text-gray-700">
                </div>
            <div id="details-modal-actions" class="flex justify-end mt-6 space-x-2 rtl:space-x-reverse">
                </div>
        </div>
    </div>

    <div id="reject-offer-modal" class="modal hidden">
        <div class="modal-content w-full max-w-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Reject Offer</h3>
                <button class="text-gray-500 hover:text-gray-700 modal-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="reject-offer-form" class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Reason for Rejection</label>
                    <select id="rejection-reason" class="w-full px-4 py-3 border rounded-lg focus-border-primary" required>
                        <option value="">Select a reason</option>
                        <option value="price_unsuitable">Price is unsuitable</option>
                        <option value="shipping_terms">Shipping terms are not acceptable</option>
                        <option value="payment_terms">Payment method/terms are not suitable</option>
                        <option value="quantity_mismatch">Quantity offered does not match requirement</option>
                        <option value="quality_concerns">Concerns about product quality/specs</option>
                        <option value="delivery_time">Delivery time is too long</option>
                        <option value="other">Other (Please specify in chat)</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                    <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 modal-close-btn">Cancel</button>
                    <button type="submit" class="btn-primary bg-red-500 hover:bg-red-600">Reject Offer</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="upgrade-package-modal">
        <div class="modal-content max-w-4xl">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold">Upgrade Your Exporter Package</h2>
                <button class="text-gray-400 hover:text-gray-600 text-2xl modal-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                <div class="card p-6 border-2 border-gray-200 hover:border-primary transition-all">
                    <i class="fas fa-box text-4xl text-primary mx-auto mb-4"></i>
                    <h3 class="text-xl font-bold">Package 1</h3>
                    <p class="text-3xl font-bold my-2">$200<span class="text-lg font-normal">/year</span></p>
                    <p class="text-lg font-semibold text-gray-700">50 Offers Submitted</p>
                    <p class="text-gray-500 mt-2 h-12">Ideal for new exporters getting started.</p>
                    <button onclick="app.handlePackageUpgrade('exporter-package-1')" class="btn-primary mt-4 w-full">Select Package</button>
                </div>
                <div class="card p-6 border-2 border-primary shadow-lg transform scale-105">
                    <i class="fas fa-boxes text-4xl text-primary mx-auto mb-4"></i>
                    <h3 class="text-xl font-bold">Package 2</h3>
                    <p class="text-3xl font-bold my-2">$300<span class="text-lg font-normal">/year</span></p>
                    <p class="text-lg font-semibold text-gray-700">150 Offers Submitted</p>
                    <p class="text-gray-500 mt-2 h-12">Perfect for growing businesses expanding their reach.</p>
                    <button onclick="app.handlePackageUpgrade('exporter-package-2')" class="btn-primary mt-4 w-full">Select Package</button>
                </div>
                <div class="card p-6 border-2 border-gray-200 hover:border-primary transition-all">
                    <i class="fas fa-truck-loading text-4xl text-primary mx-auto mb-4"></i>
                    <h3 class="text-xl font-bold">Package 3</h3>
                    <p class="text-3xl font-bold my-2">$500<span class="text-lg font-normal">/year</span></p>
                    <p class="text-lg font-semibold text-gray-700">300 Offers Submitted</p>
                    <p class="text-gray-500 mt-2 h-12">For established exporters seeking maximum engagement.</p>
                    <button onclick="app.handlePackageUpgrade('exporter-package-3')" class="btn-primary mt-4 w-full">Select Package</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="create-marketing-offer-modal">
        <div class="modal-content">
            <div class="flex items-center justify-between mb-6">
                <h2 id="marketing-offer-modal-title" class="text-2xl font-bold">Create New Marketing Offer</h2>
                <button class="text-gray-400 hover:text-gray-600 text-2xl modal-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="marketing-offer-form" class="space-y-4">
                <div class="modal-section-title"><i class="fas fa-bullhorn text-primary"></i> Offer Details</div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Product / Service</label>
                    <input type="text" id="marketing-offer-product" class="w-full px-4 py-3 border rounded-lg focus-border-primary" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Description</label>
                    <textarea id="marketing-offer-description" class="w-full px-4 py-3 border rounded-lg focus-border-primary" rows="3" required></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Available Quantity</label>
                        <input type="number" id="marketing-offer-quantity-value" placeholder="e.g., 1000" class="w-full px-4 py-3 border rounded-lg focus-border-primary" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Unit</label>
                        <select id="marketing-offer-quantity-unit" class="w-full" required></select>
                    </div>
                </div>

                <div class="modal-section-title"><i class="fas fa-users text-primary"></i> Target Audience</div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Target Importer Business Categories</label>
                    <select id="marketing-offer-categories" class="w-full" multiple="multiple"></select>
                    <p class="text-xs text-gray-500 mt-1">Select one or more business categories to target.</p>
                </div>

                <div id="marketing-offer-audience-details" class="mt-4 p-3 bg-gray-50 rounded-lg hidden">
                    <p id="marketing-offer-importer-count" class="font-semibold text-gray-800 mb-2">Found 0 matching importers.</p>
                    <div id="marketing-offer-importer-list" class="space-y-2 max-h-40 overflow-y-auto border-t border-b py-2 my-2">
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <button type="button" id="marketing-offer-select-all-btn" class="text-sm text-blue-600 hover:underline">Select All</button>
                        <button type="button" id="marketing-offer-clear-all-btn" class="text-sm text-red-600 hover:underline">Clear Selection</button>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4 rtl:space-x-reverse pt-4">
                    <button type="button" class="px-6 py-3 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors modal-close-btn">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-paper-plane mr-2"></i>Send Offer
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div id="verification-modal" class="modal hidden">
    <div class="modal-content w-full max-w-lg">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Complete Account Verification</h3>
            <button class="text-gray-500 hover:text-gray-700 text-2xl modal-close-btn"><i class="fas fa-times"></i></button>
        </div>
        <div class="space-y-6">
            <div>
                <label class="block text-gray-700 font-medium"><i class="fas fa-file-contract mr-2"></i>Commercial Register</label>
                <div class="mt-2 flex items-center space-x-2">
                    <button onclick="document.getElementById('commercial-register-input').click()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Choose File</button>
                    <input type="file" id="commercial-register-input" class="hidden" accept=".pdf,.jpg,.jpeg,.png">
                    <span id="commercial-register-filename" class="text-sm text-gray-500 truncate w-32">No file chosen</span>
                </div>
                <div class="progress-bar-container mt-2 h-2 bg-gray-200 rounded-full overflow-hidden hidden">
                    <div id="commercial-register-progress" class="progress-bar-fill h-full bg-blue-500 rounded-full transition-all duration-300" style="width: 0%;"></div>
                </div>
            </div>
            <div>
                <label class="block text-gray-700 font-medium"><i class="fas fa-file-invoice-dollar mr-2"></i>Tax Card</label>
                <div class="mt-2 flex items-center space-x-2">
                    <button onclick="document.getElementById('tax-card-input').click()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Choose File</button>
                    <input type="file" id="tax-card-input" class="hidden" accept=".pdf,.jpg,.jpeg,.png">
                    <span id="tax-card-filename" class="text-sm text-gray-500 truncate w-32">No file chosen</span>
                </div>
                 <div class="progress-bar-container mt-2 h-2 bg-gray-200 rounded-full overflow-hidden hidden">
                    <div id="tax-card-progress" class="progress-bar-fill h-full bg-blue-500 rounded-full transition-all duration-300" style="width: 0%;"></div>
                </div>
            </div>
            <div>
                <label class="block text-gray-700 font-medium"><i class="fas fa-university mr-2"></i>IBAN Document</label>
                <div class="mt-2 flex items-center space-x-2">
                    <button onclick="document.getElementById('iban-doc-input').click()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Choose File</button>
                    <input type="file" id="iban-doc-input" class="hidden" accept=".pdf,.jpg,.jpeg,.png">
                    <span id="iban-doc-filename" class="text-sm text-gray-500 truncate w-32">No file chosen</span>
                </div>
                 <div class="progress-bar-container mt-2 h-2 bg-gray-200 rounded-full overflow-hidden hidden">
                    <div id="iban-doc-progress" class="progress-bar-fill h-full bg-blue-500 rounded-full transition-all duration-300" style="width: 0%;"></div>
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-8">
             <button id="save-verification-docs-btn" class="btn-primary">Submit for Review</button>
        </div>
    </div>
</div>