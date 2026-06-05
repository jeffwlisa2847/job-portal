<style>
.hero{background:linear-gradient(135deg,#1A56DB 0%,#1347c8 55%,#1e1b4b 100%);
      min-height:480px;display:flex;align-items:center;position:relative;overflow:hidden;}
.hero::before{content:'';position:absolute;inset:0;
  background:url("data:image/svg+xml,%3Csvg width='60' height='60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23fff' fill-opacity='.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E");}
.search-wrap{background:#fff;border-radius:14px;padding:6px;
             box-shadow:0 8px 32px rgba(0,0,0,.25);max-width:700px;margin:0 auto;}
.search-wrap input{border:none;outline:none;font-size:15px;background:transparent;padding:8px 12px;}
.search-wrap .btn{border-radius:10px;height:46px;font-size:15px;}
.stat-item{text-align:center;padding:0 20px;}
.stat-item .num{font-size:1.8rem;font-weight:800;line-height:1;}
.stat-item .lbl{font-size:12px;opacity:.7;margin-top:2px;}
.cat-card{border:1.5px solid #e5e7eb;border-radius:14px;padding:18px 14px;
          text-align:center;background:#fff;text-decoration:none;color:inherit;
          display:block;transition:.2s;}
.cat-card:hover{border-color:#1A56DB;background:#EBF5FF;color:#1A56DB;
                transform:translateY(-2px);box-shadow:0 6px 20px rgba(26,86,219,.12);}
.job-card-home{border:1px solid #e5e7eb;border-radius:14px;padding:20px;
               background:#fff;transition:.2s;text-decoration:none;
               color:inherit;display:block;height:100%;}
.job-card-home:hover{border-color:#1A56DB;box-shadow:0 6px 20px rgba(26,86,219,.1);
                     transform:translateY(-2px);}
.company-row{border:1px solid #e5e7eb;border-radius:12px;padding:16px 20px;
             background:#fff;display:flex;align-items:center;gap:16px;
             text-decoration:none;color:inherit;transition:.2s;}
.company-row:hover{border-color:#1A56DB;box-shadow:0 4px 16px rgba(26,86,219,.1);}
.sec-label{font-size:12px;font-weight:700;letter-spacing:1.5px;
           text-transform:uppercase;color:#1A56DB;margin-bottom:6px;}
</style>

<!-- HERO -->
<section class="hero text-white">
<div class="container py-5 position-relative">
  <div class="row justify-content-center text-center">
    <div class="col-12 col-lg-9">
      <div class="mb-3">
        <span class="badge px-3 py-2 fw-600"
              style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);
                     border-radius:50px;font-size:13px;">
          🚀 <?= number_format($stats['active_jobs']) ?> Active Jobs Available
        </span>
      </div>
      <h1 class="fw-800 mb-3"
          style="font-size:clamp(1.8rem,5vw,2.8rem);letter-spacing:-1px;line-height:1.15;">
        Find Your <span style="color:#93c5fd;">Dream Job</span><br>
        or Hire Top Talent
      </h1>
      <p class="mb-4" style="opacity:.8;font-size:1.05rem;max-width:520px;margin:0 auto 1.5rem;">
        Ghana's most trusted job platform — thousands of opportunities across every industry.
      </p>

      <!-- Search -->
      <form action="<?= url('/jobs') ?>" method="GET" class="mb-4 px-2">
        <div class="search-wrap d-flex align-items-center gap-2 flex-wrap flex-md-nowrap">
          <div class="d-flex align-items-center flex-grow-1 gap-1">
            <i class="bi bi-search text-muted ms-2"></i>
            <input type="text" name="q" class="flex-grow-1"
                   placeholder="Job title, keyword, or company..."
                   value="<?= e($_GET['q'] ?? '') ?>">
          </div>
          <div class="d-flex align-items-center border-start ps-2 flex-grow-1 gap-1">
            <i class="bi bi-geo-alt text-muted"></i>
            <input type="text" name="location" class="flex-grow-1"
                   placeholder="City or country..."
                   value="<?= e($_GET['location'] ?? '') ?>">
          </div>
          <button type="submit" class="btn btn-primary fw-700 px-4" style="white-space:nowrap;">
            Search Jobs
          </button>
        </div>
      </form>

      <!-- Stats -->
      <div class="d-flex flex-wrap justify-content-center gap-4">
        <?php foreach([
          ['num'=>number_format($stats['active_jobs']), 'lbl'=>'Active Jobs'],
          ['num'=>number_format($stats['companies']),   'lbl'=>'Companies'],
          ['num'=>number_format($stats['seekers']),     'lbl'=>'Job Seekers'],
          ['num'=>number_format($stats['placements']),  'lbl'=>'Placements'],
        ] as $s): ?>
        <div class="stat-item">
          <div class="num"><?= $s['num'] ?></div>
          <div class="lbl"><?= $s['lbl'] ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
</section>

<!-- CATEGORIES -->
<?php if(!empty($categories)): ?>
<section class="py-5 bg-white">
<div class="container">
  <div class="text-center mb-4">
    <div class="sec-label">Explore Opportunities</div>
    <h2 class="fw-800" style="letter-spacing:-.5px;">Browse by Category</h2>
  </div>
  <?php $icons=['Information Technology'=>'💻','Finance'=>'📊','Healthcare'=>'🏥',
    'Education'=>'📚','Sales & Marketing'=>'📣','Engineering'=>'⚙️',
    'Legal'=>'⚖️','Logistics'=>'🚚','Design'=>'🎨','Other'=>'💼']; ?>
  <div class="row g-3">
    <?php foreach($categories as $cat): ?>
    <div class="col-6 col-md-3">
      <a href="<?= url('/jobs?industry='.urlencode($cat['industry'])) ?>" class="cat-card">
        <div style="font-size:1.8rem;margin-bottom:6px;"><?= $icons[$cat['industry']]??'💼' ?></div>
        <div class="fw-700 small"><?= e($cat['industry']) ?></div>
        <div class="text-muted" style="font-size:11px;"><?= $cat['job_count'] ?> jobs</div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
</div>
</section>
<?php endif; ?>

<!-- FEATURED JOBS -->
<?php if(!empty($featuredJobs)): ?>
<section class="py-5" style="background:#F9FAFB;">
<div class="container">
  <div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <div class="sec-label">Latest Opportunities</div>
      <h2 class="fw-800 mb-0" style="letter-spacing:-.5px;">Featured Jobs</h2>
    </div>
    <a href="<?= url('/jobs') ?>" class="btn btn-outline-primary fw-600">
      View All <i class="bi bi-arrow-right ms-1"></i>
    </a>
  </div>
  <div class="row g-3">
    <?php foreach($featuredJobs as $job): ?>
    <div class="col-12 col-md-6 col-xl-4">
      <a href="<?= url('/jobs/'.$job['slug']) ?>" class="job-card-home">
        <div class="d-flex gap-3 mb-3">
          <?php if($job['company_logo']): ?>
          <img src="<?= url('/file?path='.urlencode($job['company_logo'])) ?>"
               style="width:48px;height:48px;border-radius:10px;object-fit:contain;
                      border:1px solid #e5e7eb;padding:4px;background:#fff;flex-shrink:0;" alt="">
          <?php else: ?>
          <div class="d-flex align-items-center justify-content-center fw-800 flex-shrink-0"
               style="width:48px;height:48px;border-radius:10px;background:#EBF5FF;
                      color:#1A56DB;font-size:18px;">
            <?= strtoupper(substr($job['company_name'],0,1)) ?>
          </div>
          <?php endif; ?>
          <div class="min-w-0">
            <div class="fw-700 text-dark text-truncate"><?= e($job['title']) ?></div>
            <div class="text-muted small"><?= e($job['company_name']) ?></div>
          </div>
          <?php if($job['is_featured']): ?>
          <span class="badge bg-warning text-dark ms-auto flex-shrink-0"
                style="font-size:10px;height:fit-content;">⭐ Featured</span>
          <?php endif; ?>
        </div>
        <div class="d-flex flex-wrap gap-2 mb-3">
          <span class="badge bg-light text-dark fw-500" style="font-size:11px;">
            <i class="bi bi-geo-alt me-1"></i>
            <?= $job['is_remote'] ? '<span class="text-success fw-600">Remote</span>' : e($job['location_city']??'N/A') ?>
          </span>
          <span class="badge bg-light text-dark fw-500" style="font-size:11px;">
            <?= status_label($job['job_type']) ?>
          </span>
          <span class="badge bg-light text-dark fw-500" style="font-size:11px;">
            <?= status_label($job['experience_level']) ?>
          </span>
        </div>
        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
          <span class="fw-700 small" style="color:#057A55;">
            <?= (!$job['salary_is_hidden'] && $job['salary_min'])
              ? salary_range($job['salary_min'],$job['salary_max'],$job['salary_currency'])
              : 'Competitive' ?>
          </span>
          <span class="text-muted small"><?= time_ago($job['created_at']) ?></span>
        </div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
</div>
</section>
<?php endif; ?>

<!-- TOP COMPANIES -->
<?php if(!empty($topCompanies)): ?>
<section class="py-5 bg-white">
<div class="container">
  <div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <div class="sec-label">Top Employers</div>
      <h2 class="fw-800 mb-0" style="letter-spacing:-.5px;">Hiring Companies</h2>
    </div>
    <a href="<?= url('/companies') ?>" class="btn btn-outline-primary fw-600">
      All Companies <i class="bi bi-arrow-right ms-1"></i>
    </a>
  </div>
  <div class="row g-3">
    <?php foreach($topCompanies as $co):
      $slug = $co['slug'] ?? (slug($co['company_name']).'-'.$co['id']); ?>
    <div class="col-12 col-md-6">
      <a href="<?= url('/companies/'.$slug) ?>" class="company-row">
        <?php if($co['logo_path']): ?>
        <img src="<?= url('/file?path='.urlencode($co['logo_path'])) ?>"
             style="width:52px;height:52px;border-radius:12px;object-fit:contain;
                    border:1px solid #e5e7eb;padding:5px;background:#fff;flex-shrink:0;" alt="">
        <?php else: ?>
        <div class="d-flex align-items-center justify-content-center fw-800 flex-shrink-0"
             style="width:52px;height:52px;border-radius:12px;background:#EBF5FF;
                    color:#1A56DB;font-size:20px;">
          <?= strtoupper(substr($co['company_name'],0,1)) ?>
        </div>
        <?php endif; ?>
        <div class="flex-grow-1 min-w-0">
          <div class="fw-700 text-truncate"><?= e($co['company_name']) ?></div>
          <div class="text-muted small">
            <?= e($co['industry']??'') ?>
            <?php if($co['location_city']): ?>· <i class="bi bi-geo-alt"></i> <?= e($co['location_city']) ?><?php endif; ?>
          </div>
        </div>
        <?php if($co['job_count']>0): ?>
        <span class="badge fw-600 flex-shrink-0"
              style="background:#F0FDF4;color:#057A55;font-size:11px;">
          <?= $co['job_count'] ?> open <?= $co['job_count']==1?'job':'jobs' ?>
        </span>
        <?php endif; ?>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
</div>
</section>
<?php endif; ?>

<!-- HOW IT WORKS -->
<section class="py-5" style="background:#F9FAFB;">
<div class="container">
  <div class="text-center mb-5">
    <div class="sec-label">Simple Process</div>
    <h2 class="fw-800" style="letter-spacing:-.5px;">How It Works</h2>
  </div>
  <div class="row g-4 text-center">
    <?php foreach([
      ['icon'=>'person-plus','color'=>'#EBF5FF','ic'=>'#1A56DB','n'=>'01',
       'title'=>'Create Account','desc'=>'Register as a job seeker or employer in 2 minutes.'],
      ['icon'=>'file-earmark-person','color'=>'#F0FDF4','ic'=>'#057A55','n'=>'02',
       'title'=>'Build Profile','desc'=>'Upload resume, add skills and showcase experience.'],
      ['icon'=>'search','color'=>'#EDE9FE','ic'=>'#7C3AED','n'=>'03',
       'title'=>'Find & Apply','desc'=>'Search thousands of jobs and apply with one click.'],
      ['icon'=>'trophy','color'=>'#FEF3C7','ic'=>'#D97706','n'=>'04',
       'title'=>'Get Hired','desc'=>'Track applications and land your dream job.'],
    ] as $s): ?>
    <div class="col-6 col-md-3">
      <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
           style="width:68px;height:68px;background:<?= $s['color'] ?>;">
        <i class="bi bi-<?= $s['icon'] ?>" style="font-size:1.6rem;color:<?= $s['ic'] ?>;"></i>
      </div>
      <div class="fw-800 small" style="color:<?= $s['ic'] ?>;letter-spacing:1px;font-size:11px;">
        STEP <?= $s['n'] ?>
      </div>
      <h6 class="fw-700 mt-1 mb-1"><?= $s['title'] ?></h6>
      <p class="text-muted small mb-0"><?= $s['desc'] ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</div>
</section>

<!-- CTA -->
<section class="py-5" style="background:linear-gradient(135deg,#1A56DB,#1e1b4b);">
<div class="container text-center text-white py-2">
  <h2 class="fw-800 mb-3" style="font-size:clamp(1.4rem,4vw,2rem);">
    Ready to take the next step?
  </h2>
  <p class="mb-4" style="opacity:.8;">
    Join <?= number_format($stats['seekers']+$stats['companies']) ?>+ professionals on <?= APP_NAME ?>
  </p>
  <div class="d-flex gap-3 justify-content-center flex-wrap">
    <?php if(!is_logged_in()): ?>
    <a href="<?= url('/register?role=seeker') ?>"
       class="btn btn-warning btn-lg fw-700 px-5">
      <i class="bi bi-person-plus me-2"></i>Find a Job
    </a>
    <a href="<?= url('/register?role=employer') ?>"
       class="btn btn-outline-light btn-lg fw-700 px-5">
      <i class="bi bi-building me-2"></i>Post a Job
    </a>
    <?php else: ?>
    <a href="<?= url('/'.auth()['role'].'/dashboard') ?>"
       class="btn btn-warning btn-lg fw-700 px-5">
      <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
    </a>
    <a href="<?= url('/jobs') ?>"
       class="btn btn-outline-light btn-lg fw-700 px-5">
      <i class="bi bi-search me-2"></i>Browse Jobs
    </a>
    <?php endif; ?>
  </div>
</div>
</section>
