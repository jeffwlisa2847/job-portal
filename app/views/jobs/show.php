<div class="container py-4">
<div class="row g-4">

<!-- ── MAIN CONTENT ─────────────────────────────────────────────────────── -->
<div class="col-lg-8">

    <!-- Job header -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex gap-3 align-items-start mb-3">
                <div class="logo-placeholder flex-shrink-0" style="width:64px;height:64px;font-size:24px;border-radius:14px;">
                    <?php if ($job['company_logo']): ?>
                        <img src="<?= url('/file?path=' . urlencode($job['company_logo'])) ?>"
                             alt="" style="width:64px;height:64px;border-radius:14px;object-fit:contain;border:1px solid #e5e7eb;padding:4px;background:#fff;">
                    <?php else: ?>
                        <?= strtoupper(substr($job['company_name'], 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div class="flex-grow-1">
                    <h1 class="h3 fw-800 mb-1"><?= e($job['title']) ?></h1>
                    <div class="text-muted"><?= e($job['company_name']) ?></div>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <span class="badge bg-light text-dark fw-500">
                            <i class="bi bi-geo-alt me-1"></i>
                            <?= $job['is_remote'] ? '<span class="text-success fw-600">Remote</span>' : e($job['location_city'] . ($job['location_country'] ? ', ' . $job['location_country'] : '')) ?>
                        </span>
                        <span class="badge bg-light text-dark fw-500"><?= status_label($job['job_type']) ?></span>
                        <span class="badge bg-light text-dark fw-500"><?= status_label($job['experience_level']) ?></span>
                        <?php if ($job['industry']): ?>
                        <span class="badge bg-light text-dark fw-500"><?= e($job['industry']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Key details row -->
            <div class="row g-3 py-3 border-top border-bottom mb-3">
                <div class="col-6 col-md-3 text-center">
                    <div class="text-muted small">Salary</div>
                    <div class="fw-700 small">
                        <?= salary_range($job['salary_min'], $job['salary_max'], $job['salary_currency'], (bool)$job['salary_is_hidden']) ?>
                        <?php if (!$job['salary_is_hidden'] && $job['salary_period']): ?>
                        <div class="text-muted fw-400" style="font-size:11px;">per <?= $job['salary_period'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-6 col-md-3 text-center">
                    <div class="text-muted small">Vacancies</div>
                    <div class="fw-700 small"><?= $job['vacancies'] ?></div>
                </div>
                <div class="col-6 col-md-3 text-center">
                    <div class="text-muted small">Posted</div>
                    <div class="fw-700 small"><?= time_ago($job['created_at']) ?></div>
                </div>
                <div class="col-6 col-md-3 text-center">
                    <div class="text-muted small">Deadline</div>
                    <div class="fw-700 small">
                        <?= $job['application_deadline'] ? format_date($job['application_deadline'], 'M d, Y') : 'Open' ?>
                    </div>
                </div>
            </div>

            <!-- Required skills -->
            <?php if (!empty($skills)): ?>
            <div class="mb-3">
                <div class="fw-700 small mb-2">Required Skills</div>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($skills as $sk): ?>
                    <span class="badge rounded-pill fw-500"
                          style="background:<?= $sk['is_required'] ? '#EBF5FF' : '#F9FAFB' ?>;
                                 color:<?= $sk['is_required'] ? '#1A56DB' : '#6B7280' ?>;
                                 padding:6px 12px;font-size:12px;border:1px solid <?= $sk['is_required'] ? '#BFDBFE' : '#E5E7EB' ?>;">
                        <?= e($sk['skill_name']) ?>
                        <?= $sk['is_required'] ? '' : ' <span style="font-size:10px;">optional</span>' ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Job description -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-700 mb-3">Job Description</h5>
            <div class="job-content" style="line-height:1.8;color:#374151;">
                <?= nl2br(e($job['description'])) ?>
            </div>

            <?php if ($job['responsibilities']): ?>
            <h5 class="fw-700 mt-4 mb-3">Responsibilities</h5>
            <div class="job-content" style="line-height:1.8;color:#374151;">
                <?= nl2br(e($job['responsibilities'])) ?>
            </div>
            <?php endif; ?>

            <?php if ($job['requirements']): ?>
            <h5 class="fw-700 mt-4 mb-3">Requirements</h5>
            <div class="job-content" style="line-height:1.8;color:#374151;">
                <?= nl2br(e($job['requirements'])) ?>
            </div>
            <?php endif; ?>

            <?php if ($job['benefits']): ?>
            <h5 class="fw-700 mt-4 mb-3">Benefits</h5>
            <div class="job-content" style="line-height:1.8;color:#374151;">
                <?= nl2br(e($job['benefits'])) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Related jobs -->
    <?php if (!empty($related)): ?>
    <h5 class="fw-700 mb-3">Similar Jobs</h5>
    <div class="row g-3">
        <?php foreach ($related as $r): ?>
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm job-card">
                <div class="card-body p-3">
                    <div class="d-flex gap-2">
                        <div class="logo-placeholder flex-shrink-0"
                             style="width:40px;height:40px;font-size:15px;border-radius:8px;">
                            <?= strtoupper(substr($r['company_name'], 0, 1)) ?>
                        </div>
                        <div class="min-w-0">
                            <div class="fw-700 small">
                                <a href="<?= url('/jobs/' . $r['slug']) ?>"
                                   class="text-dark text-decoration-none">
                                    <?= e(truncate($r['title'], 45)) ?>
                                </a>
                            </div>
                            <div class="text-muted" style="font-size:12px;"><?= e($r['company_name']) ?></div>
                            <div class="text-muted" style="font-size:11px;">
                                <i class="bi bi-geo-alt me-1"></i>
                                <?= $r['is_remote'] ? 'Remote' : e($r['location_city'] ?? 'N/A') ?>
                                · <?= status_label($r['job_type']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>

<!-- ── APPLY SIDEBAR ─────────────────────────────────────────────────────── -->
<div class="col-lg-4">

    <!-- Apply card (sticky) -->
    <div class="sticky-top" style="top:80px;">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">

                <?php if ($job['status'] !== 'active'): ?>
                    <div class="alert alert-warning border-0 rounded-3 mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        This job is no longer accepting applications.
                    </div>

                <?php elseif (!is_logged_in()): ?>
                    <h6 class="fw-700 mb-3">Apply for this Job</h6>
                    <p class="text-muted small">Create a free account or sign in to apply.</p>
                    <a href="<?= url('/register?role=seeker') ?>" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-person-plus me-2"></i>Create Account &amp; Apply
                    </a>
                    <a href="<?= url('/login') ?>" class="btn btn-outline-primary w-100">
                        Sign In to Apply
                    </a>

                <?php elseif (Session::isSeeker()): ?>
                    <?php if ($hasApplied): ?>
                        <div class="text-center py-2">
                            <div class="rounded-circle bg-success d-inline-flex align-items-center
                                        justify-content-center mb-3"
                                 style="width:56px;height:56px;">
                                <i class="bi bi-check-lg text-white fs-4"></i>
                            </div>
                            <h6 class="fw-700 mb-1">Application Submitted</h6>
                            <p class="text-muted small mb-3">You've already applied for this job.</p>
                            <a href="<?= url('/seeker/applications') ?>" class="btn btn-outline-primary btn-sm w-100">
                                Track Application
                            </a>
                        </div>
                    <?php else: ?>
                        <h6 class="fw-700 mb-3">Apply for this Job</h6>
                        <?php if (!$profile): ?>
                            <div class="alert alert-warning small border-0">Please complete your profile before applying.</div>
                        <?php else: ?>
                        <!-- Apply form -->
                        <form method="POST"
                              action="<?= url('/seeker/apply/' . $job['id']) ?>"
                              enctype="multipart/form-data">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label small fw-600">Cover Letter <span class="text-muted fw-400">(optional)</span></label>
                                <textarea class="form-control form-control-sm" name="cover_letter_text"
                                          rows="4"
                                          placeholder="Briefly explain why you're a great fit..."
                                          maxlength="1000"></textarea>
                            </div>

                            <?php if ($profile['resume_path']): ?>
                            <div class="mb-3 p-2 rounded-2 border bg-light d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-pdf text-danger"></i>
                                <div class="small text-truncate flex-grow-1">
                                    <?= e($profile['resume_original_name'] ?? 'Resume on file') ?>
                                </div>
                                <span class="badge bg-success rounded-pill" style="font-size:10px;">✓ Attached</span>
                            </div>
                            <?php else: ?>
                            <div class="mb-3">
                                <label class="form-label small fw-600">Resume <span class="text-muted fw-400">(optional)</span></label>
                                <input type="file" class="form-control form-control-sm"
                                       name="cover_letter" accept=".pdf,.doc,.docx">
                            </div>
                            <div class="alert alert-info border-0 small p-2 mb-3">
                                <i class="bi bi-info-circle me-1"></i>
                                <a href="<?= url('/seeker/profile') ?>" class="text-decoration-none">Upload your resume</a>
                                to your profile for faster applications.
                            </div>
                            <?php endif; ?>

                            <button type="submit" class="btn btn-primary w-100 fw-700">
                                <i class="bi bi-send me-2"></i>Submit Application
                            </button>
                        </form>
                        <?php endif; ?>

                        <!-- Save job toggle -->
                        <form method="POST"
                              action="<?= url($isSaved ? '/seeker/unsave-job/' . $job['id'] : '/seeker/save-job/' . $job['id']) ?>"
                              class="mt-2" data-no-loading>
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-secondary w-100 btn-sm">
                                <i class="bi bi-bookmark<?= $isSaved ? '-fill text-primary' : '' ?> me-1"></i>
                                <?= $isSaved ? 'Saved' : 'Save Job' ?>
                            </button>
                        </form>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="alert alert-info border-0 small rounded-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Sign in as a job seeker to apply for this position.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Company info card -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h6 class="fw-700 mb-3">About <?= e($job['company_name']) ?></h6>
                <?php if ($job['company_desc']): ?>
                <p class="text-muted small"><?= e(truncate($job['company_desc'], 180)) ?></p>
                <?php endif; ?>
                <div class="d-flex flex-column gap-2">
                    <?php if ($job['company_city']): ?>
                    <div class="d-flex align-items-center gap-2 small text-muted">
                        <i class="bi bi-geo-alt text-primary"></i>
                        <?= e($job['company_city']) ?><?= $job['company_country'] ? ', ' . e($job['company_country']) : '' ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($job['company_size']): ?>
                    <div class="d-flex align-items-center gap-2 small text-muted">
                        <i class="bi bi-people text-primary"></i>
                        <?= e($job['company_size']) ?> employees
                    </div>
                    <?php endif; ?>
                    <?php if ($job['company_industry']): ?>
                    <div class="d-flex align-items-center gap-2 small text-muted">
                        <i class="bi bi-building text-primary"></i>
                        <?= e($job['company_industry']) ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($job['company_website']): ?>
                    <div class="d-flex align-items-center gap-2 small">
                        <i class="bi bi-globe text-primary"></i>
                        <a href="<?= e($job['company_website']) ?>" target="_blank" rel="noopener"
                           class="text-primary text-decoration-none">
                            Company Website <i class="bi bi-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
</div>
