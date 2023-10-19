<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-10-19 19:28:34
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-20 01:51:37
 * @FilePath: /smanga-php/app/Http/Controllers/UnCompressTools.php
 */

namespace App\Http\Controllers;

class UnCompressTools extends Controller
{
    public static function ext_zip($zipFile, $coverImagePath)
    {
        $zip = new \ZipArchive();

        if ($zip->open($zipFile) === TRUE) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);

                // Check if the file has an image extension (e.g., jpg, png, gif)
                $imageExtensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');
                $ext = pathinfo($filename, PATHINFO_EXTENSION);

                if (in_array(strtolower($ext), $imageExtensions)) {
                    // This is an image file, you can use it as a cover
                    $coverImageContents = $zip->getFromIndex($i);

                    // Save the cover image to a local file
                    return file_put_contents($coverImagePath, $coverImageContents);

                    break;
                }
            }

            $zip->close();
        } else {
            // echo 'Failed to open the ZIP file.';
            return 0;
        }
    }

    public static function ext_rar($rarFile, $extractedFolder, $newName)
    {
        putenv('LANG=en_US.UTF-8');
        // Run the unrar command to list the contents of the RAR file
        $set_chatset = "LC_ALL=en_US.UTF-8 locale charmap;";
        $command = "unrar lb \"{$rarFile}\" 2>&1";
        exec($command, $fileList);

        $coverImage = null;

        // Iterate through the file list to find the first image
        foreach ($fileList as $file) {
            // Check if the file has an image extension (e.g., jpg, png, gif)
            $imageExtensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');
            $ext = pathinfo($file, PATHINFO_EXTENSION);

            if (in_array(strtolower($ext), $imageExtensions)) {
                // This is an image file, you can use it as a cover
                $coverImage = basename($file);
                break;
            }
        }

        if ($coverImage !== null) {
            // Run the unrar command to extract the cover image
            exec("unrar e -o+ \"{$rarFile}\" \"{$extractedFolder}\" \"*{$coverImage}\"");

            // Move the extracted cover image to the local path
            rename("$extractedFolder/$coverImage", "$extractedFolder/$newName");

            return true;
        } else {
            return false;
        }
    }
}
