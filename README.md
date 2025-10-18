# Laravel Authentication API with Email Verification

A complete Laravel backend API for user registration, login authentication, and email verification using Resend.

## Features

- ✅ User Registration
- ✅ User Login/Logout  
- ✅ JWT Authentication with Laravel Sanctum
- ✅ Email Verification with Resend
- ✅ API Token Management
- ✅ Protected Routes
- ✅ Comprehensive API Documentation

## Requirements

- PHP 8.1+
- Composer
- Laravel 12.x
- SQLite (or your preferred database)

## Installation

1. **Clone and install dependencies:**
   ```bash
   composer install
   ```

2. **Environment Setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup:**
   ```bash
   php artisan migrate
   ```

4. **Configure Resend:**
   - Get your API key from [resend.com](https://resend.com)
   - Update `.env` file:
   ```env
   MAIL_MAILER=resend
   MAIL_FROM_ADDRESS="noreply@yourdomain.com"
   RESEND_KEY=your_resend_api_key_here
   ```

5. **Start the server:**
   ```bash
   php artisan serve
   ```

## API Endpoints

### Authentication

#### Register User
```http
POST /api/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "User registered successfully. Please verify your email.",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  },
  "token": "1|abcd1234...",
  "email_verified": false
}
```

#### Login User
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "email_verified_at": null
  },
  "token": "2|efgh5678...",
  "email_verified": false
}
```

#### Get Profile
```http
GET /api/auth/profile
Authorization: Bearer {token}
```

#### Logout
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

### Email Verification

#### Verify Email (via link)
```http
GET /api/email/verify/{id}/{hash}
```

#### Resend Verification Email
```http
POST /api/email/resend
Authorization: Bearer {token}
```

#### Check Verification Status
```http
GET /api/email/status
Authorization: Bearer {token}
```

## Testing the API

### Using the Web Interface
Visit `http://localhost:8000/api-docs` for an interactive testing interface.

### Using cURL

**Register a user:**
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123", 
    "password_confirmation": "password123"
  }'
```

**Login:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

**Get Profile:**
```bash
curl -X GET http://localhost:8000/api/auth/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

## Email Configuration

### Resend Setup

1. Sign up at [resend.com](https://resend.com)
2. Create a new API key
3. Add your domain (for production)
4. Update your `.env` file with:
   ```env
   RESEND_KEY=re_your_api_key_here
   MAIL_FROM_ADDRESS="noreply@yourdomain.com"
   ```

### Email Templates

The system uses Laravel's built-in email verification system with a custom notification that works with Resend. Email verification links are automatically generated and sent to users upon registration.

## Security Features

- Password hashing with bcrypt
- API token authentication via Sanctum
- Email verification required
- CSRF protection
- Rate limiting (can be configured)
- Signed email verification URLs with expiration

## Database Schema

### Users Table
- `id` - Primary key
- `name` - User's full name
- `email` - Email address (unique)
- `email_verified_at` - Timestamp of email verification
- `password` - Hashed password
- `remember_token` - For "remember me" functionality
- `created_at` / `updated_at` - Timestamps

### Personal Access Tokens Table
- Managed by Laravel Sanctum
- Stores API tokens for users

## Error Handling

The API returns consistent JSON responses for all endpoints:

**Success Response:**
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {}
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

## Production Deployment

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Configure your production database
4. Set up proper domain for Resend
5. Configure CORS if needed for frontend
6. Set up proper caching and optimization:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).