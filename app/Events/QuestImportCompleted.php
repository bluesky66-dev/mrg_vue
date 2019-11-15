<?php

namespace Momentum\Events;

use Illuminate\Queue\SerializesModels;

/**
 * Event called when quest import command has been completed.
 *
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class QuestImportCompleted
{
    use SerializesModels;
    /**
     * Users related to quest.
     * @since 0.2.5
     *
     * @var array
     */
    public $users;
    /**
     * Imported files.
     * @since 0.2.5
     *
     * @var Collection
     */
    public $files;
    /**
     * Constructor.
     * @since 0.2.5
     */
    public function __construct($users, $files)
    {
        $this->users = $users;
        $this->files = $files;
    }
}