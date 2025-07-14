document.addEventListener('DOMContentLoaded', function () {
    class AuthApp {
        constructor() {
            this.loginForm = document.getElementById('login-form');
            this.registerForm = document.getElementById('register-form');
            this.tradeCategories = [ 'Electronics', 'Textiles', 'Food', 'Machinery', 'Chemicals', 'Automotive', 'Construction', 'Medical', 'Agriculture', 'Energy', 'Other' ];
            this.init();
        }

        async init() {
            this.setupEventListeners();
            await this.populateCountries();
            this.populateCategories();
            this.updatePackages();
        }

        formatCountryOption(country) {
            if (!country.id) { return country.text; }
            const flagUrl = `https://flagcdn.com/w20/${country.id.toLowerCase()}.png`;
            return jQuery(`<span><img src="${flagUrl}" class="inline-block mr-2" /> ${country.text}</span>`);
        }

        async populateCountries() {
            try {
                const response = await fetch('https://flagcdn.com/en/codes.json');
                if (!response.ok) throw new Error('Network response was not ok.');
                const countries = await response.json();
                const countryOptions = Object.entries(countries).map(([code, name]) => ({
                    id: code.toUpperCase(),
                    text: name
                }));

                jQuery('#register-country').select2({
                    placeholder: 'Select Country...',
                    data: countryOptions,
                    templateResult: this.formatCountryOption,
                    templateSelection: this.formatCountryOption
                });
            } catch (error) {
                console.error('Failed to load countries:', error);
            }
        }

        populateCategories() {
    const categorySelect = document.getElementById('register-business-category');
    if (!categorySelect) return;
    
    categorySelect.innerHTML = '<option value=""></option>';
    this.tradeCategories.forEach(cat => {
        categorySelect.add(new Option(cat, cat));
    });

    jQuery(categorySelect).select2({
        placeholder: 'Select Business Category...',
        allowClear: true,
        tags: true // السماح بإضافة خيارات جديدة عن طريق الكتابة
    });
}

        updatePackages() {
            const accountTypeSelect = document.getElementById('register-type');
            const packageSelect = document.getElementById('register-package');
            if (!accountTypeSelect || !packageSelect) return;

            const selectedType = accountTypeSelect.value;
            const importerOption = packageSelect.querySelector('option[value="importer-package"]');
            const exporterOptions = packageSelect.querySelectorAll('option[value^="exporter-"]');

            if (selectedType === 'importer') {
                importerOption.style.display = 'block';
                exporterOptions.forEach(opt => { opt.style.display = 'none'; });
                packageSelect.value = 'importer-package';
            } else {
                importerOption.style.display = 'none';
                exporterOptions.forEach(opt => { opt.style.display = 'block'; });
                packageSelect.value = '';
            }
        }

        showNotification(message, type = 'error') {
            const container = document.getElementById('notificationContainer');
            if(!container) return;
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

        setupEventListeners() {
            document.getElementById('login-tab').addEventListener('click', (e) => this.switchTab(e));
            document.getElementById('register-tab').addEventListener('click', (e) => this.switchTab(e));
            document.getElementById('register-type').addEventListener('change', () => this.updatePackages());
            this.loginForm.addEventListener('submit', (e) => this.handleLogin(e));
            this.registerForm.addEventListener('submit', (e) => this.handleRegister(e));
        }

        switchTab(e) {
            e.preventDefault();
            const isLogin = e.currentTarget.id === 'login-tab';
            document.getElementById('login-tab').classList.toggle('active', isLogin);
            document.getElementById('register-tab').classList.toggle('active', !isLogin);
            document.getElementById('login-form').classList.toggle('section-hidden', !isLogin);
            document.getElementById('register-form').classList.toggle('section-hidden', isLogin);
        }

        async handleLogin(e) {
            e.preventDefault();
            const submitButton = this.loginForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner"></span> Logging in...';
            
            const data = {
                email: document.getElementById('login-email').value,
                password: document.getElementById('login-password').value,
            };

            try {
                const response = await fetch(globtrade_data.api_url + 'login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': globtrade_data.nonce },
                    body: JSON.stringify(data)
                });
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Login failed.');
                }
                window.location.href = globtrade_data.dashboard_url;
            } catch (error) {
                this.showNotification(error.message);
                submitButton.disabled = false;
                submitButton.innerHTML = 'Login';
            }
        }

        async handleRegister(e) {
            e.preventDefault();
            const submitButton = this.registerForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner"></span> Creating Account...';
            
            const countryData = jQuery('#register-country').select2('data')[0];

            const data = {
                company: document.getElementById('register-company').value,
                email: document.getElementById('register-email').value,
                password: document.getElementById('register-password').value,
                country: countryData ? countryData.text : '',
                country_code: countryData ? countryData.id : '',
                type: document.getElementById('register-type').value,
                category: document.getElementById('register-business-category').value,
                package: document.getElementById('register-package').value,
            };

            try {
                const response = await fetch(globtrade_data.api_url + 'register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': globtrade_data.nonce },
                    body: JSON.stringify(data)
                });
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Registration failed.');
                }
                window.location.href = globtrade_data.dashboard_url;
            } catch (error) {
                this.showNotification(error.message);
                submitButton.disabled = false;
                submitButton.innerHTML = 'Create Account';
            }
        }
    }
    new AuthApp();
});