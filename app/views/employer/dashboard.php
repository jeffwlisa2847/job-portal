<?php $firstName = explode(' ', auth()['name'])[0]; ?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h4 fw-800 mb-0">Welcome, <?= e($firstName) ?> 👋</h1>
        <p class="text-muted small mb-0">
            <?= e($ep['company_name']) ?>
            <?php if ($ep['verification_status'] === 'approved'): ?>
                <span class="badge bg-success ms-1" style="font-size:10px;">✓ Verified</span>
            <?php elseif ($ep['verification_status'] === 'pending'): ?>
                <span class="badge bg-warning text-dark ms-1" style="font-size:10px;">⏳ Pending Verification</span>
            <?php else: ?>
                <span class="badge bg-secondary ms-1" style="font-size:10px;">Unverified</span>
            <?php endif; ?>
        </p>
    </div>
    <a href="<?= url('/employer/jobs/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Post a Job
    </a>
</div>

<?php if ($ep['verification_status'] === 'pending'): ?>
<div class="alert border-0 mb-4 rounded-3 d-flex align-items-center gap-3"
     style="background:#FFFBEB;border-left:4px solid #F59E0B!important;">
    <i class="bi bi-clock-history text-warning fs-4"></i>
    <div>
        <div class="fw-700">Verification in progress</div>
        <div class="small text-muted">Admin is reviewing your company documents. You'll be notified once approved.</div>
    </div>
</div>
<?php elseif ($ep['verification_status'] !== 'approved'): ?>
<div class="alert border-0 mb-4 rounded-3 d-flex align-items-center gap-3"
     style="background:#FEF2F2;border-left:4px solid #EF4444!important;">
    <i class="bi bi-shield-exclamation text-danger fs-4"></i>
    <div class="flex-grow-1">
        <div class="fw-700">Company not verified</div>
        <div class="small text-muted">Submit your verification to unlock job posting.</div>
    </div>
    <a href="<?= url('/employer/profile') ?>" class="btn btn-sm btn-danger flex-shrink-0">
        Verify Now
    </a>
</div>
<?php endif; ?>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card stat-card border-0" style="background:#EBF5FF;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(26,86,219,.15);">
                    <i class="bi bi-briefcase text-primary"></i>
                </div>
                <div>
                    <div class="stat-value text-primary"><?= $stats['active_jobs'] ?></div>
                    <div class="stat-label">Active Jobs</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card border-0" style="background:#F0FDF4;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(5,122,85,.15);">
                    <i class="bi bi-file-earmark-text text-success"></i>
                </div>
                <div>
                    <div class="stat-value text-success"><?= $stats['total_apps'] ?></div>
                    <div class="stat-label">Total Applications</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card border-0" style="background:#EDE9FE;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(124,58,237,.15);">
                    <i class="bi bi-star" style="color:#7c3aed;"></i>
                </div>
                <div>
                    <div class="stat-value" style="color:#7c3aed;"><?= $stats['shortlisted'] ?></div>
                    <div class="stat-label">Shortlisted</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card border-0" style="background:#FEF3C7;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(245,158,11,.15);">
                    <i class="bi bi-envelope-open text-warning"></i>
                </div>
                <div>
                    <div class="stat-value text-warning"><?= $stats['new_apps'] ?></div>
                    <div class="stat-label">Unread Apps</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent applications -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="fw-700 mb-0"><i class="bi bi-people me-2 text-primary"></i>Recent Applicants</h6>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentApps)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size:2.5rem;"></i>
                    <p class="text-muted mt-2 mb-0">No applications yet.</p>
                </div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recentApps as $app): ?>
                    <div class="list-group-item border-0 px-4 py-3 <?= !$app['is_read_by_employer'] ? 'bg-light' : '' ?>">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center
                                        justify-content-center fw-700 flex-shrink-0"
                                 style="width:38px;height:38px;font-size:14px;">
                                <?= strtoupper(substr($app['seeker_name'], 0, 1)) ?>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-700 small"><?= e($app['seeker_name']) ?></div>
                                <div class="text-muted" style="font-size:12px;">
                                    <?= e(truncate($app['headline'] ?? 'Applied for', 50)) ?>
                                    · <span class="text-primary"><?= e($app['job_title']) ?></span>
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <span class="badge <?= status_badge($app['status']) ?> rounded-pill">
                                    <?= status_label($app['status']) ?>
                                </span>
                                <div class="text-muted mt-1" style="font-size:11px;"><?= time_ago($app['applied_at']) ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Active job listings -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="fw-700 mb-0"><i class="bi bi-briefcase me-2 text-primary"></i>Active Jobs</h6>
                <a href="<?= url('/employer/jobs') ?>" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($activeJobs)): ?>
                <div class="text-center py-4">
                    <p class="text-muted small mb-2">No active job listings.</p>
                    <a href="<?= url('/employer/jobs/create') ?>" class="btn btn-primary btn-sm">Post First Job</a>
                </div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($activeJobs as $job): ?>
                    <div class="list-group-item border-0 px-4 py-3">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="min-w-0">
                                <div class="fw-700 small text-truncate"><?= e($job['title']) ?></div>
                                <div class="text-muted" style="font-size:12px;">
                                    <i class="bi bi-people me-1"></i><?= $job['app_count'] ?> applicants
                                    · <i class="bi bi-eye me-1"></i><?= number_format($job['view_count']) ?> views
                                </div>
                            </div>
                            <a href="<?= url('/employer/jobs/' . $job['id'] . '/applicants') ?>"
                               class="btn btn-sm btn-outline-primary flex-shrink-0">
                                Review
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
