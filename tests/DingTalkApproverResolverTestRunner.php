<?php

use App\Services\DingTalk\DingTalkApproverResolver;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Load test data
$testData = require __DIR__.'/TestData/DingTalkApproverResolverFullResults.php';

$resolver = new DingTalkApproverResolver();

// Test Area flows
echo "Testing Area Flows:\n";
foreach ($testData['area_flow_results']['cases'] as $case) {
    $result = $resolver->resolveAreaFlow($case['input'] + ['fixtures' => $case['fixtures']]);
    
    echo "Case: {$case['name']}\n";
    echo "- Expected Approvers: ".implode(',', $case['expected']['approvers'])."\n";
    echo "- Actual Approvers: ".implode(',', $result['approvers'])."\n";
    echo "- Expected CC: ".implode(',', $case['expected']['cc_list'])."\n";
    echo "- Actual CC: ".implode(',', $result['cc_list'])."\n\n";
}

// Test Backoffice flows
echo "Testing Backoffice Flows:\n";
foreach ($testData['backoffice_flow_results']['cases'] as $case) {
    $result = $resolver->resolveBackofficeFlow($case['input'] + ['fixtures' => $case['fixtures']]);
    
    echo "Case: {$case['name']}\n";
    echo "- Expected Approvers: ".implode(',', $case['expected']['approvers'])."\n";
    echo "- Actual Approvers: ".implode(',', $result['approvers'] ?? [])."\n";
    echo "- Expected CC: ".implode(',', $case['expected']['cc_list'])."\n";
    echo "- Actual CC: ".implode(',', $result['cc_list'] ?? [])."\n\n";
}
