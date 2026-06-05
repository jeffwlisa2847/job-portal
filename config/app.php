<?php
return [
    'name'      => 'JobPortal',
    'tagline'   => 'Find Your Dream Job or Hire Top Talent',
    // ── IMPORTANT: change this if your folder name is different ──────────────
    // Format: http://localhost/YOUR-FOLDER-NAME/public
    'base_url'  => 'http://localhost/job-portal/public',
    'env'       => 'development',
    'debug'     => true,
    'timezone'  => 'Africa/Accra',
    'locale'    => 'en',
    'per_page'  => 10,

    // File uploads
    'max_resume_size_mb'   => 5,
    'max_image_size_mb'    => 2,
    'allowed_resume_types' => ['application/pdf','application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    'allowed_image_types'  => ['image/jpeg','image/png','image/webp'],

    // Storage paths (relative to ROOT_PATH)
    'storage' => [
        'resumes'       => '/storage/resumes/',
        'avatars'       => '/storage/avatars/',
        'logos'         => '/storage/company-logos/',
        'cover_letters' => '/storage/cover-letters/',
        'documents'     => '/storage/documents/',
    ],

    'job_expiry_days'  => 30,
    'support_email'    => 'support@jobportal.com',
    'noreply_email'    => 'noreply@jobportal.com',
];
