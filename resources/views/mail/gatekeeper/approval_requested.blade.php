<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Request: {{ $resourceName }} #{{ $approvable->id }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 700px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }

        h2 {
            color: #34495e;
            font-size: 18px;
            margin: 30px 0 15px;
        }

        .intro,
        .closing {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .details-table th,
        .details-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .details-table th {
            background-color: #eef2f7;
            color: #2c3e50;
            font-weight: bold;
            width: 30%;
        }

        .actions {
            text-align: center;
            margin: 30px 0;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            margin: 0 10px;
            transition: background-color 0.3s;
        }

        .btn-approve {
            background-color: #28a745;
            color: #ffffff;
        }

        .btn-approve:hover {
            background-color: #218838;
        }

        .btn-reject {
            background-color: #dc3545;
            color: #ffffff;
        }

        .btn-reject:hover {
            background-color: #c82333;
        }

        .info {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #999;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Approval Request: {{ $resourceName }} PO#{{$po}}</h1>

        <p class="intro">
            Hello,<br>
            A new approval request requires your attention. Please review the packing list details below and take
            action.
        </p>

        <h2>Packing List Details</h2>
        <table class="details-table">
            <tr>
                <th>Type</th>
                <td>{{ $resourceName }}</td>
            </tr>
            <tr>
                <th>Purchase Order</th>
                <td>{{ $po }}</td>
            </tr>
            <tr>
                <th>Purchase Order Quantity</th>
                <td>{{ $po_quantity }} pcs</td>
            </tr>
            <tr>
                <th>Validated Items</th>
                <td>{{ $validatedItems }} pcs</td>
            </tr>
            <tr>
                <th>Validated Cartons</th>
                <td>{{ $validatedCartons }} boxes</td>
            </tr>
            <tr>
                <th>Buyer Name</th>
                <td>{{ $buyerName }}</td>
            </tr>
            <tr>
                <th>Contract</th>
                <td>{{ $contract }}</td>
            </tr>
            <tr>
                <th>Style</th>
                <td>{{ $style }}</td>
            </tr>
            <tr>
                <th>Size</th>
                <td>{{ $size }}</td>
            </tr>
            <tr>
                <th>Color</th>
                <td>{{ $color }}</td>
            </tr>

        </table>

        <div class="actions">
            <a href="{{ $approveUrl }}" class="btn btn-approve">Approve</a>
            <a href="{{ $rejectUrl }}" class="btn btn-reject">Reject</a>
        </div>

        <div class="info">
            <strong>Additional Details:</strong><br>
            - Requested On: {{ now()->format('F j, Y, g:i a') }}<br>
            - Expires On: {{ now()->addHours(24)->format('F j, Y, g:i a') }}<br>
            These links are secure and do not require login. They will expire in 24 hours.
        </div>

        <p class="closing">
            If you have any questions, please contact the system administrator.
        </p>

        <div class="footer">
            Best regards,<br>
            {{ config('app.name') }} Team
        </div>
    </div>
</body>

</html>
