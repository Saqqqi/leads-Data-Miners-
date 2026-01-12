<?php
/**
 * Export Leads to CSV - Fixed Version
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="leads_' . date('Y-m-d_H-i-s') . '.csv"');

if (!isset($_POST['leads_data'])) {
    die("No data provided");
}

$leads = json_decode($_POST['leads_data'], true);
if (!$leads) {
    die("Invalid data format");
}

// Improved formatting function
function formatForCSV($data) {
    if (empty($data)) return '';
    $values = array_map('trim', explode(',', $data));
    $filtered = array_filter($values);
    
    // Excel needs \r\n for proper line breaks within cells
    return implode("\r\n", $filtered);
}

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Headers
fputcsv($output, [
    '#', 'Lead Name', 'Location', 
    'Phone Numbers', 'Primary Number', 
    'Email', 'Date'
], ',', '"');

// Data rows
foreach ($leads as $index => $lead) {
    fputcsv($output, [
        $index + 1,
        $lead['lead_name'] ?? '',
        $lead['lead_location'] ?? '',
        formatForCSV($lead['phone_numbers'] ?? ''),
        formatForCSV($lead['primaryNumber'] ?? ''),
        formatForCSV($lead['emails'] ?? ''),
        $lead['lead_date'] ?? ''
    ], ',', '"');
}

fclose($output);
exit();
?>