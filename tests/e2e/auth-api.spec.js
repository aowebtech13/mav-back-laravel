import { test, expect } from '@playwright/test';

const API_BASE = 'http://127.0.0.1:8000/api';

// Test data
const testUser = {
  name: 'Test User',
  email: `test${Date.now()}@example.com`,
  password: 'password123',
  password_confirmation: 'password123'
};

let authToken = '';

test.describe('Laravel Authentication API', () => {
  
  test('should register a new user successfully', async ({ request }) => {
    const response = await request.post(`${API_BASE}/auth/register`, {
      data: testUser,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    });

    expect(response.status()).toBe(201);
    
    const responseBody = await response.json();
    
    expect(responseBody).toHaveProperty('success', true);
    expect(responseBody).toHaveProperty('message', 'User registered successfully. Please verify your email.');
    expect(responseBody).toHaveProperty('user');
    expect(responseBody).toHaveProperty('token');
    expect(responseBody).toHaveProperty('email_verified', false);
    
    expect(responseBody.user).toHaveProperty('name', testUser.name);
    expect(responseBody.user).toHaveProperty('email', testUser.email);
    expect(responseBody.user).toHaveProperty('id');
    expect(responseBody.user).toHaveProperty('created_at');
    expect(responseBody.user).toHaveProperty('updated_at');
    
    expect(responseBody.token).toBeTruthy();
    expect(typeof responseBody.token).toBe('string');
    
    // Store token for subsequent tests
    authToken = responseBody.token;
  });

  test('should reject registration with invalid data', async ({ request }) => {
    const invalidUser = {
      name: '',
      email: 'invalid-email',
      password: '123', // Too short
      password_confirmation: 'different'
    };

    const response = await request.post(`${API_BASE}/auth/register`, {
      data: invalidUser,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    });

    expect(response.status()).toBe(422);
    
    const responseBody = await response.json();
    
    expect(responseBody).toHaveProperty('success', false);
    expect(responseBody).toHaveProperty('message', 'Validation failed');
    expect(responseBody).toHaveProperty('errors');
    
    expect(responseBody.errors).toHaveProperty('name');
    expect(responseBody.errors).toHaveProperty('email');
    expect(responseBody.errors).toHaveProperty('password');
  });

  test('should reject duplicate email registration', async ({ request }) => {
    // Try to register with the same email again
    const response = await request.post(`${API_BASE}/auth/register`, {
      data: testUser,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    });

    expect(response.status()).toBe(422);
    
    const responseBody = await response.json();
    
    expect(responseBody).toHaveProperty('success', false);
    expect(responseBody).toHaveProperty('errors');
    expect(responseBody.errors).toHaveProperty('email');
  });

  test('should login user with valid credentials', async ({ request }) => {
    const loginData = {
      email: testUser.email,
      password: testUser.password
    };

    const response = await request.post(`${API_BASE}/auth/login`, {
      data: loginData,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    });

    expect(response.status()).toBe(200);
    
    const responseBody = await response.json();
    
    expect(responseBody).toHaveProperty('success', true);
    expect(responseBody).toHaveProperty('message', 'Login successful');
    expect(responseBody).toHaveProperty('user');
    expect(responseBody).toHaveProperty('token');
    expect(responseBody).toHaveProperty('email_verified', false);
    
    expect(responseBody.user).toHaveProperty('name', testUser.name);
    expect(responseBody.user).toHaveProperty('email', testUser.email);
    expect(responseBody.user).toHaveProperty('email_verified_at', null);
    
    expect(responseBody.token).toBeTruthy();
    expect(typeof responseBody.token).toBe('string');
    
    // Update token for subsequent tests
    authToken = responseBody.token;
  });

  test('should reject login with invalid credentials', async ({ request }) => {
    const invalidLogin = {
      email: testUser.email,
      password: 'wrongpassword'
    };

    const response = await request.post(`${API_BASE}/auth/login`, {
      data: invalidLogin,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    });

    expect(response.status()).toBe(422);
    
    const responseBody = await response.json();
    
    expect(responseBody).toHaveProperty('message');
    expect(responseBody).toHaveProperty('errors');
  });

  test('should get user profile with valid token', async ({ request }) => {
    const response = await request.get(`${API_BASE}/auth/profile`, {
      headers: {
        'Authorization': `Bearer ${authToken}`,
        'Accept': 'application/json'
      }
    });

    expect(response.status()).toBe(200);
    
    const responseBody = await response.json();
    
    expect(responseBody).toHaveProperty('success', true);
    expect(responseBody).toHaveProperty('user');
    expect(responseBody).toHaveProperty('email_verified', false);
    
    expect(responseBody.user).toHaveProperty('name', testUser.name);
    expect(responseBody.user).toHaveProperty('email', testUser.email);
  });

  test('should reject profile access without token', async ({ request }) => {
    const response = await request.get(`${API_BASE}/auth/profile`, {
      headers: {
        'Accept': 'application/json'
      }
    });

    expect(response.status()).toBe(401);
  });

  test('should reject profile access with invalid token', async ({ request }) => {
    const response = await request.get(`${API_BASE}/auth/profile`, {
      headers: {
        'Authorization': 'Bearer invalid_token',
        'Accept': 'application/json'
      }
    });

    expect(response.status()).toBe(401);
  });

  test('should get email verification status', async ({ request }) => {
    const response = await request.get(`${API_BASE}/email/status`, {
      headers: {
        'Authorization': `Bearer ${authToken}`,
        'Accept': 'application/json'
      }
    });

    expect(response.status()).toBe(200);
    
    const responseBody = await response.json();
    
    expect(responseBody).toHaveProperty('success', true);
    expect(responseBody).toHaveProperty('email_verified', false);
  });

  test('should resend email verification', async ({ request }) => {
    const response = await request.post(`${API_BASE}/email/resend`, {
      headers: {
        'Authorization': `Bearer ${authToken}`,
        'Accept': 'application/json'
      }
    });

    expect(response.status()).toBe(200);
    
    const responseBody = await response.json();
    
    expect(responseBody).toHaveProperty('success', true);
    expect(responseBody).toHaveProperty('message', 'Verification email sent');
  });

  test('should logout user successfully', async ({ request }) => {
    const response = await request.post(`${API_BASE}/auth/logout`, {
      headers: {
        'Authorization': `Bearer ${authToken}`,
        'Accept': 'application/json'
      }
    });

    expect(response.status()).toBe(200);
    
    const responseBody = await response.json();
    
    expect(responseBody).toHaveProperty('success', true);
    expect(responseBody).toHaveProperty('message', 'Logged out successfully');
  });

  test('should reject access after logout', async ({ request }) => {
    // Try to access profile after logout
    const response = await request.get(`${API_BASE}/auth/profile`, {
      headers: {
        'Authorization': `Bearer ${authToken}`,
        'Accept': 'application/json'
      }
    });

    expect(response.status()).toBe(401);
  });

  test('should reject logout with invalid token', async ({ request }) => {
    const response = await request.post(`${API_BASE}/auth/logout`, {
      headers: {
        'Authorization': 'Bearer invalid_token',
        'Accept': 'application/json'
      }
    });

    expect(response.status()).toBe(401);
  });
});

