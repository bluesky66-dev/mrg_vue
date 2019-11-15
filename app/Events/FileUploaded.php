<?php

namespace Momentum\Events;

use Momentum\Media;
use Illuminate\Queue\SerializesModels;

/**
 * Event called whenever a file is uploaded.
 *
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class FileUploaded
{
    use SerializesModels;
    /**
     * Application state.
     * @since 0.2.5
     *
     * @var File
     */
    public $file;
    /**
     * Application state.
     * @since 0.2.5
     *
     * @var \Momentum\Media 
     */
    public $media;
    /**
     * Constructor.
     * @since 0.2.5
     *
     * @param \File           $file  File uploaded.
     * @param \Momentum\Media $media Media used to upload.
     */
    public function __construct($file, Media $media = null)
    {
        $this->file = $file;
        $this->media = $media;
    }
}