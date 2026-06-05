# Job Portal — Fresh XAMPP Setup Guide

## What you have: Fresh XAMPP installed ✓

---

## Step 1 — Enable mod_rewrite (do this ONCE)

Open XAMPP Control Panel → Apache → **Config** → **Apache (httpd.conf)**

**A)** Find and uncomment:
```
#LoadModule rewrite_module modules/mod_rewrite.so
```
Remove the `#` so it reads:
```
LoadModule rewrite_module modules/mod_rewrite.so
```

**B)** Press **Ctrl+H** (Find & Replace):
- Find: `AllowOverride None`
- Replace with: `AllowOverride All`
- Click **Replace All** (replaces 2 places)

**C)** Save the file → Restart Apache in XAMPP

---

## Step 2 — Place project files

Extract this ZIP so your structure is:
```
C:\xampp\htdocs\job-portal\
    app\
    core\
    config\
    public\
        index.php
        .htaccess
    storage\
    database\
        schema.sql
    routes.php
```

---

## Step 3 — Create database

1. Open **phpMyAdmin**: http://localhost/phpmyadmin
2. Click **New** → name: `job_portal` → collation: `utf8mb4_unicode_ci` → **Create**
3. Click `job_portal` in left sidebar → click **Import** tab
4. Click **Choose File** → select `database/schema.sql` → click **Go**

---

## Step 4 — Run diagnostic

Visit: **http://localhost/job-portal/public/test.php**

All items should show green ✓

---

## Step 5 — Open the portal!

Visit: **http://localhost/job-portal/public/**

---

## Demo Login Accounts (password: `Admin@1234`)

| Role     | Email                  |
|----------|------------------------|
| Admin    | admin@jobportal.com    |
| Seeker   | kwame@example.com      |
| Employer | ama@techcorp.com       |

---

## If XAMPP Control Panel crashes

Run `SETUP-NEW-XAMPP.bat` as Administrator — it kills port conflicts automatically.

If port 80 is permanently taken, change Apache to port 8080:
- httpd.conf: change `Listen 80` to `Listen 8080`
- config/app.php: change `base_url` to `http://localhost:8080/job-portal/public`
- Visit: http://localhost:8080/job-portal/public/

---

## Project Features (all built)

| Module | Features |
|--------|----------|
| Auth | Register, Login, Logout, Forgot/Reset Password |
| Job Search | Search + 7 filters, pagination, bookmarks |
| Job Detail | Apply, save job, company info |
| Companies | Browse all companies + detail page |
| Seeker Dashboard | Profile, resume upload, skills, experience, education, applications tracker |
| Employer Dashboard | Post/manage jobs, view applicants, shortlist, interviews, candidate search |
| Admin Panel | User management, company verification, job moderation, reports, audit log |
