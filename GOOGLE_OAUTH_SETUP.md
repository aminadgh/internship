# Google OAuth Setup for SwiftNotify

## 🚀 **What's Been Added:**

✅ **Laravel Socialite** - Google OAuth package installed  
✅ **Google OAuth Controller** - Handles authentication flow  
✅ **Database Migration** - Added `google_id` field to users table  
✅ **Routes** - Google OAuth endpoints configured  
✅ **UI Components** - Google Sign-In buttons on Login & Register pages  
✅ **User Management** - Automatic user creation and role assignment  

## 🔧 **Setup Required:**

### Step 1: Create Google OAuth App
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Enable **Google+ API** and **Google OAuth2 API**
4. Go to **Credentials** → **Create Credentials** → **OAuth 2.0 Client IDs**
5. Choose **Web application**
6. Add these **Authorized redirect URIs**:
   - `http://127.0.0.1:8000/auth/google/callback` (for local development)
   - `https://yourdomain.com/auth/google/callback` (for production)

### Step 2: Get OAuth Credentials
After creating the OAuth app, you'll get:
- **Client ID** (looks like: `123456789-abcdef.apps.googleusercontent.com`)
- **Client Secret** (looks like: `GOCSPX-abcdefghijklmnop`)

### Step 3: Update Environment Variables
Add these to your `.env` file:

```env
# Google OAuth
GOOGLE_CLIENT_ID=your-client-id-here
GOOGLE_CLIENT_SECRET=your-client-secret-here
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

### Step 4: Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
```

## 🎯 **How It Works:**

1. **User clicks "Continue with Google"** on Login/Register page
2. **Redirects to Google** for authentication
3. **Google returns user data** (name, email, Google ID)
4. **System checks** if user exists:
   - **If exists**: Logs them in
   - **If new**: Creates account with default "agent" role
5. **User is redirected** to dashboard

## 🔒 **Security Features:**

- ✅ **Google ID tracking** - Prevents duplicate accounts
- ✅ **Email verification** - Google accounts are pre-verified
- ✅ **Role assignment** - New users get "agent" role by default
- ✅ **Secure tokens** - Uses Laravel's built-in security

## 🎨 **UI Features:**

- **Professional Google button** with official Google colors
- **Responsive design** - Works on all devices
- **Dark mode support** - Matches your app's theme
- **Hover effects** - Smooth animations and transitions

## 🧪 **Testing:**

1. **Set up Google OAuth** (follow steps above)
2. **Click "Continue with Google"** on login/register page
3. **Complete Google authentication**
4. **Check dashboard access** and user creation

## 🚨 **Troubleshooting:**

### Common Issues:
- **"Invalid redirect URI"** - Check your Google OAuth app settings
- **"Client not found"** - Verify GOOGLE_CLIENT_ID in .env
- **"Invalid client secret"** - Verify GOOGLE_CLIENT_SECRET in .env

### Debug Commands:
```bash
php artisan config:clear
php artisan route:list | grep google
php artisan migrate:status
```

## 🌟 **Next Steps:**

Once Google OAuth is working, you can:
- **Add more providers** (Facebook, GitHub, LinkedIn)
- **Customize user roles** based on OAuth provider
- **Add profile picture** from Google account
- **Implement social sharing** features

---

**Need help?** Check the Laravel logs or let me know what error you're seeing!



