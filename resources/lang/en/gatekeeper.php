<?php

return [
    'approve_action' => [
        'label' => 'Approve',
        'modal_heading' => 'Approve this request?',
        'modal_description' => 'Are you sure you want to approve this request? This action cannot be undone.',
        'modal_submit' => 'Approve',
        'modal_cancel' => 'Cancel',
        'success_title' => 'Approved Successfully',
        'success_message' => 'The request has been approved.',
        'error_title' => 'Approval Failed',
        'error_message' => 'Failed to approve the request.',
        'exception_title' => 'Error Occurred',
        'exception_message' => 'An error occurred: :error',
    ],

    'reject_action' => [
        'label' => 'Reject',
        'modal_heading' => 'Reject this request?',
        'modal_description' => 'Please provide a reason for rejecting this request.',
        'modal_submit' => 'Reject',
        'modal_cancel' => 'Cancel',
        'reason_label' => 'Rejection Reason',
        'success_title' => 'Rejected Successfully',
        'success_message' => 'The request has been rejected.',
        'error_title' => 'Rejection Failed',
        'error_message' => 'Failed to reject the request.',
        'exception_title' => 'Error Occurred',
        'exception_message' => 'An error occurred: :error',
    ],

    'view_history_action' => [
        'label' => 'View History',
        'modal_heading' => 'History for :sku',
        'modal_cancel_label' => 'Close',
    ],
];
