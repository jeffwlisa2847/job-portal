<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Validator.php';
require_once ROOT_PATH . '/app/models/User.php';
require_once ROOT_PATH . '/app/helpers/Mailer.php';

class AuthController extends Controller
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    // ── Show register page ────────────────────────────────────────────────────
    public function showRegister(): void
    {
        $this->view('auth/register', ['title' => 'Create Account']);
    }

    // ── Handle registration ───────────────────────────────────────────────────
    public function register(): void
    {
        $this->verifyCsrf();

        $v = Validator::make($_POST, [
            'full_name' => 'required|min:2|max:120',
            'email'     => 'required|email|max:180',
            'password'  => 'required|min:8|max:72',
            'password_confirm' => 'required|confirmed:password',
            'role'      => 'required|in:seeker,employer',
        ], [
            'password_confirm.confirmed' => 'Passwords do not match.',
        ]);

        // Employer needs a company name
        if (($this->input('role')) === 'employer' && trim($this->input('company_name')) === '') {
            $this->flash('error', 'Company name is required for employer accounts.');
            $this->saveOldInput();
            $this->back();
            return;
        }

        if ($v->fails()) {
            $this->flash('error', $v->first());
            $this->saveOldInput();
            $this->back();
            return;
        }

        if ($this->users->emailExists($this->input('email'))) {
            $this->flash('error', 'An account with this email address already exists.');
            $this->saveOldInput();
            $this->back();
            return;
        }

        try {
            $userId = $this->users->register([
                'full_name'    => $this->input('full_name'),
                'email'        => $this->input('email'),
                'password'     => $_POST['password'],
                'role'         => $this->input('role'),
                'company_name' => $this->input('company_name'),
            ]);

            // Send verification email (silently fails if mail not configured)
            $token = $this->users->getVerifyToken($userId);
            if ($token) {
                Mailer::sendVerification(
                    $this->input('email'),
                    $this->input('full_name'),
                    $token
                );
            }

            $this->flash('success',
                'Account created successfully! You can now log in.'
            );
            $this->redirect(BASE_URL . '/login');

        } catch (\Throwable $e) {
            error_log('[register] ' . $e->getMessage());
            $this->flash('error', 'Something went wrong. Please try again.');
            $this->saveOldInput();
            $this->back();
        }
    }

    // ── Show login page ───────────────────────────────────────────────────────
    public function showLogin(): void
    {
        $this->view('auth/login', ['title' => 'Sign In']);
    }

    // ── Handle login ──────────────────────────────────────────────────────────
    public function login(): void
    {
        $this->verifyCsrf();

        $v = Validator::make($_POST, [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->first());
            $this->saveOldInput();
            $this->back();
            return;
        }

        $user = $this->users->authenticate(
            $this->input('email'),
            $_POST['password']
        );

        if (!$user) {
            $this->flash('error', 'Invalid email or password. Please try again.');
            $this->saveOldInput();
            $this->back();
            return;
        }

        Session::login($user);

        $intended = Session::intended($this->dashboardUrl($user['role']));
        $this->redirect($intended);
    }

    // ── Logout ────────────────────────────────────────────────────────────────
    public function logout(): void
    {
        Session::logout();
        $this->flash('success', 'You have been logged out successfully.');
        $this->redirect(BASE_URL . '/login');
    }

    // ── Email verification ────────────────────────────────────────────────────
    public function verifyEmail(): void
    {
        $token = $this->query('token');

        if (!$token) {
            $this->flash('error', 'Invalid verification link.');
            $this->redirect(BASE_URL . '/login');
            return;
        }

        if ($this->users->verifyEmail($token)) {
            $this->flash('success', '✓ Email verified! You can now log in.');
        } else {
            $this->flash('error', 'This verification link is invalid or already used.');
        }

        $this->redirect(BASE_URL . '/login');
    }

    // ── Forgot password ───────────────────────────────────────────────────────
    public function showForgot(): void
    {
        $this->view('auth/forgot-password', ['title' => 'Forgot Password']);
    }

    public function sendReset(): void
    {
        $this->verifyCsrf();

        $v = Validator::make($_POST, ['email' => 'required|email']);
        if ($v->fails()) {
            $this->flash('error', $v->first());
            $this->back();
            return;
        }

        $user = $this->users->findByEmail(strtolower(trim($this->input('email'))));

        // Same message whether email exists or not (prevents user enumeration)
        if ($user && $user['is_active']) {
            $token = $this->users->createResetToken($user['id']);
            Mailer::sendPasswordReset($user['email'], $user['full_name'], $token);
        }

        $this->flash('success', 'If an account exists for that email, a reset link has been sent.');
        $this->redirect(BASE_URL . '/forgot-password');
    }

    // ── Reset password ────────────────────────────────────────────────────────
    public function showReset(): void
    {
        $token = $this->query('token');

        if (!$token || !$this->users->findValidResetToken($token)) {
            $this->flash('error', 'This reset link is invalid or has expired.');
            $this->redirect(BASE_URL . '/forgot-password');
            return;
        }

        $this->view('auth/reset-password', ['title' => 'Set New Password', 'token' => $token]);
    }

    public function resetPassword(): void
    {
        $this->verifyCsrf();

        $v = Validator::make($_POST, [
            'token'            => 'required',
            'password'         => 'required|min:8|max:72',
            'password_confirm' => 'required|confirmed:password',
        ], [
            'password_confirm.confirmed' => 'Passwords do not match.',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->first());
            $this->redirect(BASE_URL . '/reset-password?token=' . urlencode($this->input('token')));
            return;
        }

        if (!$this->users->resetPassword($this->input('token'), $_POST['password'])) {
            $this->flash('error', 'This reset link is invalid or expired. Please request a new one.');
            $this->redirect(BASE_URL . '/forgot-password');
            return;
        }

        $this->flash('success', 'Password updated! Please log in with your new password.');
        $this->redirect(BASE_URL . '/login');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function dashboardUrl(string $role): string
    {
        return match($role) {
            'seeker'   => BASE_URL . '/seeker/dashboard',
            'employer' => BASE_URL . '/employer/dashboard',
            'admin'    => BASE_URL . '/admin/dashboard',
            default    => BASE_URL,
        };
    }

    private function saveOldInput(): void
    {
        $safe = $_POST;
        unset($safe['password'], $safe['password_confirm'], $safe['_csrf']);
        Session::set('_old_input', $safe);
    }
}
