# MaternaHealth System Documentation

## Overview
MaternaHealth is a maternal health risk monitoring system built with a PHP web interface, a MySQL database, and a Python machine learning service. The system helps healthcare workers register patients, capture health records, run risk predictions, track high-risk cases, and review reports and analytics in one place.

## How The System Works
1. A user logs in through the main entry page.
2. The PHP pages use session data to control access and decide whether the user is a nurse, doctor, or admin.
3. Patient and user data are stored in the MySQL database.
4. The Python app provides prediction and analytics services, including patient search, risk scoring, retraining, and dashboard statistics.
5. The UI pulls data from backend APIs to keep the dashboard, alerts, reports, and case lists updated.
6. High-risk predictions can be reviewed, tracked, and resolved so staff can prioritize urgent cases.

## Page Guide

### `index.php`
Login page for the system. It lets authorized users sign in and access the MaternaHealth portal.

### `components/dashboard.php`
Main operations dashboard. It shows summary cards, recent activity, live statistics, and quick access to the most important maternal health indicators.

### `components/patients.php`
Patient registry page. It is used to add, search, filter, view, and manage patient records and their latest risk information.

### `components/prediction.php`
Risk prediction workspace. This is where staff can run maternal risk assessments, review model output, and work with the patient health record data used by the ML service.

### `components/high_risk_cases.php`
High-risk monitoring page. It focuses on patients flagged as high risk, active alerts, and location-based risk visualization so urgent cases are easy to find.

### `components/reports.php`
Analytics and reporting page. It shows trends, charts, filters, and exportable summaries for patient risk patterns and system activity.

### `components/profile.php`
Personal account page. Users can review their account details, update their display name, and change their password.

### `components/user_management.php`
Admin-only account management page. It lets administrators create, edit, deactivate, and delete user accounts, and manage role access.

### `components/logout.php`
Session exit page. It clears the current session and returns the user to the login screen.

## Supporting Parts Of The System

### `components/sidebar.php`
Shared navigation used across the app. It shows the correct menu items based on the logged-in role.

### `components/header.php`
Shared page header used across the dashboard pages. It shows the current user and refresh controls.

### `backend/*.php`
Server-side API and data handlers for the PHP pages. These files provide patient, report, profile, dashboard, alert, and user-management operations.

### `app.py`
Python service that powers the machine learning side of the system. It handles prediction requests, patient search, dashboard analytics, model retraining, and community risk data.

### `db.py`
Database connector used by the Python service to talk to the MySQL database.

## How This System Helps
- It gives healthcare workers a single place to manage maternal patient data.
- It helps staff identify high-risk cases earlier through machine learning predictions.
- It improves prioritization by surfacing alerts, summaries, and high-risk lists.
- It supports data-driven decisions with charts, trends, and location-based analytics.
- It reduces manual tracking by storing patient history, predictions, and account activity in one system.
- It improves collaboration because different roles can focus on the pages relevant to them.

## Typical Workflow
1. Log in to the system.
2. Register or search for a patient.
3. Review the latest health record and run a prediction.
4. Check whether the patient appears in the high-risk list.
5. Use the reports page to monitor broader trends.
6. Use the profile or user-management page depending on the user role.

## Notes
- The system expects the MySQL database named `maternal_dbase`.
- The web UI is PHP-based, while the prediction engine and analytics service are handled by Python.
- Admin users have extra access for user management, while non-admin users see the profile page in the sidebar instead.