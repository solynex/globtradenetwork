<div id="notificationContainer"></div>
<div id="auth-section" class="min-h-screen flex items-center justify-center p-4">
    <div class="card w-full max-w-md">
        <div class="text-center mb-8">
            <?php
    $logo_url = get_option( 'globtrade_logo_url' );
    $default_logo = GLOBTRADE_PLUGIN_URL . 'frontend/assets/images/logo.png';
    $logo_to_display = !empty( $logo_url ) ? esc_url( $logo_url ) : $default_logo;
?>
<img src="<?php echo $logo_to_display; ?>" alt="Globtrade Logo" class="w-20 h-20 mx-auto mb-4 object-contain">
            <h1 class="text-4xl font-bold text-gray-800">GLOBTRADE</h1>
            <p class="text-gray-500 font-semibold tracking-wider">GLOBAL DEALS MAKERS</p>
        </div>
        
        <div class="flex mb-6 rounded-xl overflow-hidden shadow-sm">
            <button id="login-tab" class="flex-1 py-3 px-4 tab-button active">Login</button>
            <button id="register-tab" class="flex-1 py-3 px-4 tab-button">Register</button>
        </div>
        
        <form id="login-form" class="space-y-6">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Email</label>
                <input type="email" id="login-email" required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2">Password</label>
                <input type="password" id="login-password" required>
            </div>
            <button type="submit" class="w-full btn-primary py-3 text-lg">Login</button>
        </form>
        
        <form id="register-form" class="space-y-5 section-hidden">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Company Name</label>
                <input type="text" id="register-company" required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2">Email</label>
                <input type="email" id="register-email" required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2">Password</label>
                <input type="password" id="register-password" required>
            </div>
             <div>
                <label class="block text-gray-700 font-medium mb-2">Country</label>
                <select id="register-country" required></select>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2">Account Type</label>
                <select id="register-type">
                    <option value="exporter">Exporter</option>
                    <option value="importer">Importer</option>
                </select>
            </div>
            <div id="register-business-category-group">
                <label class="block text-gray-700 font-medium mb-2">Business Category</label>
                <select id="register-business-category" required></select>
            </div>
            <div id="register-package-group">
                <label class="block text-gray-700 font-medium mb-2">Choose your Package</label>
                <select id="register-package" required>
                    <option value="">Select an Exporter Package</option>
                    <option value="exporter-package-1">Package 1 ($200/year)</option>
                    <option value="exporter-package-2">Package 2 ($300/year)</option>
                    <option value="exporter-package-3">Package 3 ($500/year)</option>
                    <option value="importer-package">Importer Package (Free for first year)</option>
                </select>
            </div>
            <div class="flex items-center pt-2">
                <input type="checkbox" id="register-terms" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded" required>
                <label for="register-terms" class="ml-3 block text-sm text-gray-700">I agree to the <a href="#" class="font-semibold text-primary hover:underline">Terms and Privacy Policy</a>.</label>
            </div>
            <button type="submit" class="w-full btn-primary py-3 text-lg mt-6">Create Account</button>
        </form>
    </div>
</div>