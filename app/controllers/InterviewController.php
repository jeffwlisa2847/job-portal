<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/app/helpers/NotificationService.php';

class InterviewController extends Controller
{
    public function index(): void
    {
        $this->requireRole('employer');
        $db = Database::getInstance();
        $ep = $db->fetchOne("SELECT * FROM employer_profiles WHERE user_id = ?", [Session::id()]);

        $interviews = $ep ? $db->fetchAll(
            "SELECT isc.*, a.id AS app_id,
                    u.full_name AS seeker_name, jl.title AS job_title
               FROM interview_schedules isc
               JOIN applications a          ON a.id  = isc.application_id
               JOIN job_listings jl         ON jl.id = a.job_id
               JOIN job_seeker_profiles jsp ON jsp.id = a.seeker_id
               JOIN users u                 ON u.id  = jsp.user_id
              WHERE jl.employer_id = ?
              ORDER BY isc.scheduled_at DESC",
            [$ep['id']]
        ) : [];

        $this->view('employer/interviews', [
            'title'      => 'Interviews',
            'interviews' => $interviews,
        ], 'employer');
    }

    public function schedule(): void
    {
        $this->requireRole('employer');
        $this->verifyCsrf();

        $db    = Database::getInstance();
        $appId = (int)$this->input('application_id');

        $db->insert('interview_schedules', [
            'application_id' => $appId,
            'scheduled_at'   => $this->input('scheduled_at'),
            'duration_mins'  => (int)$this->input('duration_mins', 60),
            'interview_type' => $this->input('interview_type', 'video'),
            'meeting_link'   => $this->input('meeting_link'),
            'location_address'=> $this->input('location_address'),
            'instructions'   => $this->input('instructions'),
            'status'         => 'scheduled',
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);

        $db->update('applications',
            ['status' => 'interview_scheduled', 'updated_at' => date('Y-m-d H:i:s')],
            'id = ?', [$appId]
        );

        // Notify seeker
        $app = $db->fetchOne(
            "SELECT a.seeker_id, jl.title AS job_title, ep.company_name,
                    u.id AS seeker_user_id
               FROM applications a
               JOIN job_listings jl ON jl.id = a.job_id
               JOIN employer_profiles ep ON ep.id = jl.employer_id
               JOIN job_seeker_profiles jsp ON jsp.id = a.seeker_id
               JOIN users u ON u.id = jsp.user_id
              WHERE a.id = ? LIMIT 1",
            [$appId]
        );
        if ($app) {
            NotificationService::interviewScheduled(
                $app['seeker_user_id'],
                $app['job_title'],
                $app['company_name'],
                $this->input('scheduled_at'),
                $this->input('interview_type', 'video'),
                $this->input('meeting_link')
            );
        }

        $this->flash('success', 'Interview scheduled! The candidate has been notified.');
        $this->back();
    }

    public function cancel(array $params): void
    {
        $this->requireRole('employer');
        $this->verifyCsrf();
        $db = Database::getInstance();
        $db->update('interview_schedules',
            ['status' => 'cancelled', 'cancelled_reason' => $this->input('reason'),
             'updated_at' => date('Y-m-d H:i:s')],
            'id = ?', [(int)$params['id']]
        );
        $this->flash('success', 'Interview cancelled.');
        $this->back();
    }
}
