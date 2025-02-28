<?php


return [
    'user_model' => \App\Models\User::class,
    'cluster' => \App\Filament\Clusters\Settings::class,
    'approval_flow' => [
        'model' => \Xbigdaddyx\TeresaGatekeeper\Models\ApprovalFlow::class,
        'scope_to_tenant' => false,
        'icons' => 'heroicon-o-chart-bar',
        'should_register_on_navigation' => true,
        'label' => 'Approval Flow',
        'group' => 'Permits',
        'plural_label' => 'Approval Flows',
        'sort' => 0,
    ],
    'tables' => [
        'job_titles' => 'job_titles',
        'approval_flows' => 'approval_flows',
        'approvals' => 'approvals',
    ],
    'statuses' => [
        'pending' => 'pending',
        'approved' => 'approved',
        'rejected' => 'rejected',
    ],
    'default_flow' => [
        ['title' => 'Supervisor', 'step_order' => 1],
        ['title' => 'Manager', 'step_order' => 2]
    ],
];
