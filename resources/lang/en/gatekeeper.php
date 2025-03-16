<?php

return [
  // Blade Template (Pending Approvals and Approval History)
  'approval' => [
    'Pending Approvals' => 'Pending Approvals',
    'Requested by:' => 'Requested by:',
    'Pending' => 'Pending',
    'Approval History' => 'Approval History',
    'Reviewed by:' => 'Reviewed by:',
    'Approved' => 'Approved',
    'Rejected' => 'Rejected',
  ],

  // ViewApprovalTimelineAction
  'approval_timeline_action' => [
    'label' => 'View Approval Timeline',
    'modal_heading' => 'Approval Timeline for ID: :id',
    'modal_cancel_label' => 'Close',
  ],

  // SubmitApprovalAction
  'submit_approval_action' => [
    'label' => 'Submit for Approval',
    'modal_heading' => 'Submit for Approval?',
    'modal_description' => 'Are you sure you want to submit this record for approval?',
    'modal_submit' => 'Submit',
    'modal_cancel' => 'Cancel',
    'success_title' => 'Submitted Successfully',
    'success_message' => 'The record has been submitted for approval.',
    'error_title' => 'Submission Failed',
    'error_message' => 'Failed to submit the record for approval. Ensure the model supports this action.',
    'exception_title' => 'Submission Error',
    'exception_message' => 'An error occurred while submitting: :error',
  ],

  // RejectAction
  'reject_action' => [
    'label' => 'Reject',
    'modal_heading' => 'Reject this Request?',
    'modal_description' => 'Please provide a reason for rejecting this request.',
    'modal_submit' => 'Reject',
    'modal_cancel' => 'Cancel',
    'reason_label' => 'Rejection Reason',
    'success_title' => 'Rejected Successfully',
    'success_message' => 'The request has been rejected.',
    'error_title' => 'Rejection Failed',
    'error_message' => 'Failed to reject the request. Ensure the model supports this action.',
    'exception_title' => 'Rejection Error',
    'exception_message' => 'An error occurred while rejecting: :error',
  ],

  // ApproveAction
  'approve_action' => [
    'label' => 'Approve',
    'modal_heading' => 'Approve this Request?',
    'modal_description' => 'Are you sure you want to approve this request? This action cannot be undone.',
    'modal_submit' => 'Approve',
    'modal_cancel' => 'Cancel',
    'success_title' => 'Approved Successfully',
    'success_message' => 'The request has been approved.',
    'error_title' => 'Approval Failed',
    'error_message' => 'Failed to approve the request. Ensure the model supports this action.',
    'exception_title' => 'Approval Error',
    'exception_message' => 'An error occurred while approving: :error',
  ],
];
