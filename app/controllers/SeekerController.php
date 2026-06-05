<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Validator.php';
require_once ROOT_PATH . '/app/models/SeekerProfile.php';
require_once ROOT_PATH . '/app/models/Application.php';
require_once ROOT_PATH . '/app/helpers/FileUpload.php';

class SeekerController extends Controller
{
    private SeekerProfile $profiles;
    private Application   $apps;

    public function __construct()
    {
        $this->requireRole('seeker');
        $this->profiles = new SeekerProfile();
        $this->apps     = new Application();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    private function seekerProfile(): array
    {
        $p = $this->profiles->getByUserId(Session::id());
        if (!$p) $this->abort(404);
        return $p;
    }

    // =========================================================================
    // DASHBOARD
    // =========================================================================
    public function dashboard(): void
    {
        $profile  = $this->seekerProfile();
        $seekerId = $profile['id'];

        $statusCounts = $this->apps->countByStatus($seekerId);
        $recentApps   = $this->apps->getRecent($seekerId, 5);

        $skills  = $this->profiles->getSkills($seekerId);
        $exp     = $this->profiles->getExperience($seekerId);
        $edu     = $this->profiles->getEducation($seekerId);
        $score   = $this->profiles->completionScore($profile, $skills, $exp, $edu);

        $this->view('seeker/dashboard', [
            'title'        => 'My Dashboard',
            'profile'      => $profile,
            'statusCounts' => $statusCounts,
            'recentApps'   => $recentApps,
            'score'        => $score,
            'skills'       => $skills,
        ], 'seeker');
    }

    // =========================================================================
    // PROFILE
    // =========================================================================
    public function profile(): void
    {
        $profile  = $this->seekerProfile();
        $seekerId = $profile['id'];

        $skills = $this->profiles->getSkills($seekerId);
        $exp    = $this->profiles->getExperience($seekerId);
        $edu    = $this->profiles->getEducation($seekerId);
        $score  = $this->profiles->completionScore($profile, $skills, $exp, $edu);

        $this->view('seeker/profile', [
            'title'   => 'My Profile',
            'profile' => $profile,
            'skills'  => $skills,
            'exp'     => $exp,
            'edu'     => $edu,
            'score'   => $score,
        ], 'seeker');
    }

    public function updateProfile(): void
    {
        $this->verifyCsrf();

        $v = Validator::make($_POST, [
            'headline'         => 'max:160',
            'bio'              => 'max:1000',
            'phone'            => 'max:20',
            'location_city'    => 'max:80',
            'location_country' => 'max:80',
            'years_experience' => 'numeric|min_val:0|max_val:50',
            'linkedin_url'     => 'max:300',
            'github_url'       => 'max:300',
            'portfolio_url'    => 'max:300',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->first());
            $this->back();
            return;
        }

        $fields = [
            'headline', 'bio', 'phone', 'location_city', 'location_country',
            'years_experience', 'expected_salary_min', 'expected_salary_max',
            'salary_currency', 'linkedin_url', 'github_url', 'portfolio_url',
            'is_open_to_work',
        ];

        $data = [];
        foreach ($fields as $f) {
            if (isset($_POST[$f])) {
                $data[$f] = trim($_POST[$f]) === '' ? null : trim($_POST[$f]);
            }
        }
        $data['is_open_to_work'] = isset($_POST['is_open_to_work']) ? 1 : 0;

        // Also update full_name in users table
        if (!empty($_POST['full_name'])) {
            $db = Database::getInstance();
            $db->update('users',
                ['full_name' => trim($_POST['full_name'])],
                'id = ?', [Session::id()]
            );
            // Refresh session name
            $_SESSION['user']['name'] = trim($_POST['full_name']);
        }

        $this->profiles->updateByUserId(Session::id(), $data);
        $this->flash('success', 'Profile updated successfully!');
        $this->redirect(BASE_URL . '/seeker/profile');
    }

    // =========================================================================
    // AVATAR UPLOAD
    // =========================================================================
    public function uploadAvatar(): void
    {
        $this->verifyCsrf();

        if (empty($_FILES['avatar']) || $_FILES['avatar']['error'] === UPLOAD_ERR_NO_FILE) {
            $this->flash('error', 'Please select an image to upload.');
            $this->back();
            return;
        }

        $result = FileUpload::upload($_FILES['avatar'], 'avatars');

        if (!$result['ok']) {
            $this->flash('error', $result['error']);
            $this->back();
            return;
        }

        // Delete old avatar
        $profile = $this->seekerProfile();
        if ($profile['avatar_path']) {
            FileUpload::delete($profile['avatar_path']);
        }

        $this->profiles->updateAvatar(Session::id(), $result['path']);
        $this->flash('success', 'Profile photo updated!');
        $this->redirect(BASE_URL . '/seeker/profile');
    }

    // =========================================================================
    // RESUME UPLOAD
    // =========================================================================
    public function uploadResume(): void
    {
        $this->verifyCsrf();

        if (empty($_FILES['resume']) || $_FILES['resume']['error'] === UPLOAD_ERR_NO_FILE) {
            $this->flash('error', 'Please select a file to upload.');
            $this->back();
            return;
        }

        $result = FileUpload::upload($_FILES['resume'], 'resumes');

        if (!$result['ok']) {
            $this->flash('error', $result['error']);
            $this->back();
            return;
        }

        // Delete old resume
        $profile = $this->seekerProfile();
        if ($profile['resume_path']) {
            FileUpload::delete($profile['resume_path']);
        }

        $this->profiles->updateResume(
            Session::id(),
            $result['path'],
            $result['original_name']
        );

        $this->flash('success', 'Resume uploaded successfully!');
        $this->redirect(BASE_URL . '/seeker/profile');
    }

    // =========================================================================
    // SKILLS
    // =========================================================================
    public function addSkill(): void
    {
        $this->verifyCsrf();

        $skill = trim($this->input('skill_name'));
        $prof  = $this->input('proficiency', 'intermediate');

        if (empty($skill)) {
            $this->flash('error', 'Skill name cannot be empty.');
            $this->back();
            return;
        }

        if (strlen($skill) > 80) {
            $this->flash('error', 'Skill name is too long (max 80 characters).');
            $this->back();
            return;
        }

        $allowed = ['beginner', 'intermediate', 'advanced', 'expert'];
        if (!in_array($prof, $allowed)) $prof = 'intermediate';

        $profile = $this->seekerProfile();
        $this->profiles->addSkill($profile['id'], $skill, $prof);

        $this->flash('success', "Skill \"{$skill}\" added!");
        $this->redirect(BASE_URL . '/seeker/profile#skills');
    }

    public function deleteSkill(): void
    {
        $this->verifyCsrf();
        $profile = $this->seekerProfile();
        $skillId = (int)$this->input('skill_id');
        $this->profiles->deleteSkill($skillId, $profile['id']);
        $this->flash('success', 'Skill removed.');
        $this->redirect(BASE_URL . '/seeker/profile#skills');
    }

    // =========================================================================
    // WORK EXPERIENCE
    // =========================================================================
    public function addExperience(): void
    {
        $this->verifyCsrf();

        $v = Validator::make($_POST, [
            'job_title'    => 'required|max:120',
            'company_name' => 'required|max:120',
            'start_date'   => 'required|date',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->first());
            $this->back();
            return;
        }

        $profile = $this->seekerProfile();
        $this->profiles->addExperience($profile['id'], $_POST);
        $this->flash('success', 'Work experience added!');
        $this->redirect(BASE_URL . '/seeker/profile#experience');
    }

    public function deleteExperience(): void
    {
        $this->verifyCsrf();
        $profile = $this->seekerProfile();
        $expId   = (int)$this->input('exp_id');
        $this->profiles->deleteExperience($expId, $profile['id']);
        $this->flash('success', 'Experience entry removed.');
        $this->redirect(BASE_URL . '/seeker/profile#experience');
    }

    // =========================================================================
    // EDUCATION
    // =========================================================================
    public function addEducation(): void
    {
        $this->verifyCsrf();

        $v = Validator::make($_POST, [
            'institution' => 'required|max:160',
            'degree'      => 'required|max:120',
            'start_year'  => 'required|numeric|min_val:1950|max_val:2030',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->first());
            $this->back();
            return;
        }

        $profile = $this->seekerProfile();
        $this->profiles->addEducation($profile['id'], $_POST);
        $this->flash('success', 'Education added!');
        $this->redirect(BASE_URL . '/seeker/profile#education');
    }

    public function deleteEducation(): void
    {
        $this->verifyCsrf();
        $profile = $this->seekerProfile();
        $eduId   = (int)$this->input('edu_id');
        $this->profiles->deleteEducation($eduId, $profile['id']);
        $this->flash('success', 'Education entry removed.');
        $this->redirect(BASE_URL . '/seeker/profile#education');
    }

    // =========================================================================
    // APPLICATIONS TRACKER
    // =========================================================================
    public function applications(): void
    {
        $profile = $this->seekerProfile();
        $result  = $this->apps->getForSeeker($profile['id'], $this->currentPage(), 10);

        $this->view('seeker/applications', [
            'title'   => 'My Applications',
            'profile' => $profile,
            'apps'    => $result['data'],
            'paging'  => $result,
        ], 'seeker');
    }

    // =========================================================================
    // SAVED JOBS
    // =========================================================================
    public function savedJobs(): void
    {
        $profile  = $this->seekerProfile();
        $db       = Database::getInstance();
        $saved    = $db->fetchAll(
            "SELECT sj.*, jl.title, jl.job_type, jl.location_city, jl.is_remote,
                    jl.salary_min, jl.salary_max, jl.salary_currency, jl.salary_is_hidden,
                    jl.slug AS job_slug, jl.status AS job_status,
                    ep.company_name, ep.logo_path AS company_logo
               FROM saved_jobs sj
               JOIN job_listings jl      ON jl.id  = sj.job_id
               JOIN employer_profiles ep ON ep.id  = jl.employer_id
              WHERE sj.seeker_id = ?
              ORDER BY sj.saved_at DESC",
            [$profile['id']]
        );

        $this->view('seeker/saved-jobs', [
            'title'   => 'Saved Jobs',
            'profile' => $profile,
            'saved'   => $saved,
        ], 'seeker');
    }

    public function saveJob(array $params): void
    {
        $this->verifyCsrf();
        $profile = $this->seekerProfile();
        $jobId   = (int)($params['jobId'] ?? 0);

        $db      = Database::getInstance();
        $exists  = $db->fetchColumn(
            "SELECT id FROM saved_jobs WHERE seeker_id = ? AND job_id = ?",
            [$profile['id'], $jobId]
        );

        if (!$exists) {
            $db->insert('saved_jobs', [
                'seeker_id' => $profile['id'],
                'job_id'    => $jobId,
                'saved_at'  => date('Y-m-d H:i:s'),
            ]);
        }

        if ($this->isAjax()) {
            $this->json(['success' => true, 'saved' => true]);
        }
        $this->flash('success', 'Job saved!');
        $this->back();
    }

    public function unsaveJob(array $params): void
    {
        $this->verifyCsrf();
        $profile = $this->seekerProfile();
        $jobId   = (int)($params['jobId'] ?? 0);

        $db = Database::getInstance();
        $db->delete('saved_jobs', 'seeker_id = ? AND job_id = ?', [$profile['id'], $jobId]);

        if ($this->isAjax()) {
            $this->json(['success' => true, 'saved' => false]);
        }
        $this->flash('success', 'Job removed from saved list.');
        $this->back();
    }

    // =========================================================================
    // NOTIFICATIONS
    // =========================================================================
    public function notifications(): void
    {
        $db    = Database::getInstance();
        $notifs = $db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ?
              ORDER BY created_at DESC LIMIT 50",
            [Session::id()]
        );

        // Mark all as read
        $db->update('notifications', ['is_read' => 1], 'user_id = ? AND is_read = 0', [Session::id()]);

        $this->view('seeker/notifications', [
            'title'   => 'Notifications',
            'notifs'  => $notifs,
        ], 'seeker');
    }

