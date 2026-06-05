<?php
$old  = Session::get('_old_input', []);
Session::forget('_old_input');
$role = $_GET['role'] ?? $old['role'] ?? 'seeker';
?>
<div class="auth-page py-5">
<div class="container">
<div class="row justify-content-center">
<div class="col-12 col-md-9 col-lg-7 col-xl-6">
<div class="auth-card">

    <div class="text-center mb-4">
        <a href="<?= url('/') ?>" class="auth-logo">
            <i class="bi bi-briefcase-fill me-1"></i><?= APP_NAME ?>
        </a>
        <h1 class="h4 fw-800 mt-3 mb-1">Create your account</h1>
        <p class="text-muted small">Join thousands of professionals.</p>
    </div>

    <!-- Role Selector -->
    <p class="form-label text-center mb-2">I am a…</p>
    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="role-card <?= $role==='seeker' ? 'selected':'' ?>"
                 data-role="seeker" onclick="selectRole('seeker')">
                <span class="role-icon">🧑‍💼</span>
                <div class="fw-700">Job Seeker</div>
                <div class="text-muted" style="font-size:12px;">Looking for work</div>
            </div>
        </div>
        <div class="col-6">
            <div class="role-card <?= $role==='employer' ? 'selected':'' ?>"
                 data-role="employer" onclick="selectRole('employer')">
                <span class="role-icon">🏢</span>
                <div class="fw-700">Employer</div>
                <div class="text-muted" style="font-size:12px;">Hiring talent</div>
            </div>
        </div>
    </div>

    <form method="POST" action="<?= url('/register') ?>" id="regForm" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="role" id="roleInput" value="<?= e($role) ?>">

        <!-- Company name (employer only) -->
        <div class="mb-3" id="companyField"
             style="display:<?= $role==='employer'?'block':'none' ?>;">
            <label class="form-label">Company Name <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-building"></i></span>
                <input type="text" class="form-control" name="company_name"
                       placeholder="e.g. TechCorp Ghana Ltd"
                       value="<?= e($old['company_name'] ?? '') ?>">
            </div>
        </div>

        <!-- Full name -->
        <div class="mb-3">
            <label class="form-label">Full Name <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" name="full_name"
                       placeholder="e.g. Kwame Asante"
                       value="<?= e($old['full_name'] ?? '') ?>" autofocus>
            </div>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label class="form-label">Email Address <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" name="email"
                       placeholder="you@example.com"
                       value="<?= e($old['email'] ?? '') ?>">
            </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" name="password"
                       id="pw" placeholder="Min. 8 characters"
                       oninput="checkStrength(this.value)">
                <button type="button" class="password-toggle input-group-text" onclick="togglePw('pw',this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            <div id="strengthBar" style="height:4px;border-radius:2px;margin-top:6px;width:0;background:#e5e7eb;transition:all .3s;"></div>
            <small id="strengthLabel" style="font-size:11.5px;"></small>
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password" class="form-control" name="password_confirm"
                       id="pw2" placeholder="Repeat your password">
                <button type="button" class="password-toggle input-group-text" onclick="togglePw('pw2',this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            <small id="matchMsg" style="font-size:11.5px;display:block;margin-top:4px;"></small>
        </div>

        <!-- Terms -->
        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="terms" required>
            <label class="form-check-label small text-muted" for="terms">
                I agree to the <a href="#" class="text-primary">Terms of Service</a> and
                <a href="#" class="text-primary">Privacy Policy</a>
            </label>
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-lg">
            Create Account <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </form>

    <div class="auth-divider my-4">or</div>

    <p class="text-center text-muted small mb-0">
        Already have an account?
        <a href="<?= url('/login') ?>" class="text-primary fw-600 text-decoration-none">Sign in</a>
    </p>

</div>
</div>
</div>
</div>

<script>
function selectRole(role) {
    document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
    document.querySelector(`.role-card[data-role="${role}"]`).classList.add('selected');
    document.getElementById('roleInput').value = role;
    document.getElementById('companyField').style.display = role === 'employer' ? 'block' : 'none';
}
function togglePw(id, btn) {
    const el = document.getElementById(id);
    const isText = el.type === 'text';
    el.type = isText ? 'password' : 'text';
    btn.querySelector('i').className = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
}
function checkStrength(v) {
    let s = 0;
    if (v.length >= 8)           s++;
    if (/[A-Z]/.test(v))         s++;
    if (/[0-9]/.test(v))         s++;
    if (/[^A-Za-z0-9]/.test(v))  s++;
    const c = ['','#ef4444','#f59e0b','#3b82f6','#10b981'][s];
    const l = ['','Weak','Fair','Good','✓ Strong'][s];
    const bar = document.getElementById('strengthBar');
    bar.style.width = (s * 25) + '%';
    bar.style.background = c;
    const lbl = document.getElementById('strengthLabel');
    lbl.textContent = l; lbl.style.color = c;
}
document.getElementById('pw2').addEventListener('input', function() {
    const msg = document.getElementById('matchMsg');
    if (!this.value) { msg.textContent=''; return; }
    const ok = this.value === document.getElementById('pw').value;
    msg.textContent = ok ? '✓ Passwords match' : '✗ Passwords do not match';
    msg.style.color = ok ? '#10b981' : '#ef4444';
});
document.getElementById('regForm').addEventListener('submit', function(e) {
    if (!document.getElementById('terms').checked) {
        e.preventDefault();
        alert('Please agree to the Terms of Service to continue.');
    }
});
</script>
