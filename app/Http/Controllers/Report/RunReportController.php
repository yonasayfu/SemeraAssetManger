<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;

class RunReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {
    }

    public function preview(Request $request): JsonResponse
    {
        $data = $request->validate([
            'family' => ['required', 'string'],
            'filters' => ['nullable', 'array'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $filters = $data['filters'] ?? [];
        $limit = $data['limit'] ?? 25;

        try {
            $query = $this->reportService->getReportQuery($data['family'], $filters);
        } catch (\InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        $rows = $query
            ->limit($limit)
            ->get()
            ->map(fn ($model) => $model->toArray());

        return response()->json([
            'data' => $rows,
            'count' => $rows->count(),
        ]);
    }

    public function export(Request $request)
    {
        $data = $request->validate([
            'family' => ['required', 'string'],
            'filters' => ['nullable', 'array'],
            'format' => ['nullable', 'string', 'in:csv'],
        ]);

        $filters = $data['filters'] ?? [];
        $format = $data['format'] ?? 'csv';

        try {
            $query = $this->reportService->getReportQuery($data['family'], $filters);
        } catch (\InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        $rows = $query->get()->map(fn ($model) => $model->toArray());

        if ($format === 'csv') {
            return $this->streamCsv($rows, $data['family']);
        }

        return response()->json([
            'message' => 'Unsupported export format.',
        ], 422);
    }

    protected function streamCsv(Collection $rows, string $family)
    {
        $filename = sprintf('%s-report-%s.csv', $family, now()->format('Ymd_His'));

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');

            $headersWritten = false;

            foreach ($rows as $row) {
                $flat = $this->flattenRow($row);

                if (! $headersWritten) {
                    fputcsv($handle, array_keys($flat));
                    $headersWritten = true;
                }

                fputcsv($handle, array_values($flat));
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'X-Report-Filename' => $filename,
        ]);
    }

    protected function flattenRow(array $row): array
    {
        $flat = Arr::dot($row);

        return collect($flat)
            ->mapWithKeys(function ($value, $key) {
                $column = str_replace('.', ' ', $key);

                if (is_array($value)) {
                    $value = json_encode($value);
                }

                return [$column => is_scalar($value) ? $value : json_encode($value)];
            })
            ->toArray();
    }
}
