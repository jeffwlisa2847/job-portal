<div class="mb-4">
    <h1 class="h4 fw-800 mb-0">Reports & Analytics</h1>
    <p class="text-muted small mb-0">Platform-wide statistics and insights.</p>
</div>

<!-- Export buttons -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="fw-700 mb-0"><i class="bi bi-download me-2 text-primary"></i>Export Data (CSV)</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="p-3 rounded-2 border text-center h-100">
                    <i class="bi bi-people text-primary" style="font-size:2rem;"></i>
                    <h6 class="fw-700 mt-2 mb-1">Users</h6>
                    <p class="text-muted small mb-3">All registered users with roles and status</p>
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <a href="<?= url('/admin/export/users') ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-download me-1"></i>All Users
                        </a>
                        <a href="<?= url('/admin/export/users?role=seeker') ?>" class="btn btn-sm btn-outline-primary">
                            Seekers
                        </a>
                        <a href="<?= url('/admin/export/users?role=employer') ?>" class="btn btn-sm btn-outline-primary">
                            Employers
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="p-3 rounded-2 border text-center h-100">
                    <i class="bi bi-briefcase text-success" style="font-size:2rem;"></i>
                    <h6 class="fw-700 mt-2 mb-1">Job Listings</h6>
                    <p class="text-muted small mb-3">All jobs with views, applications and status</p>
                    <a href="<?= url('/admin/export/jobs') ?>" class="btn btn-sm btn-success">
                        <i class="bi bi-download me-1"></i>Export Jobs
                    </a>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="p-3 rounded-2 border text-center h-100">
                    <i class="bi bi-file-earmark-text text-warning" style="font-size:2rem;"></i>
                    <h6 class="fw-700 mt-2 mb-1">Applications</h6>
                    <p class="text-muted small mb-3">All applications with seeker and job details</p>
                    <a href="<?= url('/admin/export/applications') ?>" class="btn btn-sm btn-warning text-dark">
                        <i class="bi bi-download me-1"></i>Export Applications
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="row g-4">

    <!-- Users by role -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-700 mb-0"><i class="bi bi-people me-2 text-primary"></i>Users by Role</h6>
            </div>
            <div class="card-body">
                <?php
                $roleColors = ['seeker'=>'#1A56DB','employer'=>'#7C3AED','admin'=>'#DC2626'];
                $totalUsers = array_sum(array_column($usersByRole,'cnt'));
                foreach ($usersByRole as $r):
                    $pct = $totalUsers > 0 ? round($r['cnt']/$totalUsers*100) : 0;
                    $col = $roleColors[$r['role']] ?? '#6B7280';
                ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="fw-600"><?= ucfirst($r['role']) ?>s</span>
                        <span class="text-muted"><?= number_format($r['cnt']) ?> (<?= $pct ?>%)</span>
                    </div>
                    <div class="progress" style="height:8px;border-radius:4px;">
                        <div class="progress-bar" style="width:<?= $pct ?>%;background:<?= $col ?>;border-radius:4px;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Applications by status -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-700 mb-0"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Applications</h6>
            </div>
            <div class="card-body">
                <?php
                $totalApps = array_sum(array_column($appsByStatus,'cnt'));
                $appColors = ['applied'=>'#6B7280','under_review'=>'#0891B2','shortlisted'=>'#1A56DB',
                              'interview_scheduled'=>'#D97706','offered'=>'#059669','hired'=>'#057A55',
                              'rejected'=>'#DC2626','withdrawn'=>'#374151'];
                foreach ($appsByStatus as $s):
                    $pct = $totalApps > 0 ? round($s['cnt']/$totalApps*100) : 0;
                    $col = $appColors[$s['status']] ?? '#6B7280';
                ?>
                <div class="mb-2">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="fw-500"><?= status_label($s['status']) ?></span>
                        <span class="text-muted"><?= $s['cnt'] ?></span>
                    </div>
                    <div class="progress" style="height:6px;border-radius:3px;">
                        <div class="progress-bar" style="width:<?= $pct ?>%;background:<?= $col ?>;border-radius:3px;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Jobs by type -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-700 mb-0"><i class="bi bi-briefcase me-2 text-primary"></i>Jobs by Type</h6>
            </div>
            <div class="card-body">
                <?php
                $totalJobs = array_sum(array_column($jobsByType,'cnt'));
                $typeColors = ['full_time'=>'#1A56DB','part_time'=>'#059669','contract'=>'#D97706',
                               'internship'=>'#7C3AED','freelance'=>'#DC2626','volunteer'=>'#6B7280'];
                foreach ($jobsByType as $jt):
                    $pct = $totalJobs > 0 ? round($jt['cnt']/$totalJobs*100) : 0;
                    $col = $typeColors[$jt['job_type']] ?? '#6B7280';
                ?>
                <div class="mb-2">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="fw-500"><?= status_label($jt['job_type']) ?></span>
                        <span class="text-muted"><?= $jt['cnt'] ?></span>
                    </div>
                    <div class="progress" style="height:6px;border-radius:3px;">
                        <div class="progress-bar" style="width:<?= $pct ?>%;background:<?= $col ?>;border-radius:3px;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($jobsByType)): ?>
                <p class="text-muted small text-center py-3 mb-0">No active jobs yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Top industries -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-700 mb-0"><i class="bi bi-building me-2 text-primary"></i>Top Industries</h6>
            </div>
            <div class="card-body">
                <?php
                $maxInd = $topIndustries ? max(array_column($topIndustries,'cnt')) : 1;
                foreach ($topIndustries as $ind):
                    $pct = $maxInd > 0 ? round($ind['cnt']/$maxInd*100) : 0;
                ?>
                <div class="mb-2">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="fw-500 text-truncate me-2" style="max-width:140px;"><?= e($ind['industry']) ?></span>
                        <span class="text-muted flex-shrink-0"><?= $ind['cnt'] ?></span>
                    </div>
                    <div class="progress" style="height:6px;border-radius:3px;">
                        <div class="progress-bar bg-primary" style="width:<?= $pct ?>%;border-radius:3px;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($topIndustries)): ?>
                <p class="text-muted small text-center py-3 mb-0">No data yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Registration trend (last 14 days) -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-700 mb-0">
                    <i class="bi bi-graph-up me-2 text-primary"></i>New Registrations — Last 14 Days
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($dailyRegistrations)): ?>
                <p class="text-muted text-center py-3 mb-0">No registration data yet.</p>
                <?php else: ?>
                <?php
                $maxReg = max(array_column($dailyRegistrations,'cnt'));
                ?>
                <div class="d-flex align-items-end gap-2" style="height:120px;">
                    <?php foreach ($dailyRegistrations as $day): ?>
                    <?php $h = $maxReg > 0 ? max(4, round($day['cnt']/$maxReg*100)) : 4; ?>
                    <div class="flex-grow-1 d-flex flex-column align-items-center gap-1">
                        <span style="font-size:10px;color:#6B7280;"><?= $day['cnt'] ?></span>
                        <div class="w-100 rounded-top" title="<?= format_date($day['day'], 'M d') ?>: <?= $day['cnt'] ?> users"
                             style="height:<?= $h ?>%;background:#1A56DB;border-radius:4px 4px 0 0;min-height:4px;cursor:pointer;transition:.2s;"
                             onmouseover="this.style.background='#1347c8'"
                             onmouseout="this.style.background='#1A56DB'">
                        </div>
                        <span style="font-size:9px;color:#9CA3AF;">
                            <?= date('M d', strtotime($day['day'])) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
