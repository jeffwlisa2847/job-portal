<?php
$jobTypes  = ['full_time'=>'Full-Time','part_time'=>'Part-Time','contract'=>'Contract','internship'=>'Internship','freelance'=>'Freelance'];
$expLevels = ['entry'=>'Entry Level','junior'=>'Junior','mid'=>'Mid-Level','senior'=>'Senior','lead'=>'Lead','executive'=>'Executive'];
$industries= ['Information Technology','Finance','Healthcare','Education','Sales & Marketing',
              'Engineering','Legal','Logistics','Design','Human Resources','Manufacturing','Real Estate','Other'];
?>
<!-- Search hero -->
<div style="background:linear-gradient(135deg,#1A56DB,#1e1b4b);padding:36px 0 28px;">
<div class="container">
  <h1 class="text-white fw-800 mb-3" style="font-size:1.7rem;">Find Your Next Job</h1>
  <form id="jobSearchForm" action="<?= url('/jobs') ?>" method="GET">
    <div class="row g-2">
      <div class="col-12 col-md-5">
        <div class="input-group">
          <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
          <input type="text" class="form-control border-0" name="q"
                 placeholder="Job title, keyword, company..."
                 value="<?= e($filters['q']) ?>" id="searchKeyword">
        </div>
      </div>
      <div class="col-12 col-md-4">
        <div class="input-group">
          <span class="input-group-text bg-white border-0"><i class="bi bi-geo-alt text-muted"></i></span>
          <input type="text" class="form-control border-0" name="location"
                 placeholder="City or country..."
                 value="<?= e($filters['location']) ?>">
        </div>
      </div>
      <div class="col-12 col-md-3">
        <button type="submit" class="btn btn-warning w-100 fw-700" style="height:46px;">
          <i class="bi bi-search me-1"></i> Search
          <span id="searchSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
        </button>
      </div>
    </div>
  </form>
</div>
</div>

<div class="container py-4">
<div class="row g-4">

<!-- FILTERS -->
<div class="col-lg-3">
  <div class="card border-0 shadow-sm sticky-top" style="top:80px;">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
      <h6 class="fw-700 mb-0">Filters</h6>
      <a href="<?= url('/jobs') ?>" class="btn btn-link btn-sm text-muted p-0 text-decoration-none small">Clear all</a>
    </div>
    <div class="card-body p-3">

      <!-- Job Type -->
      <div class="mb-3">
        <label class="form-label fw-700 small text-uppercase" style="letter-spacing:.5px;font-size:11px;">Job Type</label>
        <?php foreach($jobTypes as $v=>$l): ?>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="job_type" form="jobSearchForm"
                 value="<?= $v ?>" id="jt_<?= $v ?>"
                 <?= $filters['job_type']===$v?'checked':'' ?>
                 onchange="document.getElementById('jobSearchForm').dispatchEvent(new Event('submit'))">
          <label class="form-check-label small" for="jt_<?= $v ?>"><?= $l ?></label>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Experience -->
      <div class="mb-3">
        <label class="form-label fw-700 small text-uppercase" style="letter-spacing:.5px;font-size:11px;">Experience</label>
        <?php foreach($expLevels as $v=>$l): ?>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="experience_level" form="jobSearchForm"
                 value="<?= $v ?>" id="exp_<?= $v ?>"
                 <?= $filters['experience_level']===$v?'checked':'' ?>
                 onchange="document.getElementById('jobSearchForm').dispatchEvent(new Event('submit'))">
          <label class="form-check-label small" for="exp_<?= $v ?>"><?= $l ?></label>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Work Mode -->
      <div class="mb-3">
        <label class="form-label fw-700 small text-uppercase" style="letter-spacing:.5px;font-size:11px;">Work Mode</label>
        <?php foreach([''=> 'Any','1'=>'Remote Only','0'=>'On-site'] as $v=>$l): ?>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="is_remote" form="jobSearchForm"
                 value="<?= $v ?>" id="rm_<?= $v ?>"
                 <?= $filters['is_remote']===$v?'checked':'' ?>
                 onchange="document.getElementById('jobSearchForm').dispatchEvent(new Event('submit'))">
          <label class="form-check-label small" for="rm_<?= $v ?>"><?= $l ?></label>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Industry -->
      <div class="mb-2">
        <label class="form-label fw-700 small text-uppercase" style="letter-spacing:.5px;font-size:11px;">Industry</label>
        <select class="form-select form-select-sm" name="industry" form="jobSearchForm"
                onchange="document.getElementById('jobSearchForm').dispatchEvent(new Event('submit'))">
          <option value="">All Industries</option>
          <?php foreach($industries as $ind): ?>
          <option value="<?= $ind ?>" <?= $filters['industry']===$ind?'selected':'' ?>><?= $ind ?></option>
          <?php endforeach; ?>
        </select>
      </div>

    </div>
  </div>
</div>

