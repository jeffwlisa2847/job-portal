<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Validator.php';
require_once ROOT_PATH . '/app/models/Job.php';
require_once ROOT_PATH . '/app/models/Application.php';

class JobController extends Controller
{
    private Job $jobs;

    public function __construct()
    {
        $this->jobs = new Job();
    }

    // ── Public: job search page ───────────────────────────────────────────────
public function index(): void
    {
        $filters = [
            'q'                => $this->query('q'),
            'location'         => $this->query('location'),
            'job_type'         => $this->query('job_type'),
            'experience_level' => $this->query('experience_level'),
            'industry'         => $this->query('industry'),
            'is_remote'        => $this->query('is_remote'),
            'salary_min'       => $this->query('salary_min'),
            'sort'             => $this->query('sort', 'newest'),
        ];

        $result = $this->jobs->search($filters, $this->currentPage(), 12);

        // ── AJAX request: return JSON ──────────────────────────────────────
        if ($this->isAjax() || $this->query('ajax') === '1') {
            $jobsForJson = array_map(function($job) {
                return [
                    'id'               => $job['id'],
                    'title'            => $job['title'],
                    'slug'             => $job['slug'],
                    'company_name'     => $job['company_name'],
                    'company_logo'     => $job['company_logo'] ?? null,
                    'location_city'    => $job['location_city'] ?? '',
                    'is_remote'        => (bool)$job['is_remote'],
                    'job_type'         => $job['job_type'],
                    'job_type_label'   => status_label($job['job_type']),
                    'experience_level' => $job['experience_level'],
                    'experience_label' => status_label($job['experience_level']),
                    'salary_display'   => (!$job['salary_is_hidden'] && $job['salary_min'])
                        ? salary_range($job['salary_min'],$job['salary_max'],$job['salary_currency'])
                        : '',
                    'is_featured'      => (bool)$job['is_featured'],
                    'created_at'       => $job['created_at'],
                    'time_ago'         => time_ago($job['created_at']),
                ];
            }, $result['data']);

            $this->json([
                'jobs'  => $jobsForJson,
                'total' => $result['total'],
                'pages' => $result['pages'],
            ]);
        }

        // ── Normal page render ─────────────────────────────────────────────
        $savedIds = [];
        if (is_logged_in() && Session::isSeeker()) {
            require_once ROOT_PATH . '/app/models/SeekerProfile.php';
            $profile = (new SeekerProfile())->getByUserId(Session::id());
            if ($profile) {
                $rows = Database::getInstance()->fetchAll(
                    "SELECT job_id FROM saved_jobs WHERE seeker_id = ?", [$profile['id']]
                );
                $savedIds = array_column($rows, 'job_id');
            }
        }

        $this->view('jobs/index', [
            'title'   => 'Find Jobs',
            'jobs'    => $result['data'],
            'paging'  => $result,
            'filters' => $filters,
            'savedIds'=> $savedIds,
        ]);
    }

    // ── Public: job detail page ───────────────────────────────────────────────
    public function show(array $params): void
    {
        $job = $this->jobs->getBySlug($params['slug'] ?? '');
        if (!$job || $job['status'] === 'removed') $this->abort(404);

        // Increment view count
        Database::getInstance()->query(
            "UPDATE job_listings SET view_count = view_count + 1 WHERE id = ?", [$job['id']]
        );

        $skills  = $this->jobs->getSkills($job['id']);
        $related = $this->jobs->getRelated($job['id'], $job['industry'] ?? '', 3);

        // Check if seeker already applied or saved
        $hasApplied = false;
        $isSaved    = false;
        $profile    = null;

        if (is_logged_in() && Session::isSeeker()) {
            require_once ROOT_PATH . '/app/models/SeekerProfile.php';
            require_once ROOT_PATH . '/app/models/Application.php';
            $profile    = (new SeekerProfile())->getByUserId(Session::id());
            if ($profile) {
                $hasApplied = (new Application())->hasApplied($profile['id'], $job['id']);
                $isSaved    = (bool)Database::getInstance()->fetchColumn(
                    "SELECT id FROM saved_jobs WHERE seeker_id = ? AND job_id = ?",
                    [$profile['id'], $job['id']]
                );
            }
        }

        $this->view('jobs/show', [
            'title'      => $job['title'] . ' — ' . $job['company_name'],
            'job'        => $job,
            'skills'     => $skills,
            'related'    => $related,
            'hasApplied' => $hasApplied,
            'isSaved'    => $isSaved,
            'profile'    => $profile,
        ]);
    }

    // ── Employer: list own jobs ───────────────────────────────────────────────
    public function employerIndex(): void
    {
        $this->requireRole('employer');
        $ep = $this->employerProfile();

        $result = $this->jobs->getForEmployer($ep['id'], $this->currentPage(), 15);

        $this->view('employer/jobs', [
            'title'  => 'My Job Listings',
            'jobs'   => $result['data'],
            'paging' => $result,
            'ep'     => $ep,
        ], 'employer');
    }