test.describe('API Documentation and Info', () => {
  
  test('should serve API info endpoint', async ({ request }) => {
    const response = await request.get('/api');
    
    expect(response.status()).toBe(200);
    
    const responseBody = await response.json();
    
    expect(responseBody).toHaveProperty('message', 'Laravel Authentication API');
    expect(responseBody).toHaveProperty('version');
    expect(responseBody).toHaveProperty('documentation');
    expect(responseBody).toHaveProperty('endpoints');
    
    expect(responseBody.endpoints).toHaveProperty('register');
    expect(responseBody.endpoints).toHaveProperty('login');
    expect(responseBody.endpoints).toHaveProperty('logout');
    expect(responseBody.endpoints).toHaveProperty('profile');
  });

  test('should serve API documentation page', async ({ page }) => {
    await page.goto('/api-docs');
    
    await expect(page).toHaveTitle(/Laravel Authentication API Documentation/);
    
    // Check for main headings
    await expect(page.locator('h1')).toContainText('Laravel Authentication API');
    await expect(page.locator('h2')).toContainText('API Endpoints');
    
    // Check for endpoint documentation
    await expect(page.locator('text=POST /api/auth/register')).toBeVisible();
    await expect(page.locator('text=POST /api/auth/login')).toBeVisible();
    await expect(page.locator('text=GET /api/auth/profile')).toBeVisible();
    await expect(page.locator('text=POST /api/auth/logout')).toBeVisible();
    
    // Check for test interface
    await expect(page.locator('h2:has-text("API Test Interface")')).toBeVisible();
    await expect(page.locator('button:has-text("Register")')).toBeVisible();
    await expect(page.locator('button:has-text("Login")')).toBeVisible();
  });
});