<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h4 fw-800 mb-0">Job Alerts</h1>
        <p class="text-muted small mb-0">Get notified when matching jobs are posted.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#newAlertForm">
        <i class="bi bi-plus me-2"></i>New Alert
    </button>
</div>

<!-- Create alert form -->
<div class="collapse mb-4" id="newAlertForm">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="fw-700 mb-0">Create New Job Alert</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url('/seeker/alerts') ?>" class="row g-3">
                <?= csrf_field() ?>
                <div class="col-md-6">
                    <label class="form-label">Alert Name</label>
                    <input type="text" class="form-control" name="alert_name"
                           placeholder="e.g. PHP Jobs in Accra">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Keywords</label>
                    <input type="text" class="form-control" name="keywords"
                           placeholder="e.g. PHP developer backend">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Location</label>
                    <input type="text" class="form-control" name="location" placeholder="e.g. Accra">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Job Type</label>
                    <select class="form-select" name="job_type">
                        <option value="any">Any Type</option>
                        <option value="full_time">Full-Time</option>
                        <option value="part_time">Part-Time</option>
                        <option value="contract">Contract</option>
                        <option value="internship">Internship</option>
                        <option value="remote">Remote</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Frequency</label>
                    <select class="form-select" name="frequency">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="instant">Instant</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-bell me-2"></i>Create Alert
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Alert list -->
<?php if (empty($alerts)): ?>
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="bi bi-bell text-muted" style="font-size:3rem;"></i>
        <h5 class="fw-700 mt-3">No job alerts yet</h5>
        <p class="text-muted">Create an alert to be notified when matching jobs are posted.</p>
    </div>
</div>
<?php else: ?>
<div class="row g-3">
    <?php foreach ($alerts as $al): ?>
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div>
                        <h6 class="fw-700 mb-1"><?= e($al['alert_name'] ?: 'Job Alert') ?></h6>
                        <?php if ($al['keywords']): ?>
                        <p class="text-muted small mb-1">
                            <i class="bi bi-search me-1"></i><?= e($al['keywords']) ?>
                        </p>
                        <?php endif; ?>
                        <?php if ($al['location']): ?>
                        <p class="text-muted small mb-1">
                            <i class="bi bi-geo-alt me-1"></i><?= e($al['location']) ?>
                        </p>
                        <?php endif; ?>
                        <div class="d-flex gap-2 mt-2">
                            <span class="badge bg-light text-dark"><?= status_label($al['job_type'] ?? 'any') ?></span>
                            <span class="badge bg-light text-dark"><?= ucfirst($al['frequency']) ?></span>
                            <span class="badge <?= $al['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $al['is_active'] ? 'Active' : 'Paused' ?>
                            </span>
                        </div>
                    </div>
                    <form method="POST" action="<?= url('/seeker/alerts/delete') ?>"
                          data-no-loading class="flex-shrink-0">
                        <?= csrf_field() ?>
                        <input type="hidden" name="alert_id" value="<?= $al['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-light text-danger"
                                data-confirm="Delete this job alert?">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
