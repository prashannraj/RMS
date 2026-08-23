<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = Illuminate\Support\Facades\DB::select(
    "SELECT TABLE_NAME AS name FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME"
);

echo "=== TABLES (" . count($tables) . ") ===\n";
foreach ($tables as $t) {
    echo $t->name . "\n";
}

echo "\n=== PPSC EXPECTED TABLES CHECK ===\n";
$expected = [
    'states', 'districts', 'local_bodies', 'castes', 'religions', 'mother_tongues',
    'physically_challenged_classes', 'samuha_bargas', 'boards', 'faculties', 'qualifications',
    'services', 'groups', 'sub_groups', 'posts', 'post_combinations',
    'organizations', 'quotas', 'master_data_curriculums', 'requisitions',
    'advertisement_codes', 'advertisements', 'master_divisions', 'master_configurations',
    'candidates', 'candidate_addresses', 'candidate_extra_details',
    'candidate_education_details', 'candidate_samuha_bargas',
    'applications', 'challans', 'application_status_histories',
    'exam_centers', 'exam_center_allocations', 'rooms', 'invigilator_posts', 'invigilators',
    'invigilator_allocations', 'papers', 'exam_schedulings', 'candidate_exams', 'admit_cards',
    'documents', 'file_uploads', 'audit_logs',
];

$tableNames = array_column($tables, 'name');
foreach ($expected as $table) {
    $found = in_array($table, $tableNames, true);
    echo ($found ? 'OK  ' : 'MISS') . '  ' . $table . "\n";
}