<?php

return [
  // Blade Template (Pending Approvals and Approval History)
  'approval' => [
    'Pending Approvals' => 'Persetujuan Tertunda',
    'Requested by:' => 'Diminta oleh:',
    'Pending' => 'Tertunda',
    'Approval History' => 'Riwayat Persetujuan',
    'Reviewed by:' => 'Ditinjau oleh:',
    'Approved' => 'Disetujui',
    'Rejected' => 'Ditolak',
  ],

  // ViewApprovalTimelineAction
  'approval_timeline_action' => [
    'label' => 'Lihat Linimasa Persetujuan',
    'modal_heading' => 'Linimasa Persetujuan untuk ID: :id',
    'modal_cancel_label' => 'Tutup',
  ],

  // SubmitApprovalAction
  'submit_approval_action' => [
    'label' => 'Ajukan untuk Persetujuan',
    'modal_heading' => 'Ajukan untuk Persetujuan?',
    'modal_description' => 'Apakah Anda yakin ingin mengajukan rekaman ini untuk persetujuan?',
    'modal_submit' => 'Ajukan',
    'modal_cancel' => 'Batal',
    'success_title' => 'Berhasil Diajukan',
    'success_message' => 'Rekaman telah diajukan untuk persetujuan.',
    'error_title' => 'Pengajuan Gagal',
    'error_message' => 'Gagal mengajukan rekaman untuk persetujuan. Pastikan model mendukung aksi ini.',
    'exception_title' => 'Kesalahan Pengajuan',
    'exception_message' => 'Terjadi kesalahan saat mengajukan: :error',
  ],

  // RejectAction
  'reject_action' => [
    'label' => 'Tolak',
    'modal_heading' => 'Tolak Permintaan Ini?',
    'modal_description' => 'Harap berikan alasan untuk menolak permintaan ini.',
    'modal_submit' => 'Tolak',
    'modal_cancel' => 'Batal',
    'reason_label' => 'Alasan Penolakan',
    'success_title' => 'Berhasil Ditolak',
    'success_message' => 'Permintaan telah ditolak.',
    'error_title' => 'Penolakan Gagal',
    'error_message' => 'Gagal menolak permintaan. Pastikan model mendukung aksi ini.',
    'exception_title' => 'Kesalahan Penolakan',
    'exception_message' => 'Terjadi kesalahan saat menolak: :error',
  ],

  // ApproveAction
  'approve_action' => [
    'label' => 'Setujui',
    'modal_heading' => 'Setujui Permintaan Ini?',
    'modal_description' => 'Apakah Anda yakin ingin menyetujui permintaan ini? Aksi ini tidak dapat dibatalkan.',
    'modal_submit' => 'Setujui',
    'modal_cancel' => 'Batal',
    'success_title' => 'Berhasil Disetujui',
    'success_message' => 'Permintaan telah disetujui.',
    'error_title' => 'Persetujuan Gagal',
    'error_message' => 'Gagal menyetujui permintaan. Pastikan model mendukung aksi ini.',
    'exception_title' => 'Kesalahan Persetujuan',
    'exception_message' => 'Terjadi kesalahan saat menyetujui: :error',
  ],
];
