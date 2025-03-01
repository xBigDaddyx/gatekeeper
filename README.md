# Laravel Approval Workflow

**A dynamic, multi-level approval system for Laravel with Filament integration.**

The `laravel-approval-workflow` package provides a flexible and powerful solution for implementing approval workflows in Laravel applications. It supports job title- and role-based approvals, parallel and conditional steps, queueable actions, audit trails, API endpoints, and notifications, all seamlessly integrated with Filament for an intuitive admin interface.

## Features

- **Multi-Level Approvals**: Define sequential approval steps based on job titles or roles.
- **Role-Based Approvals**: Integrates with `spatie/laravel-permission` for role-based workflows.
- **Parallel Approvals**: Require multiple approvers at the same step.
- **Conditional Steps**: Skip or include steps based on model attributes (e.g., amount < 1000).
- **Queueable Actions**: Process approvals asynchronously for performance.
- **Audit Trail**: Track all approval actions with timestamps and comments.
- **API Endpoints**: Manage approvals programmatically via RESTful API.
- **Notifications**: Automatically notify approvers via email or other channels.
- **Filament Integration**: Manage workflows through a user-friendly admin panel.

## Requirements

- PHP 8.2+
- Laravel 11.0+
- Filament 3.2+
- Spatie Laravel Permission 6.0+ (for role-based approvals)

## Installation

1. **Install the Package via Composer:**
   ```bash
   composer require yourname/laravel-approval-workflow:^1.0
