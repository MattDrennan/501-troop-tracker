<?php

declare(strict_types=1);

echo "Running Characterization Tests...\n\n";

/**
 * An array of all characterization test files to be executed.
 * Test files are discovered by recursively finding all files in the current directory
 * and subdirectories ending with "-test.php".
 */
$test_files = [];
$directory_iterator = new \RecursiveDirectoryIterator(__DIR__, \RecursiveDirectoryIterator::SKIP_DOTS);
$iterator = new \RecursiveIteratorIterator($directory_iterator, \RecursiveIteratorIterator::SELF_FIRST);

foreach ($iterator as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '-test.php')) {
        $test_files[] = $file->getPathname();
    }
}

// Sort the files to ensure a consistent execution order.
sort($test_files);

$all_tests_passed = true;

foreach ($test_files as $test_file) {
    // Get the relative path from the current directory for clearer test identification.
    $test_name = str_replace(__DIR__ . DIRECTORY_SEPARATOR, '', $test_file);
    $output = [];
    $exit_code = 0;

    // Execute the test script via the command line and capture its output and exit code.
    exec('php ' . escapeshellarg($test_file), $output, $exit_code);

    if ($exit_code === 0) {
        echo "✅ PASS: {$test_name}\n";
    } else {
        echo "❌ FAIL: {$test_name}\n";
        // Print the detailed error output from the failed script.
        echo "-------------------- OUTPUT --------------------\n";
        echo implode("\n", $output) . "\n";
        echo "----------------------------------------------\n\n";
        $all_tests_passed = false;
    }
}

if ($all_tests_passed) {
    echo "\nAll characterization tests passed!\n";
    exit(0); // Success
} else {
    echo "\nSome characterization tests failed.\n";
    exit(1); // Failure
}
