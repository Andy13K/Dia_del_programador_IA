<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ReportFilterRequest;
use App\Models\Department;
use App\Services\ReportService;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Muestra la vista interactiva principal de reportes con filtros y datos consolidados.
     */
    public function index(ReportFilterRequest $request, ReportService $reportService): View
    {
        $type = (string) $request->input('type', 'departamental');
        $startDate = $request->filled('start_date') ? (string) $request->input('start_date') : null;
        $endDate = $request->filled('end_date') ? (string) $request->input('end_date') : null;
        $departmentId = $request->filled('department_id') ? (int) $request->input('department_id') : null;

        $departments = Department::orderBy('name')->get();
        $reportData = $reportService->getReportData($type, $startDate, $endDate, $departmentId, $request->user());

        return view('reports.index', compact('reportData', 'departments'));
    }

    /**
     * Descarga el reporte en formato Excel estructurado (.xls HTML Spreadsheet) con encabezado institucional y logo.
     */
    public function exportExcel(ReportFilterRequest $request, ReportService $reportService): Response
    {
        $type = (string) $request->input('type', 'departamental');
        $startDate = $request->filled('start_date') ? (string) $request->input('start_date') : null;
        $endDate = $request->filled('end_date') ? (string) $request->input('end_date') : null;
        $departmentId = $request->filled('department_id') ? (int) $request->input('department_id') : null;

        $reportData = $reportService->getReportData($type, $startDate, $endDate, $departmentId, $request->user());
        $content = $reportService->buildExcelHtml($reportData);

        $timestamp = now()->timezone('America/Guatemala')->format('Ymd_His');
        $filename = "reporte_kinsolar_{$type}_{$timestamp}.xls";

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * Descarga el reporte en formato CSV plano con BOM UTF-8.
     */
    public function exportCsv(ReportFilterRequest $request, ReportService $reportService): Response
    {
        $type = (string) $request->input('type', 'departamental');
        $startDate = $request->filled('start_date') ? (string) $request->input('start_date') : null;
        $endDate = $request->filled('end_date') ? (string) $request->input('end_date') : null;
        $departmentId = $request->filled('department_id') ? (int) $request->input('department_id') : null;

        $reportData = $reportService->getReportData($type, $startDate, $endDate, $departmentId, $request->user());
        $content = $reportService->buildCsv($reportData);

        $timestamp = now()->timezone('America/Guatemala')->format('Ymd_His');
        $filename = "reporte_kinsolar_{$type}_{$timestamp}.csv";

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * Muestra la plantilla ejecutiva imprimible optimizada para PDF / Impresora con encabezado, logo y firmas.
     */
    public function printPdf(ReportFilterRequest $request, ReportService $reportService): View
    {
        $type = (string) $request->input('type', 'departamental');
        $startDate = $request->filled('start_date') ? (string) $request->input('start_date') : null;
        $endDate = $request->filled('end_date') ? (string) $request->input('end_date') : null;
        $departmentId = $request->filled('department_id') ? (int) $request->input('department_id') : null;

        $reportData = $reportService->getReportData($type, $startDate, $endDate, $departmentId, $request->user());
        $autoPrint = $request->boolean('auto_print', false);

        return view('reports.print', compact('reportData', 'autoPrint'));
    }
}
