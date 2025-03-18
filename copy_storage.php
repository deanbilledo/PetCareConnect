<?php
// Script to copy files from storage/app/public to public/storage
$source = __DIR__ . '/storage/app/public';
$destination = __DIR__ . '/public/storage';

// Create destination directory if it doesn't exist
if (!file_exists($destination)) {
    mkdir($destination, 0755, true);
}

// Function to copy directory contents recursively
function copyDirectory($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    
    while (($file = readdir($dir)) !== false) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                copyDirectory($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    
    closedir($dir);
}

// Copy files
copyDirectory($source, $destination);
echo "Storage files copied successfully!"; 