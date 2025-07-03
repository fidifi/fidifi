<?php

/**
 * Coverage Analysis Script
 * Parses the clover.xml file and identifies uncovered lines
 */

$cloverFile = 'tests/coverage/clover.xml';

if (!file_exists($cloverFile)) {
    echo "Coverage file not found. Run tests with --coverage first.\n";
    exit(1);
}

$xml = simplexml_load_file($cloverFile);
$uncoveredLines = [];
$totalLines = 0;
$coveredLines = 0;

foreach ($xml->project->package as $package) {
    foreach ($package->file as $file) {
        $filename = (string)$file['name'];
        
        // Skip files we don't want to analyze
        if (strpos($filename, 'vendor/') !== false || 
            strpos($filename, 'tests/') !== false ||
            strpos($filename, 'database/') !== false ||
            strpos($filename, 'bootstrap/') !== false ||
            strpos($filename, 'config/') !== false) {
            continue;
        }
        
        // Only analyze app files
        if (strpos($filename, '/app/') === false) {
            continue;
        }
        
        $fileLines = [];
        foreach ($file->line as $line) {
            $lineNumber = (int)$line['num'];
            $count = (int)$line['count'];
            $type = (string)$line['type'];
            
            if ($type === 'stmt') {
                $totalLines++;
                if ($count > 0) {
                    $coveredLines++;
                } else {
                    $fileLines[] = $lineNumber;
                }
            }
        }
        
        if (!empty($fileLines)) {
            $uncoveredLines[$filename] = $fileLines;
        }
    }
}

$coverage = $totalLines > 0 ? ($coveredLines / $totalLines) * 100 : 0;

echo "=== COVERAGE REPORT ===\n";
echo sprintf("Total Coverage: %.2f%% (%d/%d lines)\n\n", $coverage, $coveredLines, $totalLines);

if ($coverage < 100) {
    echo "UNCOVERED LINES:\n";
    echo "================\n";
    foreach ($uncoveredLines as $file => $lines) {
        echo "\n$file:\n";
        echo "Lines: " . implode(', ', $lines) . "\n";
    }
    
    echo "\n\nTO ACHIEVE 100% COVERAGE:\n";
    echo "=========================\n";
    echo "1. Review uncovered lines above\n";
    echo "2. Write tests that execute those code paths\n";
    echo "3. Consider edge cases and error conditions\n";
    echo "4. Re-run coverage analysis\n";
} else {
    echo "🎉 100% COVERAGE ACHIEVED!\n";
}