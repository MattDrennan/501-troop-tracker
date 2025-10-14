<?php

function assertEquals(string $expected, string $actual): void
{
    $expected_lines = explode("\n", $expected);
    $actual_lines = explode("\n", $actual);
    $line_count = max(count($expected_lines), count($actual_lines));

    for ($i = 0; $i < $line_count; $i++) {
        $expected_line = $expected_lines[$i] ?? '[Line does not exist]';
        $actual_line = $actual_lines[$i] ?? '[Line does not exist]';

        // Use trim to ignore subtle trailing whitespace differences
        if (trim($expected_line) !== trim($actual_line)) {
            $line_number = $i + 1;
            $error = "Output does not match. First difference on line {$line_number}:\n\n";
            $error .= "--- EXPECTED ---\n{$expected_line}\n\n";
            $error .= "+++ ACTUAL +++\n{$actual_line}\n";
            echo $error;
            exit(1);
        }
    }
}
/**
 * Derives the expected output file path from the test script's path.
 * e.g., "login-page-test.php" -> "login-page-output.html.txt"
 *
 * @param string $test_file_path The full path to the test file (__FILE__).
 * @return string The full path to the corresponding expected output file.
 */
function getExpectedContents(string $test_file_path, ?string $file_type = 'html'): string
{
    $base_name = basename($test_file_path, '-test.php');
    $output_file_name = $base_name . "-{$file_type}.txt";
    $path = dirname($test_file_path) . DIRECTORY_SEPARATOR . $output_file_name;

    return trim(file_get_contents($path));
}