# Gmail SMTP Setup for Password Reset Emails

## Step 1: Enable 2-Factor Authentication
1. Go to your Google Account settings: https://myaccount.google.com/
2. Click on "Security"
3. Enable "2-Step Verification" if not already enabled

## Step 2: Generate App Password
1. Go to Google Account settings: https://myaccount.google.com/
2. Click on "Security"
3. Under "2-Step Verification", click "App passwords"
4. Select "Mail" and "Other (Custom name)"
5. Enter a name like "SwiftNotify Laravel"
6. Click "Generate"
7. **Copy the 16-character password** (it will look like: xxxx xxxx xxxx xxxx)

## Step 3: Update .env File
Update these lines in your `.env` file:

```env
MAIL_USERNAME=your.email@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx
```

**Important:** 
- Use your full Gmail address for `MAIL_USERNAME`
- Use the App Password (not your regular Gmail password) for `MAIL_PASSWORD`
- Remove the spaces from the App Password

## Step 4: Test
1. Clear Laravel config cache: `php artisan config:clear`
2. Try the "Forgot Password" feature again
3. Check your email for the password reset link

## Alternative: Use Your Own Email Service
If you prefer to use a different email service (SendGrid, Mailgun, etc.), let me know and I can configure that instead.
