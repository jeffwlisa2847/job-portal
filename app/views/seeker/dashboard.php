<?php
$totalApps      = array_sum($statusCounts);
$shortlisted    = $statusCounts['shortlisted'] ?? 0;
$interviews     = $statusCounts['interview_scheduled'] ?? 0;
$offers         = ($statusCounts['offered'] ?? 0) + ($statusCounts['hired'] ?? 0);
$firstName      = explode(' ', $profile['full_name'])[0];
?>

<!-- Page header -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h4 fw-800 mb-0">Welcome back, <?= e($firstName) ?> 👋</h1>
        <p class="text-muted small mb-0">Here's what's happening with your job search today.</p>
    </div>
    <a href="<?= url('/jobs') ?>" class="btn btn-primary">
        <i class="bi bi-search me-2"></i>Find Jobs
    </a>
</div>

<!-- Profile completion banner -->
<?php if ($score < 80): ?>
<div class="alert border-0 mb-4 rounded-3 d-flex align-items-center gap-3"
     style="background:linear-gradient(135deg,#EBF5FF,#EDE9FE);">
    <div class="flex-shrink-0 rounded-circle bg-primary d-flex align-items-center justify-content-center"
         style="width:48px;height:48px;">
        <i class="bi bi-person-check text-white fs-5"></i>
    </div>
    <div class="flex-grow-1">
        <div class="fw-700 text-dark mb-1">Complete your profile — <?= $score ?>% done</div>
        <div class="progress" style="height:6px;border-radius:3px;">
            <div class="progress-bar bg-primary" style="width:<?= $score ?>%;border-radius:3px;"></div>
        </div>
        <div class="text-muted small mt-1">A complete profile gets 3× more views from employers.</div>
    </div>
    <a href="<?= url('/seeker/profile') ?>" class="btn btn-primary btn-sm flex-shrink-0">
        Complete Profile
    </a>
</div>
<?php endif; ?>

<!-- Stat cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card stat-card border-0 h-100" style="background:#EBF5FF;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(26,86,219,.15);">
                    <i class="bi bi-send text-primary"></i>
                </div>
                <div>
                    <div class="stat-value text-primary"><?= $totalApps ?></div>
                    <div class="stat-label">Total Applied</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card border-0 h-100" style="background:#F0FDF4;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(5,122,85,.15);">
                    <i class="bi bi-star text-success"></i>
                </div>
                <div>
                    <div class="stat-value text-success"><?= $shortlisted ?></div>
                    <div class="stat-label">Shortlisted</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card border-0 h-100" style="background:#FFFBEB;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(245,158,11,.15);">
                    <i class="bi bi-calendar-check text-warning"></i>
                </div>
                <div>
                    <div class="stat-value text-warning"><?= $interviews ?></div>
                    <div class="stat-label">Interviews</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card border-0 h-100" style="background:#FDF4FF;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(168,85,247,.15);">
                    <i class="bi bi-trophy" style="color:#a855f7;"></i>
                </div>
                <div>
                    <div class="stat-value" style="color:#a855f7;"><?= $offers ?></div>
                    <div class="stat-label">Offers</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Applications -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="fw-700 mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Applications</h6>
                <a href="<?= url('/seeker/applications') ?>" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentApps)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark-text text-muted" style="font-size:2.5rem;"></i>
                        <p class="text-muted mt-2 mb-3">No applications yet.</p>
                        <a href="<?= url('/jobs') ?>" class="btn btn-primary btn-sm px-4">Browse Jobs</a>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentApps as $app): ?>
                        <div class="list-group-item list-group-item-action border-0 px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <!-- Company logo -->
                                <div class="logo-placeholder flex-shrink-0">
                                    <?php if ($app['company_logo']): ?>
                                        <img src="<?= url('/file?path=' . urlencode($app['company_logo'])) ?>"
                                             alt="" class="company-logo">
                                    <?php else: ?>
                                        <?= strtoupper(substr($app['company_name'], 0, 1)) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-700 text-truncate"><?= e($app['job_title']) ?></div>
                                    <div class="text-muted small"><?= e($app['company_name']) ?></div>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <span class="badge <?= status_badge($app['status']) ?> rounded-pill">
                                        <?= status_label($app['status']) ?>
                                    </span>
                                    <div class="text-muted" style="font-size:11px;margin-top:3px;">
                                        <?= time_ago($app['applied_at']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right column -->
    <div class="col-lg-4 d-flex flex-column gap-4">

        <!-- Profile card -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-4">
                <?php if ($profile['avatar_path']): ?>
                    <img src="<?= url('/file?path=' . urlencode($profile['avatar_path'])) ?>"
                         class="avatar mb-3" style="width:72px;height:72px;" alt="Avatar">
                <?php else: ?>
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center
                                justify-content-center fw-800 mb-3"
                         style="width:72px;height:72px;font-size:28px;">
                        <?= strtoupper(substr($profile['full_name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <h6 class="fw-800 mb-0"><?= e($profile['full_name']) ?></h6>
                <p class="text-muted small mb-3">
                    <?= $profile['headline'] ? e(truncate($profile['headline'], 60)) : 'No headline yet' ?>
                </p>
                <?php if ($profile['location_city']): ?>
                <p class="text-muted small mb-3">
                    <i class="bi bi-geo-alt me-1"></i>
                    <?= e($profile['location_city']) ?><?= $profile['location_country'] ? ', ' . e($profile['location_country']) : '' ?>
                </p>
                <?php endif; ?>
                <a href="<?= url('/seeker/profile') ?>" class="btn btn-outline-primary btn-sm w-100">
                    Edit Profile
                </a>
            </div>
        </div>

        <!-- Top Skills -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="fw-700 mb-0"><i class="bi bi-lightning me-2 text-primary"></i>Top Skills</h6>
                <a href="<?= url('/seeker/profile#skills') ?>" class="btn btn-sm btn-light">Manage</a>
            </div>
            <div class="card-body">
                <?php if (empty($skills)): ?>
                    <p class="text-muted small mb-0 text-center py-2">No skills added yet.</p>
                <?php else: ?>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach (array_slice($skills, 0, 8) as $sk): ?>
                        <span class="badge rounded-pill fw-500"
                              style="background:var(--primary-light);color:var(--primary);padding:6px 12px;font-size:12px;">
                            <?= e($sk['skill_name']) ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