    // ── Employer: create job form ─────────────────────────────────────────────
    public function create(): void
    {
        $this->requireRole('employer');
        $ep = $this->employerProfile();

        if ($ep['verification_status'] !== 'approved') {
            $this->flash('error', 'Your company profile must be approved before you can post jobs.');
            $this->redirect(BASE_URL . '/employer/profile');
            return;
        }

        $this->view('employer/job-form', [
            'title'  => 'Post a New Job',
            'job'    => [],
            'ep'     => $ep,
            'mode'   => 'create',
        ], 'employer');
    }

    // ── Employer: store new job ───────────────────────────────────────────────
    public function store(): void
    {
        $this->requireRole('employer');
        $this->verifyCsrf();

        $v = Validator::make($_POST, [
            'title'       => 'required|min:3|max:160',
            'description' => 'required|min:50',
            'job_type'    => 'required|in:full_time,part_time,contract,internship,freelance,volunteer',
            'experience_level' => 'required|in:entry,junior,mid,senior,lead,executive',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->first());
            $this->back();
            return;
        }

        $ep    = $this->employerProfile();
        $jobId = $this->jobs->createListing($ep['id'], $_POST);

        $this->flash('success', '✓ Job listing published successfully!');
        $this->redirect(BASE_URL . '/employer/jobs');
    }

    // ── Employer: edit job form ───────────────────────────────────────────────
    public function edit(array $params): void
    {
        $this->requireRole('employer');
        $ep  = $this->employerProfile();
        $job = $this->ownJob((int)$params['id'], $ep['id']);

        $skills = $this->jobs->getSkills($job['id']);
        $job['skills_csv'] = implode(', ', array_column($skills, 'skill_name'));

        $this->view('employer/job-form', [
            'title' => 'Edit Job — ' . $job['title'],
            'job'   => $job,
            'ep'    => $ep,
            'mode'  => 'edit',
        ], 'employer');
    }

    // ── Employer: update job ──────────────────────────────────────────────────
    public function update(array $params): void
    {
        $this->requireRole('employer');
        $this->verifyCsrf();

        $ep  = $this->employerProfile();
        $job = $this->ownJob((int)$params['id'], $ep['id']);

        $v = Validator::make($_POST, [
            'title'       => 'required|min:3|max:160',
            'description' => 'required|min:50',
            'job_type'    => 'required',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->first());
            $this->back();
            return;
        }

        $this->jobs->updateListing($job['id'], $_POST);
        $this->flash('success', 'Job listing updated!');
        $this->redirect(BASE_URL . '/employer/jobs');
    }

    // ── Employer: close / repost / delete ─────────────────────────────────────
    public function close(array $params): void
    {
        $this->requireRole('employer');
        $this->verifyCsrf();
        $ep = $this->employerProfile();
        $this->jobs->close((int)$params['id'], $ep['id']);
        $this->flash('success', 'Job listing closed.');
        $this->redirect(BASE_URL . '/employer/jobs');
    }

    public function repost(array $params): void
    {
        $this->requireRole('employer');
        $this->verifyCsrf();
        $ep = $this->employerProfile();
        $this->jobs->repost((int)$params['id'], $ep['id']);
        $this->flash('success', 'Job listing reposted for 30 more days!');
        $this->redirect(BASE_URL . '/employer/jobs');
    }

    public function destroy(array $params): void
    {
        $this->requireRole('employer');
        $this->verifyCsrf();
        $ep = $this->employerProfile();
        $this->jobs->destroy((int)$params['id'], $ep['id']);
        $this->flash('success', 'Job listing deleted.');
        $this->redirect(BASE_URL . '/employer/jobs');
    }

    // ── Employer: job analytics ───────────────────────────────────────────────
    public function analytics(array $params): void
    {
        $this->requireRole('employer');
        $ep  = $this->employerProfile();
        $job = $this->ownJob((int)$params['id'], $ep['id']);
        $stats = $this->jobs->getAnalytics($job['id']);

        $this->view('employer/job-analytics', [
            'title' => 'Analytics — ' . $job['title'],
            'job'   => $job,
            'stats' => $stats,
        ], 'employer');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    private function employerProfile(): array
    {
        $ep = Database::getInstance()->fetchOne(
            "SELECT * FROM employer_profiles WHERE user_id = ? LIMIT 1", [Session::id()]
        );
        if (!$ep) {
            $this->flash('error', 'Employer profile not found.');
            $this->redirect(BASE_URL . '/employer/profile');
            exit;
        }
        return $ep;
    }

    private function ownJob(int $jobId, int $employerProfileId): array
    {
        $job = $this->db()->fetchOne(
            "SELECT * FROM job_listings WHERE id = ? AND employer_id = ? LIMIT 1",
            [$jobId, $employerProfileId]
        );
        if (!$job) $this->abort(404);
        return $job;
    }

    private function db(): Database { return Database::getInstance(); }
}
