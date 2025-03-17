<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Request: {{ $data['resourceName'] }} #{{ $data['id'] }}</title>
    <style>
        body {
            font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            background-color: #f7f8fa;
            margin: 0;
            padding: 0;
            color: #333333;
            line-height: 1.6;
        }
        .wrapper {
            max-width: 750px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
          background-color: #34495e;
            color: #ffffff;
            padding: 25px;
            text-align: center;
            border-bottom: 5px solid #f39c12;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 35px;
        }
        .intro {
            font-size: 16px;
            margin-bottom: 30px;
            color: #555555;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #2980b9;
            margin: 0 0 20px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e8ecef;
            display: flex;
            align-items: center;
        }
        .section-title svg {
            margin-right: 10px;
            fill: #2980b9;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: #fafafa;
            border-radius: 8px;
            overflow: hidden;
        }
        .details-table th,
        .details-table td {
            padding: 14px 18px;
            text-align: left;
            border-bottom: 1px solid #e8ecef;
        }
        .details-table th {
            background-color: #e8ecef;
            color: #2c3e50;
            font-weight: 600;
            width: 40%;
            vertical-align: top;
        }
        .details-table td {
            color: #444444;
        }
        .actions {
            text-align: center;
            margin: 35px 0;
            padding: 20px;
            background: #f7f8fa;
            border-radius: 8px;
        }
        .btn {
            display: inline-block;
            padding: 14px 35px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            border-radius: 6px;
            margin: 0 15px;
            transition: background-color 0.3s ease, box-shadow 0.2s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .btn-approve {
            background-color: #27ae60;
            color: #ffffff;
        }
        .btn-approve:hover {
            background-color: #219653;
            box-shadow: 0 4px 10px rgba(39, 174, 96, 0.3);
        }
        .btn-reject {
            background-color: #c0392b;
            color: #ffffff;
        }
        .btn-reject:hover {
            background-color: #992d22;
            box-shadow: 0 4px 10px rgba(192, 57, 43, 0.3);
        }
        .info-box {
            background-color: #fef9e7;
            padding: 20px;
            border-left: 4px solid #f1c40f;
            border-radius: 5px;
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 30px;
        }
        .footer {
            background-color: #34495e;
            color: #bdc3c7;
            padding: 25px;
            text-align: center;
            font-size: 13px;
        }
        .footer a {
            color: #f39c12;
            text-decoration: none;
            font-weight: 500;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Header -->
        <div class="header">
            <h1>Approval Request: {{ $data['resourceName'] }} #{{ $data['id'] }}</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="intro">
                Dear Approver,<br>
                A new {{ $data['resourceName'] }} approval request has been submitted for your review. Please evaluate the details below and submit your decision promptly to ensure timely processing.
            </p>

            <!-- Core Details -->
            <h2 class="section-title">
                <svg width="18" height="18" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                Request Overview
            </h2>
            <table class="details-table">
                <tr>
                    <th>Type</th>
                    <td>{{ $data['resourceName'] }}</td>
                </tr>
                <tr>
                    <th>Packing List No.</th>
                    <td>{{ $data['id'] }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{ ucfirst($data['currentStatus']) }}</td>
                </tr>
                <tr>
                    <th>Approval Step</th>
                    <td>{{ $data['stepInfo'] }}</td>
                </tr>
            </table>

            <!-- Custom Details -->
            @if (count(array_diff_key($data, array_flip(['approvable', 'approveUrl', 'rejectUrl', 'stepInfo', 'resourceName', 'currentStatus', 'currentStep', 'id']))) > 0)
                <h2 class="section-title">
                    <svg width="18" height="18" viewBox="0 0 24 24"><path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm-8 14H7v-2h4zm6-4H7v-2h10zm0-4H7V7h10z"/></svg>
                    Additional Details
                </h2>
                <table class="details-table">
                    @foreach ($data as $key => $value)
                        @if (!in_array($key, ['approvable', 'approveUrl', 'rejectUrl', 'stepInfo', 'resourceName', 'currentStatus', 'currentStep', 'id']) && $value !== 'N/A' && $value !== null)
                            <tr>
                                <th>{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                <td>{{ $value }}</td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            @endif

            <!-- Action Buttons -->
            <div class="actions">
                <a href="{{ $data['approveUrl'] }}" class="btn btn-approve">Approve Request</a>
                <a href="{{ $data['rejectUrl'] }}" class="btn btn-reject">Reject Request</a>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <strong>Submission Details:</strong><br>
                - Submitted On: {{ now()->format('F j, Y, g:i a') }}<br>
                - Action Required By: {{ now()->addHours(24)->format('F j, Y, g:i a') }}<br>
                <em>These links are secure, single-use, and will expire in 24 hours. No login required.</em>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Best regards,<br>
                The {{ config('app.name') }} Team<br>
                <a href="mailto:support@{{ config('app.url') }}">Contact Support</a> |
                <a href="{{ config('app.url') }}">Visit Dashboard</a>
            </p>
        </div>
    </div>
</body>
</html>
