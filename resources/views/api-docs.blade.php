<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Authentication API Documentation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Laravel Authentication API</h1>
            
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-2xl font-semibold mb-4">API Endpoints</h2>
                
                <div class="space-y-6">
                    <!-- Register -->
                    <div class="border-l-4 border-green-500 pl-4">
                        <h3 class="text-lg font-semibold text-green-700">POST /api/auth/register</h3>
                        <p class="text-gray-600 mb-2">Register a new user</p>
                        <div class="bg-gray-100 p-3 rounded">
                            <pre class="text-sm"><code>{
  "name": "John Doe",
  "email": "john@example.com", 
  "password": "password123",
  "password_confirmation": "password123"
}</code></pre>
                        </div>
                    </div>

                    <!-- Login -->
                    <div class="border-l-4 border-blue-500 pl-4">
                        <h3 class="text-lg font-semibold text-blue-700">POST /api/auth/login</h3>
                        <p class="text-gray-600 mb-2">Login user</p>
                        <div class="bg-gray-100 p-3 rounded">
                            <pre class="text-sm"><code>{
  "email": "john@example.com",
  "password": "password123"
}</code></pre>
                        </div>
                    </div>

                    <!-- Profile -->
                    <div class="border-l-4 border-purple-500 pl-4">
                        <h3 class="text-lg font-semibold text-purple-700">GET /api/auth/profile</h3>
                        <p class="text-gray-600 mb-2">Get user profile (requires authentication)</p>
                        <p class="text-sm text-gray-500">Headers: Authorization: Bearer {token}</p>
                    </div>

                    <!-- Logout -->
                    <div class="border-l-4 border-red-500 pl-4">
                        <h3 class="text-lg font-semibold text-red-700">POST /api/auth/logout</h3>
                        <p class="text-gray-600 mb-2">Logout user (requires authentication)</p>
                        <p class="text-sm text-gray-500">Headers: Authorization: Bearer {token}</p>
                    </div>

                    <!-- Email Verification -->
                    <div class="border-l-4 border-yellow-500 pl-4">
                        <h3 class="text-lg font-semibold text-yellow-700">Email Verification Endpoints</h3>
                        <div class="mt-2 space-y-2">
                            <p><strong>GET /api/email/verify/{id}/{hash}</strong> - Verify email</p>
                            <p><strong>POST /api/email/resend</strong> - Resend verification email</p>
                            <p><strong>GET /api/email/status</strong> - Check verification status</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Interface -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-semibold mb-4">API Test Interface</h2>
                
                <!-- Register Form -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Register New User</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" id="reg-name" placeholder="Name" class="border rounded px-3 py-2">
                        <input type="email" id="reg-email" placeholder="Email" class="border rounded px-3 py-2">
                        <input type="password" id="reg-password" placeholder="Password" class="border rounded px-3 py-2">
                        <input type="password" id="reg-password-confirm" placeholder="Confirm Password" class="border rounded px-3 py-2">
                    </div>
                    <button onclick="registerUser()" class="mt-3 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Register</button>
                </div>

                <!-- Login Form -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Login User</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="email" id="login-email" placeholder="Email" class="border rounded px-3 py-2">
                        <input type="password" id="login-password" placeholder="Password" class="border rounded px-3 py-2">
                    </div>
                    <button onclick="loginUser()" class="mt-3 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Login</button>
                </div>

                <!-- Results -->
                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-3">API Response</h3>
                    <div id="api-response" class="bg-gray-100 p-4 rounded min-h-[100px] font-mono text-sm"></div>
                </div>

                <!-- Token Storage -->
                <div class="mt-4">
                    <h4 class="font-semibold mb-2">Current Token:</h4>
                    <input type="text" id="current-token" placeholder="Token will appear here after login" class="w-full border rounded px-3 py-2" readonly>
                    <div class="mt-2">
                        <button onclick="getProfile()" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">Get Profile</button>
                        <button onclick="logoutUser()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 ml-2">Logout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_BASE = '/api';
        
        async function makeRequest(endpoint, method = 'GET', data = null, token = null) {
            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            };

            if (token) {
                options.headers['Authorization'] = `Bearer ${token}`;
            }

            if (data) {
                options.body = JSON.stringify(data);
            }

            try {
                const response = await fetch(API_BASE + endpoint, options);
                const result = await response.json();
                
                document.getElementById('api-response').textContent = JSON.stringify(result, null, 2);
                
                return { response, result };
            } catch (error) {
                document.getElementById('api-response').textContent = `Error: ${error.message}`;
                return { error };
            }
        }

        async function registerUser() {
            const data = {
                name: document.getElementById('reg-name').value,
                email: document.getElementById('reg-email').value,
                password: document.getElementById('reg-password').value,
                password_confirmation: document.getElementById('reg-password-confirm').value,
            };

            const { result } = await makeRequest('/auth/register', 'POST', data);
            
            if (result && result.success && result.token) {
                document.getElementById('current-token').value = result.token;
            }
        }

        async function loginUser() {
            const data = {
                email: document.getElementById('login-email').value,
                password: document.getElementById('login-password').value,
            };

            const { result } = await makeRequest('/auth/login', 'POST', data);
            
            if (result && result.success && result.token) {
                document.getElementById('current-token').value = result.token;
            }
        }

        async function getProfile() {
            const token = document.getElementById('current-token').value;
            if (!token) {
                alert('Please login first to get a token');
                return;
            }
            
            await makeRequest('/auth/profile', 'GET', null, token);
        }

        async function logoutUser() {
            const token = document.getElementById('current-token').value;
            if (!token) {
                alert('Please login first to get a token');
                return;
            }
            
            const { result } = await makeRequest('/auth/logout', 'POST', null, token);
            
            if (result && result.success) {
                document.getElementById('current-token').value = '';
            }
        }
    </script>
</body>
</html>