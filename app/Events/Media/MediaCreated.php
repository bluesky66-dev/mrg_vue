<?php

namespace Momentum\Events\Media;

use Illuminate\Queue\SerializesModels;
use Momentum\Media;

/**
 * Event called when a media record is created.
 *
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class MediaCreated
{
    use SerializesModels;
    /**
     * Media.
     * @since 0.2.5
     *
     * @var \Momemtum\Media
     */
    public $media;
    /**
     * Constructor.
     * @since 0.2.5
     * 
     * @param \Momemtum\Media $media
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }
}