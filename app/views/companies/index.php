<?php
$sizes = ['1-10','11-50','51-200','201-500','501-1000','1000+'];
?>

<!-- Header banner -->
<div style="background:linear-gradient(135deg,#1A56DB,#1e1b4b);padding:40px 0 36px;">
    <div class="container">
        <h1 class="text-white fw-800 mb-2" style="font-size:1.8rem;">Browse Companies</h1>
        <p class="text-white mb-4" style="opacity:.75;">
            Discover <?= number_format($paging['total']) ?> verified companies hiring right now.
        </p>
        <form action="<?= url('/companies') ?>" method="GET">
            <div class="row g-2" style="max-width:600px;">
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-0"
                               name="q" placeholder="Search company name or description..."
                               value="<?= e($filters['search']) ?>">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-warning fw-700" style="height:46px;min-width:80px;">
                        Search
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="container py-4">
<div class="row g-4">

<!-- ── FILTERS SIDEBAR ──────────────────────────────────────────────────── -->
<div class="col-lg-3">
    <div class="card border-0 shadow-sm sticky-top" style="top:80px;">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between">
            <h6 class="fw-700 mb-0">Filters</h6>
            <a href="<?= url('/companies') ?>" class="btn btn-sm btn-link text-muted p-0 text-decoration-none">
                Clear
            </a>
        </div>
        <div class="card-body">
            <form action="<?= url('/companies') ?>" method="GET" id="filterForm">
                <input type="hidden" name="q" value="<?= e($filters['search']) ?>">

                <!-- Industry -->
                <div class="mb-4">
                    <label class="form-label fw-700 small text-uppercase" style="letter-spacing:.5px;">
                        Industry
                    </label>
                    <?php foreach ($industries as $ind): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="industry"
                               id="ind_<?= slug($ind['industry']) ?>"
                               value="<?= e($ind['industry']) ?>"
                               <?= $filters['industry'] === $ind['industry'] ? 'checked' : '' ?>
                               onchange="this.form.submit()">
                        <label class="form-check-label small" for="ind_<?= slug($ind['industry']) ?>">
                            <?= e($ind['industry']) ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Company size -->
                <div class="mb-3">
                    <label class="form-label fw-700 small text-uppercase" style="letter-spacing:.5px;">
                        Company Size
                    </label>
                    <?php foreach ($sizes as $s): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="size"
                               id="size_<?= str_replace(['+','-'],'_',$s) ?>"
                               value="<?= $s ?>"
                               <?= $filters['size'] === $s ? 'checked' : '' ?>
                               onchange="this.form.submit()">
                        <label class="form-check-label small"
                               for="size_<?= str_replace(['+','-'],'_',$s) ?>">
                            <?= $s ?> employees
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── COMPANY CARDS ─────────────────────────────────────────────────────── -->
<div class="col-lg-9">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="text-muted small mb-0">
            Showing <strong><?= count($companies) ?></strong> of
            <strong><?= number_format($paging['total']) ?></strong> companies
            <?= $filters['search'] ? ' for "<strong>' . e($filters['search']) . '</strong>"' : '' ?>
        </p>
    </div>

    <?php if (empty($companies)): ?>
    <div class="card border-0 shadow-sm text-center py-5">
        <div class="card-body">
            <i class="bi bi-building text-muted" style="font-size:3rem;"></i>
            <h5 class="fw-700 mt-3">No companies found</h5>
            <p class="text-muted">Try different filters or a broader search term.</p>
            <a href="<?= url('/companies') ?>" class="btn btn-primary px-4">Clear Filters</a>
        </div>
    </div>
    <?php else: ?>

    <div class="row g-3">
        <?php foreach ($companies as $co): ?>
        <div class="col-12 col-md-6">
            <a href="<?= url('/companies/' . ($co['slug'] ?? slug($co['company_name']) . '-' . $co['id'])) ?>"
               class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 card-hover"
                 style="transition:.2s;border-left:3px solid transparent !important;"
                 onmouseover="this.style.borderLeftColor='#1A56DB'"
                 onmouseout="this.style.borderLeftColor='transparent'">
                <div class="card-body p-4">
                    <div class="d-flex gap-3 mb-3">
                        <!-- Logo -->
                        <div class="flex-shrink-0" style="width:60px;height:60px;">
                            <?php if ($co['logo_path']): ?>
                            <img src="<?= url('/file?path=' . urlencode($co['logo_path'])) ?>"
                                 alt="<?= e($co['company_name']) ?>"
                                 style="width:60px;height:60px;border-radius:12px;
                                        object-fit:contain;border:1px solid #e5e7eb;
                                        padding:6px;background:#fff;">
                            <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center fw-800 rounded-3"
                                 style="width:60px;height:60px;font-size:22px;
                                        background:#EBF5FF;color:#1A56DB;">
                                <?= strtoupper(substr($co['company_name'], 0, 1)) ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-start gap-2">
                                <h6 class="fw-800 mb-0 text-dark">
                                    <?= e($co['company_name']) ?>
                                </h6>
                                <?php if ($co['is_featured']): ?>
                                <span class="badge bg-warning text-dark flex-shrink-0"
                                      style="font-size:10px;">Featured</span>
                                <?php endif; ?>
                            </div>
                            <div class="text-muted small mt-1">
                                <?php if ($co['industry']): ?>
                                <span><?= e($co['industry']) ?></span>
                                <?php endif; ?>
                                <?php if ($co['location_city']): ?>
                                <span class="mx-1">·</span>
                                <i class="bi bi-geo-alt"></i>
                                <?= e($co['location_city']) ?>
                                <?= $co['location_country'] ? ', ' . e($co['location_country']) : '' ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($co['description']): ?>
                    <p class="text-muted small mb-3" style="line-height:1.6;">
                        <?= e(truncate($co['description'], 100)) ?>
                    </p>
                    <?php endif; ?>

                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex flex-wrap gap-2">
                            <?php if ($co['company_size']): ?>
                            <span class="badge bg-light text-dark fw-500" style="font-size:11px;">
                                <i class="bi bi-people me-1"></i><?= e($co['company_size']) ?> employees
                            </span>
                            <?php endif; ?>
                            <?php if ($co['active_job_count'] > 0): ?>
                            <span class="badge fw-600"
                                  style="background:#F0FDF4;color:#057A55;font-size:11px;">
                                <i class="bi bi-briefcase me-1"></i>
                                <?= $co['active_job_count'] ?> open job<?= $co['active_job_count'] > 1 ? 's' : '' ?>
                            </span>
                            <?php else: ?>
                            <span class="badge bg-light text-muted fw-500" style="font-size:11px;">
                                No open positions
                            </span>
                            <?php endif; ?>
                        </div>
                        <span class="text-primary small fw-600">
                            View <i class="bi bi-arrow-right ms-1"></i>
                        </span>
                    </div>
                </div>
            </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($paging['pages'] > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center gap-1">
            <?php if ($paging['current_page'] > 1): ?>
            <li class="page-item">
                <a class="page-link border-0"
                   href="?<?= http_build_query(array_merge($filters, ['page' => $paging['current_page'] - 1])) ?>">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
            <?php endif; ?>
            <?php for ($i = max(1, $paging['current_page'] - 2);
                       $i <= min($paging['pages'], $paging['current_page'] + 2); $i++): ?>
            <li class="page-item <?= $i === $paging['current_page'] ? 'active' : '' ?>">
                <a class="page-link border-0"
                   href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>">
                    <?= $i ?>
                </a>
            </li>
            <?php endfor; ?>
            <?php if ($paging['current_page'] < $paging['pages']): ?>
            <li class="page-item">
                <a class="page-link border-0"
                   href="?<?= http_build_query(array_merge($filters, ['page' => $paging['current_page'] + 1])) ?>">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
    <?php endif; ?>

</div>
</div>
</div>
