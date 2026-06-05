<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h4 fw-800 mb-0">Admin Dashboard</h1>
        <p class="text-muted small mb-0">Platform overview — <?= date('l, F j, Y') ?></p>
    </div>
    <?php if ($stats['pending_verify'] > 0): ?>
    <a href="<?= url('/admin/verifications') ?>" class="btn btn-warning fw-600">
        <i class="bi bi-shield-exclamation me-2"></i>
        <?= $stats['pending_verify'] ?> Pending Verification<?= $stats['pending_verify'] > 1 ? 's' : '' ?>
    </a>
    <?php endif; ?>
</div>

<!-- Stats grid -->
<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['label'=>'Total Users',     'value'=>number_format($stats['total_users']),     'icon'=>'people',          'color'=>'#EBF5FF', 'icolor'=>'#1A56DB', 'link'=>'/admin/users'],
        ['label'=>'Job Seekers',     'value'=>number_format($stats['total_seekers']),    'icon'=>'person-badge',    'color'=>'#F0FDF4', 'icolor'=>'#057A55', 'link'=>'/admin/users?role=seeker'],
        ['label'=>'Employers',       'value'=>number_format($stats['total_employers']), 'icon'=>'building',        'color'=>'#EDE9FE', 'icolor'=>'#7C3AED', 'link'=>'/admin/users?role=employer'],
        ['label'=>'Active Jobs',     'value'=>number_format($stats['active_jobs']),     'icon'=>'briefcase',       'color'=>'#FEF3C7', 'icolor'=>'#D97706', 'link'=>'/admin/jobs'],
        ['label'=>'Total Applies',   'value'=>number_format($stats['total_apps']),      'icon'=>'file-earmark-text','color'=>'#FEE2E2','icolor'=>'#DC2626', 'link'=>'/admin/reports'],
        ['label'=>'Pending Verif.',  'value'=>$stats['pending_verify'],                 'icon'=>'shield-check',    'color'=>'#FFF7ED', 'icolor'=>'#EA580C', 'link'=>'/admin/verifications'],
        ['label'=>'New Users Today', 'value'=>$stats['new_users_today'],               'icon'=>'person-plus',     'color'=>'#F0FDF4', 'icolor'=>'#059669', 'link'=>'/admin/users'],
        ['label'=>'Apps Today',      'value'=>$stats['apps_today'],                    'icon'=>'send',            'color'=>'#EFF6FF', 'icolor'=>'#2563EB', 'link'=>'/admin/reports'],
    ];
    foreach ($cards as $c): ?>
    <div class="col-6 col-md-3">
        <a href="<?= url($c['link']) ?>" class="text-decoration-none">
            <div class="card stat-card border-0 h-100" style="background:<?= $c['color'] ?>;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:rgba(0,0,0,.08);">
                        <i class="bi bi-<?= $c['icon'] ?>" style="color:<?= $c['icolor'] ?>;"></i>
                    </div>
                    <div>
                        <div class="stat-value" style="color:<?= $c['icolor'] ?>;"><?= $c['value'] ?></div>
                        <div class="stat-label"><?= $c['label'] ?></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <!-- Recent Users -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="fw-700 mb-0"><i class="bi bi-person-plus me-2 text-primary"></i>Recent Registrations</h6>
                <a href="<?= url('/admin/users') ?>" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($recentUsers as $u): ?>
                    <div class="list-group-item border-0 px-4 py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-700 flex-shrink-0"
                                 style="width:36px;height:36px;font-size:14px;
                                        background:<?= $u['role']==='admin' ? '#FEE2E2' : ($u['role']==='employer' ? '#EDE9FE' : '#EBF5FF') ?>;
                                        color:<?= $u['role']==='admin' ? '#DC2626' : ($u['role']==='employer' ? '#7C3AED' : '#1A56DB') ?>;">
                                <?= strtoupper(substr($u['full_name'], 0, 1)) ?>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-700 small"><?= e($u['full_name']) ?></div>
                                <div class="text-muted" style="font-size:12px;"><?= e($u['email']) ?></div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <span class="badge rounded-pill small"
                                      style="background:<?= $u['role']==='employer' ? '#EDE9FE' : '#EBF5FF' ?>;
                                             color:<?= $u['role']==='employer' ? '#7C3AED' : '#1A56DB' ?>;">
                                    <?= ucfirst($u['role']) ?>
                                </span>
                                <div class="text-muted mt-1" style="font-size:11px;"><?= time_ago($u['created_at']) ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Verifications -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="fw-700 mb-0">
                    <i class="bi bi-shield-check me-2 text-warning"></i>Pending Verifications
                </h6>
                <a href="<?= url('/admin/verifications') ?>" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($pendingVerifications)): ?>
                <div class="text-center py-4">
                    <i class="bi bi-check-circle text-success" style="font-size:2rem;"></i>
                    <p class="text-muted small mt-2 mb-0">All caught up! No pending verifications.</p>
                </div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($pendingVerifications as $cv): ?>
                    <div class="list-group-item border-0 px-4 py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-2 bg-light d-flex align-items-center justify-content-center fw-800 flex-shrink-0"
                                 style="width:40px;height:40px;font-size:16px;">
                                <?= strtoupper(substr($cv['company_name'], 0, 1)) ?>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-700 small"><?= e($cv['company_name']) ?></div>
                                <div class="text-muted" style="font-size:12px;"><?= e($cv['email']) ?></div>
                                <div class="text-muted" style="font-size:11px;"><?= time_ago($cv['created_at']) ?></div>
                            </div>
                            <a href="<?= url('/admin/verifications') ?>"
                               class="btn btn-sm btn-warning flex-shrink-0">Review</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
