# Ticket Management System

## Project Overview

This is a Laravel-based Ticket Management System for customer support, built for COMP 016. It demonstrates CRUD, RBAC, escalations, attachments, and more.

The application showcases:

-   **Laravel Core Concepts:** Routing, controllers, models, migrations, and Blade templating
-   **Advanced ORM Usage:** Eloquent ORM for object-oriented database interactions
-   **RBAC Implementation:** Role-based access control for Admin, Manager, Agent, and Customer roles
-   **Enterprise Features:** Ticket assignment, escalation, attachment handling, and status tracking
-   **Best Practices:** Clean code architecture, maintainable structure, and scalable design

---

### Project Features

-   **Ticket Creation & Tracking:** Customers create tickets; admins and managers track and resolve them
-   **Role-Based Workflow:** Different roles have different permissions and responsibilities
-   **Priority & Category Management:** Organize tickets by urgency and type
-   **Assignment & Escalation:** Assign tickets to agents and escalate complex issues
-   **Communication History:** Maintain conversation threads with attachments
-   **Status Management:** Track ticket lifecycle from creation to resolution
-   **Attachment Support:** Upload and download files related to tickets

---

## Course Information

**Course Code:** COMP 016 - Web Development  
**Project Type:** Final Project - Web Development  
**Framework:** Laravel 12  
**PHP Version:** 8.2+  
**Database:** MySQL

---

## Table of Contents

