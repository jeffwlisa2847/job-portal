<?php
$statusFilters = [
    ''                    => 'All Applications',
    'applied'             => 'Applied',
    'under_review'        => 'Under Review',
    'shortlisted'         => 'Shortlisted',
    'interview_scheduled' => 'Interview',
    'offered'             => 'Offered',
    'rejected'            => 'Not Selected',
    'withdrawn'           => 'Withdrawn',
];
$activeFilter = $_GET['status'] ?? '';
?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h4 fw-800 mb-0">My Applications</h1>
        <p class="text-muted small mb-0">Track every job you've applied to.</p>
    </div>
    <a href="<?= url('/jobs') ?>" class="btn btn-primary">
        <i class="bi bi-search me-2"></i>Find More Jobs
    </a>
</div>

<!-- Status filter tabs -->
<div class="d-flex gap-2 flex-wrap mb-4">
    <?php foreach ($statusFilters as $val => $label): ?>
    <a href="<?= url('/seeker/applications' . ($val ? '?status=' . $val : '')) ?>"
       class="btn btn-sm <?= $activeFilter === $val ? 'btn-primary' : 'btn-light' ?>">
        <?= $label ?>
    </a>
    <?php endforeach; ?>
</div>

<!-- Application list -->
<?php if (empty($apps)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-file-earmark-x text-muted" style="font-size:3rem;"></i>
            <h5 class="fw-700 mt-3">No applications found</h5>
            <p class="text-muted">
                <?= $activeFilter ? 'No applications with this status.' : "You haven't applied to any jobs yet." ?>
            </p>
            <a href="<?= url('/jobs') ?>" class="btn btn-primary px-4">Browse Jobs</a>
        </div>
    </div>
<?php else: ?>
    <div class="d-flex flex-column gap-3">
        <?php foreach ($apps as $app): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row align-items-center g-3">

                    <!-- Company logo + job info -->
                    <div class="col-12 col-md-5 d-flex gap-3 align-items-center">
                        <div class="logo-placeholder flex-shrink-0">
                            <?php if ($app['company_logo']): ?>
                                <img src="<?= url('/file?path=' . urlencode($app['company_logo'])) ?>"
                                     alt="" class="company-logo">
                            <?php else: ?>
                                <?= strtoupper(substr($app['company_name'], 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <div class="min-w-0">
                            <h6 class="fw-700 mb-0 text-truncate"><?= e($app['job_title']) ?></h6>
                            <div class="text-muted small"><?= e($app['company_name']) ?></div>
                            <div class="d-flex flex-wrap gap-2 mt-1">
                                <span class="badge bg-light text-dark fw-500" style="font-size:11px;">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    <?= $app['is_remote'] ? 'Remote' : e($app['location_city'] ?? 'N/A') ?>
                                </span>
                                <span class="badge bg-light text-dark fw-500" style="font-size:11px;">
                                    <i class="bi bi-clock me-1"></i>
                                    <?= status_label($app['job_type']) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Status + date -->
                    <div class="col-6 col-md-3">
                        <div class="text-muted small mb-1">Status</div>
                        <span class="badge <?= status_badge($app['status']) ?> rounded-pill px-3 py-2">
                            <?= status_label($app['status']) ?>
                        </span>
                    </div>

                    <!-- Interview info if scheduled -->
                    <div class="col-6 col-md-2">
                        <div class="text-muted small mb-1">Applied</div>
                        <div class="fw-500 small"><?= format_date($app['applied_at'], 'M d, Y') ?></div>
                        <?php if ($app['interview_date']): ?>
                        <div class="text-success small mt-1">
                            <i class="bi bi-calendar-check me-1"></i>
                            <?= format_date($app['interview_date'], 'M d · H:i') ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Actions -->
                    <div class="col-12 col-md-2 d-flex gap-2 justify-content-md-end">
                        <?php if (!in_array($app['status'], ['withdrawn','rejected','hired'])): ?>
                        <form method="POST"
                              action="<?= url('/seeker/withdraw/' . $app['id']) ?>"
                              data-no-loading>
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    data-confirm="Withdraw this application?">
                                <i class="bi bi-x-circle me-1"></i>Withdraw
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- Interview link if available -->
                <?php if ($app['interview_date'] && $app['meeting_link']): ?>
                <div class="mt-3 pt-3 border-top">
                    <div class="d-flex align-items-center gap-2 p-2 rounded-2"
                         style="background:#F0FDF4;border:1px solid #86EFAC;">
                        <i class="bi bi-camera-video-fill text-success"></i>
                        <span class="small fw-600 text-success">
                            <?= ucfirst($app['interview_type'] ?? 'video') ?> Interview:
                            <?= format_date($app['interview_date'], 'D, M d Y · H:i') ?>
                        </span>
                        <a href="<?= e($app['meeting_link']) ?>" target="_blank"
                           class="btn btn-success btn-sm ms-auto">
                            Join <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($paging['pages'] > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $paging['pages']; $i++): ?>
            <li class="page-item <?= $i === $paging['current_page'] ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?><?= $activeFilter ? '&status=' . $activeFilter : '' ?>">
                    <?= $i ?>
                </a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>

<?php endif; ?>