    // =========================================================================
    // JOB ALERTS
    // =========================================================================
    public function alerts(): void
    {
        $db     = Database::getInstance();
        $alerts = $db->fetchAll(
            "SELECT * FROM job_alerts WHERE user_id = ? ORDER BY created_at DESC",
            [Session::id()]
        );

        $this->view('seeker/alerts', [
            'title'  => 'Job Alerts',
            'alerts' => $alerts,
        ], 'seeker');
    }

    public function createAlert(): void
    {
        $this->verifyCsrf();

        $db = Database::getInstance();
        $db->insert('job_alerts', [
            'user_id'    => Session::id(),
            'alert_name' => $this->input('alert_name') ?: 'My Alert',
            'keywords'   => $this->input('keywords'),
            'location'   => $this->input('location'),
            'job_type'   => $this->input('job_type', 'any'),
            'frequency'  => $this->input('frequency', 'daily'),
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->flash('success', 'Job alert created!');
        $this->redirect(BASE_URL . '/seeker/alerts');
    }

    public function deleteAlert(): void
    {
        $this->verifyCsrf();
        $db      = Database::getInstance();
        $alertId = (int)$this->input('alert_id');
        $db->delete('job_alerts', 'id = ? AND user_id = ?', [$alertId, Session::id()]);
        $this->flash('success', 'Alert deleted.');
        $this->redirect(BASE_URL . '/seeker/alerts');
    }
}
