<?php
// Kiểm tra khả năng ghi file
$testFile = __DIR__ . '/images/test.txt';
$testDir = __DIR__ . '/images';

echo "Checking write permissions...<br>";
echo "Current user: " . get_current_user() . "<br>";
echo "Script owner: " . fileowner(__FILE__) . "<br>";
echo "Public directory writable: " . (is_writable(__DIR__) ? 'Yes' : 'No') . "<br>";

// Kiểm tra thư mục images
if (!is_dir($testDir)) {
    echo "Creating images directory...<br>";
    if (mkdir($testDir, 0755, true)) {
        echo "Images directory created successfully.<br>";
    } else {
        echo "Failed to create images directory. Error: " . error_get_last()['message'] . "<br>";
    }
} else {
    echo "Images directory exists.<br>";
    echo "Images directory writable: " . (is_writable($testDir) ? 'Yes' : 'No') . "<br>";
}

// Thử ghi file
echo "Trying to write test file...<br>";
$result = file_put_contents($testFile, "Test content");
if ($result !== false) {
    echo "Successfully wrote " . $result . " bytes to test file.<br>";
    echo "Test file exists: " . (file_exists($testFile) ? 'Yes' : 'No') . "<br>";
    
    // Xóa file test
    if (unlink($testFile)) {
        echo "Test file deleted successfully.<br>";
    } else {
        echo "Failed to delete test file. Error: " . error_get_last()['message'] . "<br>";
    }
} else {
    echo "Failed to write test file. Error: " . error_get_last()['message'] . "<br>";
}

// Kiểm tra OpenSSL
echo "<br>Checking OpenSSL...<br>";
if (extension_loaded('openssl')) {
    echo "OpenSSL extension is loaded.<br>";
    echo "OpenSSL version: " . OPENSSL_VERSION_TEXT . "<br>";
    
    // Kiểm tra hàm openssl_cipher_iv_length
    if (function_exists('openssl_cipher_iv_length')) {
        echo "Function openssl_cipher_iv_length exists.<br>";
        try {
            $length = openssl_cipher_iv_length('aes-256-cbc');
            echo "IV length for aes-256-cbc: " . $length . "<br>";
        } catch (Exception $e) {
            echo "Error calling openssl_cipher_iv_length: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "Function openssl_cipher_iv_length does NOT exist.<br>";
    }
} else {
    echo "OpenSSL extension is NOT loaded.<br>";
}

// Kiểm tra fileinfo
echo "<br>Checking Fileinfo...<br>";
if (extension_loaded('fileinfo')) {
    echo "Fileinfo extension is loaded.<br>";
    
    // Thử sử dụng fileinfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file(__FILE__);
    echo "MIME type of this file: " . $mime . "<br>";
} else {
    echo "Fileinfo extension is NOT loaded.<br>";
}

phpinfo();