<!-- RESULTS -->
<div class="col-lg-9">
  <!-- Toolbar -->
  <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="text-muted small">
      <span id="resultsCount">
        <?php if($paging['total']>0): ?>
          Showing <strong><?= count($jobs) ?></strong> of <strong><?= $paging['total'] ?></strong> jobs
          <?= $filters['q'] ? ' for "<strong>'.e($filters['q']).'</strong>"' : '' ?>
        <?php else: ?>
          No jobs found
        <?php endif; ?>
      </span>
    </div>
    <select class="form-select form-select-sm" style="width:auto;"
            onchange="window.location='<?= url('/jobs') ?>?'+new URLSearchParams({...Object.fromEntries(new URLSearchParams(location.search)),sort:this.value}).toString()">
      <option value="newest"      <?= ($filters['sort']??'')==='newest'     ?'selected':'' ?>>Newest First</option>
      <option value="salary_high" <?= ($filters['sort']??'')==='salary_high'?'selected':'' ?>>Highest Salary</option>
      <option value="salary_low"  <?= ($filters['sort']??'')==='salary_low' ?'selected':'' ?>>Lowest Salary</option>
    </select>
  </div>

  <!-- Results container -->
  <div id="jobResults">
    <?php if(empty($jobs)): ?>
    <div class="card border-0 shadow-sm text-center py-5">
      <div class="card-body">
        <i class="bi bi-search text-muted" style="font-size:3rem;"></i>
        <h5 class="fw-700 mt-3">No jobs found</h5>
        <p class="text-muted">Try different keywords or remove some filters.</p>
        <a href="<?= url('/jobs') ?>" class="btn btn-primary px-4">Clear Filters</a>
      </div>
    </div>
    <?php else: ?>
    <?php foreach($jobs as $job): ?>
    <div class="card border-0 shadow-sm job-card mb-3">
      <div class="card-body p-4">
        <div class="row align-items-center g-3">
          <div class="col-12 col-md-7 d-flex gap-3">
            <div class="logo-placeholder flex-shrink-0" style="width:52px;height:52px;font-size:20px;border-radius:12px;">
              <?php if($job['company_logo']): ?>
              <img src="<?= url('/file?path='.urlencode($job['company_logo'])) ?>"
                   style="width:52px;height:52px;border-radius:12px;object-fit:contain;
                          border:1px solid #e5e7eb;padding:4px;background:#fff;" alt="">
              <?php else: ?>
              <?= strtoupper(substr($job['company_name'],0,1)) ?>
              <?php endif; ?>
            </div>
            <div class="min-w-0">
              <h5 class="fw-700 mb-0 fs-6">
                <a href="<?= url('/jobs/'.$job['slug']) ?>" class="text-dark text-decoration-none">
                  <?= e($job['title']) ?>
                </a>
              </h5>
              <div class="text-muted small mb-1"><?= e($job['company_name']) ?></div>
              <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-light text-dark fw-500" style="font-size:11.5px;">
                  <i class="bi bi-geo-alt me-1"></i>
                  <?= $job['is_remote'] ? '<span class="text-success fw-600">Remote</span>' : e($job['location_city']??'N/A') ?>
                </span>
                <span class="badge bg-light text-dark fw-500" style="font-size:11.5px;"><?= status_label($job['job_type']) ?></span>
                <span class="badge bg-light text-dark fw-500" style="font-size:11.5px;"><?= status_label($job['experience_level']) ?></span>
                <?php if(!$job['salary_is_hidden']&&$job['salary_min']): ?>
                <span class="badge fw-500" style="background:#F0FDF4;color:#057A55;font-size:11.5px;">
                  <?= salary_range($job['salary_min'],$job['salary_max'],$job['salary_currency']) ?>
                </span>
                <?php endif; ?>
                <?php if($job['is_featured']): ?>
                <span class="badge bg-warning text-dark fw-600" style="font-size:11px;">⭐ Featured</span>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-5 d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
            <span class="text-muted small"><?= time_ago($job['created_at']) ?></span>
            <?php if(is_logged_in()&&Session::isSeeker()): ?>
            <form method="POST" action="<?= url(in_array($job['id'],$savedIds)?'/seeker/unsave-job/'.$job['id']:'/seeker/save-job/'.$job['id']) ?>"
                  data-no-loading style="position:relative;z-index:1;">
              <?= csrf_field() ?>
              <button type="submit" class="btn btn-sm btn-light" title="<?= in_array($job['id'],$savedIds)?'Unsave':'Save' ?>">
                <i class="bi bi-bookmark<?= in_array($job['id'],$savedIds)?'-fill text-primary':'' ?>"></i>
              </button>
            </form>
            <?php endif; ?>
            <a href="<?= url('/jobs/'.$job['slug']) ?>"
               class="btn btn-primary btn-sm px-3" style="position:relative;z-index:1;">View Job</a>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>

    <!-- Pagination -->
    <?php if($paging['pages']>1): ?>
    <nav class="mt-4"><ul class="pagination justify-content-center gap-1">
      <?php if($paging['current_page']>1): ?>
      <li class="page-item">
        <a class="page-link border-0" href="?<?= http_build_query(array_merge($filters,['page'=>$paging['current_page']-1])) ?>">
          <i class="bi bi-chevron-left"></i></a>
      </li>
      <?php endif; ?>
      <?php for($i=max(1,$paging['current_page']-2);$i<=min($paging['pages'],$paging['current_page']+2);$i++): ?>
      <li class="page-item <?= $i===$paging['current_page']?'active':'' ?>">
        <a class="page-link border-0" href="?<?= http_build_query(array_merge($filters,['page'=>$i])) ?>"><?= $i ?></a>
      </li>
      <?php endfor; ?>
      <?php if($paging['current_page']<$paging['pages']): ?>
      <li class="page-item">
        <a class="page-link border-0" href="?<?= http_build_query(array_merge($filters,['page'=>$paging['current_page']+1])) ?>">
          <i class="bi bi-chevron-right"></i></a>
      </li>
      <?php endif; ?>
    </ul></nav>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
</div>
</div>
<script src="<?= asset('js/job-search.js') ?>"></script>
