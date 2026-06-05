<?php
$old = Session::get('_old_input', []);
Session::forget('_old_input');
?>
<div class="auth-page py-5">
<div class="container">
<div class="row justify-content-center">
<div class="col-12 col-md-8 col-lg-6 col-xl-5">
<div class="auth-card">

    <div class="text-center mb-4">
        <a href="<?= url('/') ?>" class="auth-logo">
            <i class="bi bi-briefcase-fill me-1"></i><?= APP_NAME ?>
        </a>
        <h1 class="h4 fw-800 mt-3 mb-1">Welcome back</h1>
        <p class="text-muted small">Sign in to your account to continue.</p>
    </div>

    <form method="POST" action="<?= url('/login') ?>" novalidate>
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" name="email"
                       placeholder="you@example.com"
                       value="<?= e($old['email'] ?? '') ?>"
                       autofocus required>
            </div>
        </div>

        <div class="mb-1">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" name="password"
                       id="loginPw" placeholder="Your password" required>
                <button type="button" class="password-toggle input-group-text"
                        onclick="togglePw()">
                    <i class="bi bi-eye" id="pwIcon"></i>
                </button>
            </div>
        </div>

        <div class="text-end mb-4">
            <a href="<?= url('/forgot-password') ?>" class="text-primary text-decoration-none small">
                Forgot password?
            </a>
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-lg">
            Sign In <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </form>

    <!-- Demo credentials (dev mode only) -->
    <?php if (APP_DEBUG): ?>
    <div class="mt-4 p-3 rounded-3 bg-light border">
        <p class="fw-700 small mb-2 text-muted">
            <i class="bi bi-info-circle me-1"></i>Demo Credentials (password: <code>Admin@1234</code>)
        </p>
        <div class="d-flex flex-column gap-1">
            <button class="btn btn-sm btn-outline-secondary text-start"
                    onclick="fill('admin@jobportal.com')">
                🔑 Admin — admin@jobportal.com
            </button>
            <button class="btn btn-sm btn-outline-secondary text-start"
                    onclick="fill('kwame@example.com')">
                🧑‍💼 Seeker — kwame@example.com
            </button>
            <button class="btn btn-sm btn-outline-secondary text-start"
                    onclick="fill('ama@techcorp.com')">
                🏢 Employer — ama@techcorp.com
            </button>
        </div>
    </div>
    <?php endif; ?>

    <div class="auth-divider my-4">or</div>

    <p class="text-center text-muted small mb-0">
        Don't have an account?
        <a href="<?= url('/register') ?>" class="text-primary fw-600 text-decoration-none">
            Create one free
        </a>
    </p>

</div>
</div>
</div>
</div>

<script>
function togglePw() {
    const el = document.getElementById('loginPw');
    const ic = document.getElementById('pwIcon');
    const isText = el.type === 'text';
    el.type = isText ? 'password' : 'text';
    ic.className = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
}
function fill(email) {
    document.querySelector('input[name="email"]').value = email;
    document.getElementById('loginPw').value = 'Admin@1234';
}
</script>
