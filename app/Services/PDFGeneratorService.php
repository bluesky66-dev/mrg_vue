<?php

namespace Momentum\Services;

use GuzzleHttp\Client;
use Storage;

/**
 * PDF generation service.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
class PDFGeneratorService
{
    /**
     * Converts a document/file into a PDF and returns the file path
     * pointing to the converted file.
     * @since 0.2.4
     *
     * @param string $url  Url.
     * @param string $name Filename.
     * 
     * @return string
     */
    public static function documentToPDF($url, $name)
    {
        $filename = config('momentum.pdf_path') . DIRECTORY_SEPARATOR . $name . '.pdf';
        $filepath =  storage_path('app' . DIRECTORY_SEPARATOR . $filename);
        $command = config('momentum.pdf_generator_command') . ' --url "' . $url . '" > ' . $filepath;
        shell_exec($command);
        return $filepath;
    }
}