<?php

return [

    'use_multi_db_tenant' => true,
    'user_model' => \App\Models\User::class,
    'has_uuid' => true,
    'approval_flow' => [
        'cluster' => \App\Filament\Clusters\Settings::class,
        'model' => \XBigDaddyx\Gatekeeper\Models\ApprovalFlow::class,
        'scope_to_tenant' => false,
        'icon' => 'fluentui-signature-16',
        'should_register_on_navigation' => true,
        'label' => 'Approval Flow',
        'group' => 'Permits',
        'plural_label' => 'Approval Flows',
        'sort' => 0,
    ],
    'user' => [
        'tenant_ownership_name' => 'currentTeam',
        'cluster' => \App\Filament\Clusters\Settings::class,
        'model' => \App\Models\User::class,
        'scope_to_tenant' => false,
        'icon' => 'fluentui-people-queue-24',
        'should_register_on_navigation' => true,
        'label' => 'User Setting',
        'group' => 'User Managements',
        'plural_label' => 'User Settings',
        'sort' => 0,
    ],
    'job_title' => [
        'cluster' => \App\Filament\Clusters\Settings::class,
        'model' => \XBigDaddyx\Gatekeeper\Models\JobTitle::class,
        'scope_to_tenant' => false,
        'icon' => 'fluentui-shield-badge-20',
        'should_register_on_navigation' => true,
        'label' => 'Job Title',
        'group' => 'User Managements',
        'plural_label' => 'Job Titles',
        'sort' => 0,
    ],
    'tables' => [
        'user_table' => 'users',
        'job_titles' => 'gatekeeper_job_titles',
        'approval_flows' => 'gatekeeper_approval_flows',
        'approvals' => 'gatekeeper_approvals',
    ],
    'statuses' => [
        'pending' => 'pending',
        'approved' => 'approved',
        'rejected' => 'rejected',
    ],
    'default_flow' => [
        ['title' => 'Supervisor', 'step_order' => 1],
        ['title' => 'Manager', 'step_order' => 2],
    ],
];
