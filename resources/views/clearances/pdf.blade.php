<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Staff Clearance Form</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 6px; }
        .header { text-align: center; border-bottom: 1px solid #ddd; padding-bottom: 6px; margin-bottom: 10px; }
        .header img { height: 40px; }
        .status { font-weight: bold; }
        .status.rejected { color: #b91c1c; }
        .status.approved { color: #166534; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f1f5f9; }
        .footer { margin-top: 12px; font-size: 11px; color: #555; border-top: 1px solid #ddd; padding-top: 6px; }
    </style>
    </head>
<body>
    <div class="header">
        @php($logoPath = public_path('images/asset-logo.svg'))
        @if(!empty($company?->logo) && file_exists(public_path($company->logo)))
            @php($logoPath = public_path($company->logo))
        @endif
        <img src="{{ $logoPath }}" alt="Logo" />
        <div>Staff Clearance Form</div>
    </div>
    <p>
        <strong>Staff:</strong> {{ $clearance->staff->name }}<br>
        <strong>Status:</strong> <span class="status {{ $clearance->status === 'rejected' ? 'rejected' : ($clearance->status === 'approved' ? 'approved' : '') }}">{{ strtoupper($clearance->status) }}</span><br>
        <strong>Requested:</strong> {{ optional($clearance->created_at)->format('Y-m-d') }}
        @if($clearance->approved_at)<br><strong>Approved:</strong> {{ $clearance->approved_at->format('Y-m-d') }} @endif
    </p>
    @if($clearance->remarks)
        <p><strong>Remarks:</strong> {{ $clearance->remarks }}</p>
    @endif
    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Asset</th>
            <th>Action</th>
            <th>Result</th>
            <th>Notes</th>
        </tr>
        </thead>
        <tbody>
        @foreach($clearance->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ optional($item->asset)->asset_tag }} {{ optional($item->asset)->description ? ('- '.optional($item->asset)->description) : '' }} {{ $item->description }}</td>
                <td>{{ $clearance->status === 'rejected' ? "—" : ($item->action ?? "—") }}</td>
                <td>
                    @if(!empty($item->result))
                        {{ strtoupper($item->result) }}
                    @elseif($clearance->status === 'rejected')
                        REJECTED (not received)
                    @else
                        —
                    @endif
                </td>
                <td>{{ $item->condition_note }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="footer">
        Generated on {{ now()->format('Y-m-d H:i') }}
        @if(!empty($company?->hr_email)) • HR: {{ $company->hr_email }} @endif
    </div>
</body>
</html>

