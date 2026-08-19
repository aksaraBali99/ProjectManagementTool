# ProjectManagementTool

## Google OAuth ("Sign in with Google") setup

Google login authenticates an **existing** user by matching their Google account's email to a `users.email` row — it never creates a new account. To enable it in any environment (local, dev, prod):

1. In [Google Cloud Console](https://console.cloud.google.com/apis/credentials), create an OAuth 2.0 Client ID (Web application type).
2. Add an **Authorized redirect URI** matching that environment's callback URL, e.g. `http://solva.test/auth/google/callback` locally, or `https://your-domain/auth/google/callback` in dev/prod.
3. Set these three variables in that environment's `.env` (never commit real values — see `.env.example` for the placeholders):
   - `GOOGLE_CLIENT_ID`
   - `GOOGLE_CLIENT_SECRET`
   - `GOOGLE_REDIRECT_URI` — must exactly match the redirect URI registered in step 2

Each environment needs its **own** Google Cloud Console client and its own registered redirect URI — a client configured for `solva.test` won't work against a production domain.

Without these set, the "Sign in with Google" button still renders and redirects to Google, but Google will reject the request (missing `client_id`) until real credentials are configured.