1. [Quick Start](#quick-start)
2. [Demo Credentials](#demo-credentials)
3. [Step-by-Step Setup](#step-by-step-setup)
4. [Database Tables](#database-tables)
5. [Demo Data Overview](#demo-data-overview)
6. [Features](#features)
7. [Design](#design)
8. [API Routes & Endpoints](#api-routes--endpoints)
9. [Code Structure & Organization](#code-structure--organization)
10. [Google Classroom Submission](#google-classroom-submission)

---

## Quick Start

### Technology Stack

| Component         | Technology                    |
| ----------------- | ----------------------------- |
| Backend Framework | Laravel 12                    |
| PHP Version       | 8.2+                          |
| Database          | MySQL                         |
| Frontend          | Blade Templates, Tailwind CSS |
| Frontend Build    | Vite                          |
| Authentication    | Laravel Breeze                |
| ORM               | Eloquent                      |

### Cloning the Project

```bash
git clone https://github.com/raphaelatuuuuy/ticket-management-system.git
cd ticket-management-system
```

### Setup the Database and Mail

Locate the .env.example file and remove from file name '.example'.

Now, it should be only .env then edit the content based on the following:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE= # YOUR DATABASE TABLE (eg. ticket)
DB_USERNAME= # YOUR DATABASE NAME (eg. root)
DB_PASSWORD= # YOU DATABASE PASSWORD (eg. db1232)
```

Next one, setup the mail (eg. gmail)

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME= # EMAIL TO BE USED FOR EMAILING (eg. sample@gmail.com)
MAIL_PASSWORD= # APP PASSWORD FROM YOUR GMAIL (eg. "wxsf wfwa dwdw dwfe")
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=sample@gmail.com
MAIL_FROM_NAME="Ticket Management System"
```

### Run the migrations

```bash
php artisan migrate
```

### Now seed the demo data

```bash
php artisan db:seed --class=DemoSeeder

```

### Now run the server. Type these commands on seperate terminal. Don't close any of the terminal to avoid issues.

```bash
# Terminal 1
php artisan serve

 INFO  Server running on [http://127.0.0.1:8000].

  Press Ctrl+C to stop the server

# Terminal 2
npm run dev

> dev
> vite


  VITE v7.3.0  ready in 477 ms

  ➜  Local:   http://localhost:5173/
  ➜  Network: use --host to expose
  ➜  press h + enter to show help

  LARAVEL v12.42.0  plugin v2.0.1

  ➜  APP_URL: http://localhost

```

---

## Demo Credentials

| Role     | Name             | Email                            | Password     |
| -------- | ---------------- | -------------------------------- | ------------ |
| Admin    | Den Mateo        | den.mateo@ticketsystem.com       | Admin@123    |
| Manager  | Lancilot Tibay   | lancilot.tibay@ticketsystem.com  | Manager@123  |
| Manager  | Patrice Mendoza  | patrice.mendoza@ticketsystem.com | Manager@123  |
| Agent    | Raphael Latoy    | raphael.latoy@ticketsystem.com   | Agent@123    |
| Agent    | Juan Dela Cruz   | juan.delacruz@ticketsystem.com   | Agent@123    |
| Agent    | Jose Rizal       | jose.rizal@ticketsystem.com      | Agent@123    |
| Customer | Andres Bonifacio | andres.bonifacio@customer.com    | Customer@123 |
| Customer | Manny Pacquiao   | manny.pacquiao@customer.com      | Customer@123 |
| Customer | Catriona Gray    | catriona.gray@customer.com       | Customer@123 |
| Customer | Lea Salonga      | lea.salonga@customer.com         | Customer@123 |

---

## Demo Data Overview

This project includes a seeded demo dataset (see `database/seeders/DemoSeeder.php`) with realistic users, tickets, comments, attachments, escalations, and reopen requests to exercise all workflows.

Summary of demo data included by `DemoSeeder`:

-   Users: 11 total (1 Admin, 2 Managers, 3 Agents, 4 Customers)
-   Statuses: 7 (Open, In Progress, On Hold, Awaiting Customer Reply, Escalated, Resolved, Closed)
-   Priorities: 4 (Low, Medium, High, Urgent)
-   Categories: 5 (Billing, Technical Support, Feature Request, Bug Report, Account Management)
-   Tickets: 10 sample tickets across multiple statuses
-   Comments: 8+ realistic replies across tickets
-   Attachments: 4 attachment records (sample image references)
-   Ticket Escalations: 2 sample escalations
-   Reopen Requests: 2 sample reopen requests

## Database Tables

| Table Name          | Purpose                                                     |
| ------------------- | ----------------------------------------------------------- |
| users               | Stores user accounts (admin, manager, agent, customer)      |
| tickets             | Main ticket records, status, priority, category, assignment |
| ticket_comments     | Conversation threads and replies for tickets                |
| tickets_attachments | File uploads attached to tickets/comments                   |
| statuses            | Configurable ticket statuses (Open, In Progress, etc.)      |
| priorities          | Configurable priority levels (Low, Medium, High, Urgent)    |
| categories          | Configurable ticket categories (Billing, Technical, etc.)   |
| ticket_escalations  | Tracks escalations for complex tickets                      |
| reopen_requests     | Handles customer requests to reopen resolved tickets        |

---

## Design

### System Architecture

The system follows a standard Laravel MVC architecture.

```
Client (Browser) -> Routes (web.php, auth.php)
    -> Controllers (Admin/Manager/Agent/TicketController)
    -> Models / Eloquent (User, Ticket, Comment, Attachment, Status, Priority, Category, TicketEscalation, ReopenRequest)
    -> MySQL (migrations)
```

### Entity Relationship Summary

```
ticket-management-system/relationships
│
├── users/
│   ├── hasMany -> tickets
│   ├── hasMany -> ticket_comments
│   ├── hasMany -> ticket_escalations
│   └── hasMany -> reopen_requests
│
├── tickets/
│   ├── belongsTo -> user
│   ├── belongsTo -> status
│   ├── belongsTo -> priority
│   ├── belongsTo -> category
│   ├── hasMany -> ticket_comments
│   ├── hasMany -> tickets_attachments
│   ├── hasMany -> ticket_escalations
│   └── hasMany -> reopen_requests
│
├── ticket_comments/
│   ├── belongsTo -> ticket
│   ├── belongsTo -> user
│   └── mayHave -> attachments
│
├── ticket_escalations/
│   └── fields: requested_by_id, escalated_by_id, resolved_by_id, requested_at, escalated_at, resolved_at, resolution_notes
│
└── reopen_requests/
    └── fields: requested_by_id, responded_by_id, status (pending|accepted|declined), requested_at, responded_at, remarks
```

### Typical Workflow

1. Customer creates a ticket with `POST /tickets`.
2. Manager reviews and assigns via `/manager/tickets/{id}/assign` or admin `/admin/tickets/{id}/assign`.
3. Agent responds, updates status (`/agent/tickets/{id}/status`) and may attach files.
4. Agent/Manager can request escalation (`/agent/tickets/{id}/request-escalation` or `/manager/tickets/{id}/request-escalation`).
5. Admin reviews escalations and resolves/rejects them (`/admin/tickets/{ticketId}/escalations/{escalationId}/resolve`).
6. Customers may request to reopen tickets (`/tickets/{id}/request-reopen`), handled by managers/admins.

---

## Code Structure & Organization

### Directory Structure

```
ticket-management-system/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── UserManagementController.php
│   │   │   │   ├── AdminTicketController.php
│   │   │   │   └── ConfigurationController.php
│   │   │   ├── Manager/
│   │   │   │   ├── DashboardController.php
│   │   │   │   └── ManagerTicketController.php
│   │   │   ├── Agent/
│   │   │   │   ├── DashboardController.php
│   │   │   │   └── AgentTicketController.php
│   │   │   ├── TicketController.php
│   │   │   ├── ProfileController.php
│   │   │   └── Controller.php
│   │   ├── Middleware/
│   │   │   └── [Custom middleware]
│   │   └── Requests/
│   │       └── [Form requests for validation]
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── Ticket.php
│   │   ├── Comment.php
│   │   ├── Attachment.php
│   │   ├── Status.php
│   │   ├── Priority.php
│   │   ├── Category.php
│   │   ├── TicketEscalation.php
│   │   ├── ReopenRequest.php
│   │   └── TicketComment.php (alias)
│   │
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── View/
│       └── Components/
│
├── bootstrap/
│   ├── app.php
│   ├── providers.php
│   └── cache/
│
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── filesystems.php
│   ├── mail.php
│   ├── cache.php
│   ├── queue.php
│   └── session.php
│
├── database/
│   ├── migrations/
│   │   ├── 2026_01_04_112937_create_users_table.php
│   │   ├── 2026_01_04_113042_create_password_reset_token_table.php
│   │   ├── 2026_01_04_113128_create_statuses_table.php
│   │   ├── 2026_01_04_113129_create_categories_table.php
│   │   ├── 2026_01_04_113130_create_priorities_table.php
│   │   ├── 2026_01_04_113131_create_tickets_table.php
│   │   ├── 2026_01_04_113132_create_ticket_comments_table.php
│   │   ├── 2026_01_04_113133_create_tickets_attachments_table.php
│   │   ├── 2026_01_06_094817_create_ticket_escalations_table.php
│   │   └── 2026_01_06_192701_create_reopen_requests_table.php
│   ├── seeders/
│   │   └── DemoSeeder.php
│   └── factories/
│
├── resources/
│   ├── views/
│   │   ├── admin/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── users/
│   │   │   ├── tickets/
│   │   │   └── configuration/
│   │   ├── manager/
│   │   │   ├── dashboard.blade.php
│   │   │   └── tickets/
│   │   ├── agent/
│   │   │   ├── dashboard.blade.php
│   │   │   └── tickets/
│   │   ├── customer/
│   │   │   ├── dashboard.blade.php
│   │   │   └── tickets/
│   │   ├── auth/
│   │   ├── components/
│   │   └── layouts/
│   │
│   ├── css/
│   │   └── app.css (Tailwind CSS)
│   │
│   └── js/
│       ├── app.js
│       └── bootstrap.js
│
├── routes/
│   ├── web.php          (All web routes)
│   ├── auth.php         (Authentication routes - Laravel Breeze)
│   └── console.php
│
├── storage/
│   ├── app/             (File storage)
│   ├── framework/
│   └── logs/
│
├── tests/
│   ├── Feature/
│   └── Unit/
│
├── public/
│   ├── index.php
│   ├── build/           (Built assets)
│   └── js/
│
├── vendor/              (Composer packages)
├── .env.example         (Environment template)
├── composer.json        (PHP dependencies)
├── package.json         (Node.js dependencies)
├── phpunit.xml          (PHPUnit configuration)
├── tailwind.config.js   (Tailwind configuration)
├── vite.config.js       (Vite build configuration)
└── artisan              (CLI command file)
```

---

## Key Relationships

| **Key**        | **Talks To**            | **Purpose**                                                                                                                                                         | **Example / Route**                                    |
| -------------- | ----------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------ |
| **Route**      | Middleware & Controller | Maps URL to the correct controller method based on user role and action                                                                                             | `/agent/tickets` → `Agent\AgentTicketController@index` |
| **Middleware** | Route & Controller      | Filters requests: enforces authentication (`auth`) and role access (`role:admin,manager,agent`) before reaching the controller                                      | `auth`, `role:admin`                                   |
| **Controller** | Model & View            | Fetches or updates data via Models (e.g., `Ticket::where('assigned_to', ...)`), then returns a View (e.g., `agent.tickets.index`) or JSON (for AJAX ticket details) | `Admin\AdminTicketController@assign`                   |
| **Model**      | Database                | Manages data logic: defines relationships (Ticket → User), access rules (`$fillable`), and interacts with tables (`tickets`, `users`, `statuses`, etc.)             | Tables: `tickets`, `users`, `statuses`                 |
| **View**       | Controller              | Displays data as HTML using Blade templates (e.g., `admin/dashboard.blade.php`), includes shared components like `tickets/detail-panel.blade.php`                   | `resources/views/admin/dashboard.blade.php`            |

---

## API Routes & Endpoints

### Authentication & User (via Laravel Breeze)

| Method | Endpoint  | Controller → Method                      | View / Response             | Purpose                 |
| ------ | --------- | ---------------------------------------- | --------------------------- | ----------------------- |
| GET    | /register | `RegisteredUserController@create`        | `auth.register`             | Show registration form  |
| POST   | /register | `RegisteredUserController@store`         | Redirect to `/dashboard`    | Create new user account |
| GET    | /login    | `AuthenticatedSessionController@create`  | `auth.login`                | Show login form         |
| POST   | /login    | `AuthenticatedSessionController@store`   | Redirect to `/dashboard`    | Authenticate user       |
| POST   | /logout   | `AuthenticatedSessionController@destroy` | Redirect to `/`             | Log out current user    |
| GET    | /profile  | `ProfileController@edit`                 | `profile.edit`              | Show profile edit form  |
| PATCH  | /profile  | `ProfileController@update`               | Redirect back to `/profile` | Update user profile     |
| DELETE | /profile  | `ProfileController@destroy`              | Redirect to `/` (logs out)  | Delete user account     |

---

### Customer Ticket Actions

| Method | Endpoint                                                | Controller → Method                       | View / Response             | Purpose                                              |
| ------ | ------------------------------------------------------- | ----------------------------------------- | --------------------------- | ---------------------------------------------------- |
| POST   | /tickets                                                | `TicketController@store`                  | Redirect to `/dashboard`    | Create new ticket                                    |
| GET    | /tickets/{id}                                           | `TicketController@show`                   | JSON (ticket details)       | View ticket details (AJAX/JSON used by detail-panel) |
| POST   | /tickets/{id}/reply                                     | `TicketController@reply`                  | JSON (success / updated_at) | Add reply/comment with optional attachments          |
| GET    | /tickets/attachment/{path}                              | `TicketController@downloadAttachment`     | File download response      | Download ticket attachment                           |
| POST   | /tickets/{id}/request-reopen                            | `TicketController@requestReopen`          | JSON (success/message)      | Request to reopen ticket                             |
| POST   | /tickets/{id}/reopen-requests/{reopenRequestId}/respond | `TicketController@respondToReopenRequest` | JSON (success/message)      | Respond to reopen request (admin/manager action)     |

---

### Admin Routes (prefix: /admin)

| Method | Endpoint                                                      | Controller → Method                               | View / Response             | Purpose                                     |
| ------ | ------------------------------------------------------------- | ------------------------------------------------- | --------------------------- | ------------------------------------------- |
| GET    | /admin/dashboard                                              | `Admin\DashboardController@index`                 | `admin.dashboard`           | Show admin dashboard and summaries          |
| GET    | /admin/users                                                  | `Admin\UserManagementController@index`            | `admin.users.manage`        | List users (includes search/filters)        |
| GET    | /admin/users/{id}                                             | `Admin\UserManagementController@show`             | `admin.users.show`          | View user details                           |
| GET    | /admin/users/{id}/edit                                        | `Admin\UserManagementController@edit`             | `admin.users.edit`          | Show edit user form                         |
| PUT    | /admin/users/{id}                                             | `Admin\UserManagementController@update`           | Redirect to users list      | Update user details and status              |
| PATCH  | /admin/users/{id}/role                                        | `Admin\UserManagementController@updateRole`       | Redirect back               | Update user role                            |
| DELETE | /admin/users/{id}                                             | `Admin\UserManagementController@destroy`          | Redirect back               | Soft-delete (deactivate) user               |
| GET    | /admin/configuration                                          | `Admin\ConfigurationController@index`             | `admin.configuration.index` | View statuses, categories, priorities       |
| POST   | /admin/configuration/status                                   | `Admin\ConfigurationController@storeStatus`       | Redirect to configuration   | Add status                                  |
| PATCH  | /admin/configuration/status/{id}                              | `Admin\ConfigurationController@updateStatus`      | Redirect to configuration   | Update status / activate/deactivate         |
| DELETE | /admin/configuration/status/{id}                              | `Admin\ConfigurationController@destroyStatus`     | Redirect to configuration   | Deactivate status                           |
| POST   | /admin/configuration/category                                 | `Admin\ConfigurationController@storeCategory`     | Redirect to configuration   | Add category                                |
| PATCH  | /admin/configuration/category/{id}                            | `Admin\ConfigurationController@updateCategory`    | Redirect to configuration   | Update category / activate/deactivate       |
| DELETE | /admin/configuration/category/{id}                            | `Admin\ConfigurationController@destroyCategory`   | Redirect to configuration   | Deactivate category                         |
| POST   | /admin/configuration/priority                                 | `Admin\ConfigurationController@storePriority`     | Redirect to configuration   | Add priority                                |
| PATCH  | /admin/configuration/priority/{id}                            | `Admin\ConfigurationController@updatePriority`    | Redirect to configuration   | Update priority / activate/deactivate       |
| DELETE | /admin/configuration/priority/{id}                            | `Admin\ConfigurationController@destroyPriority`   | Redirect to configuration   | Deactivate priority                         |
| GET    | /admin/tickets                                                | `Admin\AdminTicketController@index`               | `admin.tickets.index`       | List all tickets and filters                |
| POST   | /admin/tickets/{id}/assign                                    | `Admin\AdminTicketController@assign`              | JSON (success/message)      | Assign ticket to user/agent                 |
| POST   | /admin/tickets/{id}/status                                    | `Admin\AdminTicketController@updateStatus`        | JSON (success/message)      | Update ticket status                        |
| POST   | /admin/tickets/{id}/request-escalation                        | `Admin\AdminTicketController@requestEscalation`   | JSON (success/message)      | Submit escalation request                   |
| POST   | /admin/tickets/{id}/escalate                                  | `Admin\AdminTicketController@escalate`            | JSON (success/message)      | Escalate ticket (admin action)              |
| POST   | /admin/tickets/{id}/reopen                                    | `Admin\AdminTicketController@reopen`              | JSON (success/message)      | Reopen ticket                               |
| POST   | /admin/tickets/{id}/reopen-request/{reopenRequestId}/{action} | `Admin\AdminTicketController@handleReopenRequest` | JSON (success/message)      | Accept or reject a reopen request           |
| POST   | /admin/tickets/{ticketId}/escalations/{escalationId}/assign   | `Admin\AdminTicketController@assignEscalation`    | JSON (success/message)      | Assign an escalation to current admin       |
| POST   | /admin/tickets/{ticketId}/escalations/{escalationId}/resolve  | `Admin\AdminTicketController@markAsResolved`      | JSON (success/message)      | Mark escalation as resolved and assign back |
| POST   | /admin/tickets/{ticketId}/escalations/{escalationId}/reject   | `Admin\AdminTicketController@rejectEscalation`    | JSON (success/message)      | Reject escalation and return to agent       |
| DELETE | /admin/tickets/{id}                                           | `Admin\AdminTicketController@destroy`             | JSON (success/message)      | Soft-delete (deactivate) ticket             |
| POST   | /admin/tickets/{id}/restore                                   | `Admin\AdminTicketController@restore`             | JSON (success/message)      | Restore soft-deleted ticket                 |

---

### Manager Routes (prefix: /manager)

| Method | Endpoint                                                        | Controller → Method                                   | View / Response         | Purpose                                    |
| ------ | --------------------------------------------------------------- | ----------------------------------------------------- | ----------------------- | ------------------------------------------ |
| GET    | /manager/dashboard                                              | `Manager\DashboardController@index`                   | `manager.dashboard`     | Show manager dashboard and team summary    |
| GET    | /manager/tickets                                                | `Manager\ManagerTicketController@index`               | `manager.tickets.index` | List team tickets with filters             |
| POST   | /manager/tickets/{id}/assign                                    | `Manager\ManagerTicketController@assign`              | JSON (success/message)  | Assign ticket to agent/manager (not admin) |
| POST   | /manager/tickets/{id}/status                                    | `Manager\ManagerTicketController@updateStatus`        | JSON (success/message)  | Update ticket status                       |
| POST   | /manager/tickets/{id}/request-escalation                        | `Manager\ManagerTicketController@requestEscalation`   | JSON (success/message)  | Submit escalation request to admin         |
| POST   | /manager/tickets/{ticketId}/escalations/{escalationId}/assign   | `Manager\ManagerTicketController@assignEscalation`    | JSON (success/message)  | Assign escalation to self                  |
| POST   | /manager/tickets/{ticketId}/escalations/{escalationId}/escalate | `Manager\ManagerTicketController@escalateToAdmin`     | JSON (success/message)  | Escalate ticket to admin                   |
| POST   | /manager/tickets/{ticketId}/escalations/{escalationId}/resolve  | `Manager\ManagerTicketController@resolveEscalation`   | JSON (success/message)  | Mark escalation resolved and reassign      |
| POST   | /manager/tickets/{ticketId}/escalations/{escalationId}/reject   | `Manager\ManagerTicketController@rejectEscalation`    | JSON (success/message)  | Reject escalation and return to agent      |
| POST   | /manager/tickets/{id}/reopen                                    | `Manager\ManagerTicketController@reopen`              | JSON (success/message)  | Reopen ticket                              |
| POST   | /manager/tickets/{id}/reopen-request/{reopenRequestId}/{action} | `Manager\ManagerTicketController@handleReopenRequest` | JSON (success/message)  | Accept or reject a reopen request          |

---

### Agent Routes (prefix: /agent)

| Method | Endpoint                                                      | Controller → Method                               | View / Response        | Purpose                                                     |
| ------ | ------------------------------------------------------------- | ------------------------------------------------- | ---------------------- | ----------------------------------------------------------- |
| GET    | /agent/dashboard                                              | `Agent\DashboardController@index`                 | `agent.dashboard`      | Show agent dashboard and assigned stats                     |
| GET    | /agent/tickets                                                | `Agent\AgentTicketController@index`               | `agent.tickets.index`  | List tickets assigned to current agent                      |
| POST   | /agent/tickets/{id}/status                                    | `Agent\AgentTicketController@updateStatus`        | JSON (success/message) | Update ticket status (by assigned agent)                    |
| POST   | /agent/tickets/{id}/request-escalation                        | `Agent\AgentTicketController@requestEscalation`   | JSON (success/message) | Submit escalation request to manager/admin                  |
| POST   | /agent/tickets/{id}/verify-and-close                          | `Agent\AgentTicketController@verifyAndClose`      | JSON (success/message) | Verify fix and close ticket                                 |
| POST   | /agent/tickets/{id}/request-re-escalation                     | `Agent\AgentTicketController@requestReEscalation` | JSON (success/message) | Request re-escalation if not resolved                       |
| POST   | /agent/tickets/{id}/request-reopen                            | `Agent\AgentTicketController@requestReopenTicket` | JSON (success/message) | Agent requests reopen on behalf of customer (if applicable) |
| POST   | /agent/tickets/{id}/reopen-request/{reopenRequestId}/{action} | `Agent\AgentTicketController@handleReopenRequest` | JSON (success/message) | Accept/reject reopen request (if authorized)                |

---

---

## Key Controller Classes

#### AdminTicketController

```php
// Location: app/Http/Controllers/Admin/AdminTicketController.php

class AdminTicketController extends Controller {
    public function index()           // List all tickets
    public function assign()          // Assign ticket to user
    public function updateStatus()    // Change ticket status
    public function requestEscalation() // Request escalation
    public function escalate()        // Approve escalation
    public function reopen()          // Reopen resolved ticket
    public function handleReopenRequest() // Accept/reject reopen request
    public function assignEscalation() // Assign escalated ticket
    public function markAsResolved()   // Mark escalation resolved
    public function rejectEscalation() // Reject escalation
    public function destroy()         // Soft delete ticket
    public function restore()         // Restore deleted ticket
}
```

#### ManagerTicketController

```php
// Location: app/Http/Controllers/Manager/ManagerTicketController.php

class ManagerTicketController extends Controller {
    public function index()           // List team tickets
    public function assign()          // Assign to agent
    public function updateStatus()    // Change status
    public function requestEscalation() // Escalate to admin
    public function assignEscalation() // Assign escalation
    public function escalateToAdmin()  // Move to admin
    public function resolveEscalation() // Mark resolved
    public function rejectEscalation() // Reject escalation
    public function reopen()          // Reopen ticket
}
```

#### TicketController

```php
// Location: app/Http/Controllers/TicketController.php

class TicketController extends Controller {
    public function store()           // Create new ticket
    public function show($id)         // View ticket details
    public function reply($id)        // Add comment/reply
    public function downloadAttachment() // Download file
    public function requestReopen($id) // Request reopen
    public function respondToReopenRequest() // Respond to reopen
}
```

etc.

---

### Model Relationships

#### User Model

```php
class User extends Authenticatable {
    // Relationships
    public function createdTickets() {}      // HasMany Ticket
    public function assignedTickets() {}     // HasMany Ticket (assigned_to)
    public function comments() {}            // HasMany Comment
    public function escalations() {}         // HasMany TicketEscalation
    public function reopenRequests() {}      // HasMany ReopenRequest
}
```

#### Ticket Model

```php
class Ticket extends Model {
    // Relationships
    public function user() {}                // BelongsTo User (creator)
    public function assignedTo() {}          // BelongsTo User (assigned agent)
    public function status_relation() {}     // BelongsTo Status
    public function priority_relation() {}   // BelongsTo Priority
    public function category_relation() {}   // BelongsTo Category
    public function comments() {}            // HasMany Comment
    public function attachments() {}         // HasMany Attachment
    public function escalations() {}         // HasMany TicketEscalation
    public function reopenRequests() {}      // HasMany ReopenRequest
}
```

etc.

---

## Authentication & Authorization

### Authentication System

-   **Built with:** Laravel Breeze (scaffolding)
-   **Method:** Session-based authentication
-   **Features:**
    -   User registration with email verification
    -   Password reset via email

### Authorization Strategy

#### Gate-Based Authorization

```php
// Check user role
if(auth()->user()->role === 'admin') {
    // Admin-only operations
}
```

#### Middleware-Based

Routes are protected by the `auth` middleware:

```php
Route::middleware('auth')->group(function () {
    // All routes here require authentication
});
```

#### Controller-Level Authorization

```php
// In controllers, verify role before executing actions
public function destroy($id) {
    if(auth()->user()->role !== 'admin') {
        abort(403, 'Unauthorized');
    }
    // Proceed with deletion
}
```

### Role Verification Pattern

```php
// Common pattern in all role-specific controllers
public function __construct() {
    $this->middleware(function ($request, $next) {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
        return $next($request);
    });
}
```
