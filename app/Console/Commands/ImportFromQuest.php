<?php

namespace Momentum\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use League\Csv\Reader;
use Momentum\Behavior;
use Momentum\Culture;
use Momentum\Enums\Agreements;
use Momentum\Enums\ObserverTypes;
use Momentum\Enums\ReportStatuses;
use Momentum\ImportLog;
use Momentum\Notifications\OnboardEmail;
use Momentum\Observer;
use Momentum\Organization;
use Momentum\Report;
use Momentum\ReportScore;
use Momentum\User;
use Momentum\Events\QuestImportCompleted;
use Momentum\Events\Organization\OrganizationCreated;
use Momentum\Events\Organization\OrganizationUpdated;
use DB;

/**
 * Imports Users, Organizations, Observers, and Report Scores from Quest.
 *
 * @author Atom team
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class ImportFromQuest extends Command
{
    private $files_collection;
    private $organizations = [];
    private $users = [];
    private $reports = [];
    private $report_scores = [];
    private $observers = [];
    /**
     * The name and signature of the console command.
     * @since 0.2.4
     *
     * @var string
     */
    protected $signature = 'import';

    /**
     * The console command description.
     * @since 0.2.4
     *
     * @var string
     */
    protected $description = 'Imports Users, Organizations, Observers, and Report Scores from Quest.';

    /**
     * Execute the console command.
     * @since 0.2.4
     * @since 0.2.5 Event calls.
     *
     * @return mixed
     */
    public function handle()
    {
        // read all the files in the import path
        $files = scandir(config('import.import_path'));

        // filter out anything that's not a csv file
        $files = array_filter($files, function ($value) {
            return substr($value, -4) === '.csv';
        });

        // sort all the files so the newest are on top
        usort($files, function ($a, $b) {
            // explode the string on underscores (removing the file extensions at the end)
            $a_date = explode('_', substr($a, 0, strpos($a, '.')));

            $a_date = (new Carbon)->setDateTime($a_date[1], $a_date[2], $a_date[3], $a_date[4], $a_date[5],
                $a_date[6])->format('u');

            // explode the string on underscores (removing the file extensions at the end)
            $b_date = explode('_', substr($b, 0, strpos($b, '.')));

            $b_date = (new Carbon)->setDateTime($b_date[1], $b_date[2], $b_date[3], $b_date[4], $b_date[5],
                $b_date[6])->format('u');

            if ($a_date == $b_date) {
                return 0;
            }

            return $a_date > $b_date ? 1 : -1;
        });

        $files_collection = new Collection($files);

        $files_collection = $files_collection->map(function ($file) {
            $group = explode('_', $file)[0];
            return [
                'file'  => $file,
                'group' => $group,
            ];
        });

        $this->files_collection = $files_collection->groupBy('group');

        // start a transaction
        DB::beginTransaction();


        // figure out what files we have, and read them in order
        // read the organizations csv
        // read the users csv
        // read the observers csv
        // read the reports scores csv

        // check if that user already exists but has a different pqp_id (report id)
        // if this is the case, we want to set their previous reports to inactive, and
        // create a new report that is active. This is the report we're going to link
        // all of the observers and report scores to

        //------------------------------------------------------------
        //                        ORGANIZATIONS
        //------------------------------------------------------------

        /*
         * OrgID_FK
         * OrgName
         * LogoFileName
         */

        $organization_field_map = [
            'quest_organization_id',
            'name',
            'logo',
        ];

        $organizations = $this->files_collection->get(config('import.organizations_csv_prefix'));
        if ($organizations) {
            foreach ($organizations as $file) {
                $started_at = Carbon::now();
                $csv = Reader::createFromPath(config('import.import_path') . DIRECTORY_SEPARATOR . $file['file']);
                $csv->setHeaderOffset(0);
                foreach ($csv->getRecords($organization_field_map) as $record) {
                    $organization = Organization::updateOrCreate(['quest_organization_id' => $record['quest_organization_id']],
                        [
                            'quest_organization_id' => $record['quest_organization_id'],
                            'name'                  => $record['name'],
                            'logo'                  => $record['logo'] == '' ? null : $record['logo'],
                        ]);
                    // Trigger event
                    event($organization->wasRecentlyCreated
                        ? new OrganizationCreated($organization)
                        : new OrganizationUpdated($organization)
                    );
                    $this->organizations[] = $organization;
                }

                $this->logComplete($file['file'], $started_at);
            }
        }

        //------------------------------------------------------------
        //                            USERS
        //------------------------------------------------------------

        /*
         * ParticipantID_PK
         * FirstName
         * LastName
         * Email
         * CultureID_FK (Culture find by quest_culture_id)
         * ParticipantToQuestionnaireToProjectID_FK (PQP_ID)
         * OrgID_FK (Organization find by quest_organization_id)
         * BillingOrgID_FK (Organization find by quest_organization_id)
         * ReportHistoryID_FK (becomes new Report object)
         * ReportFile (saved on Report Object)
         */

        $users_field_map = [
            'quest_user_id',
            'first_name',
            'last_name',
            'email',
            'quest_culture_id',
            'quest_pqp_id',
            'quest_organization_id',
            'quest_billing_organization_id',
            'quest_report_id',
            'quest_report_file',
        ];

        $users = $this->files_collection->get(config('import.participants_csv_prefix'));

        if ($users) {
            foreach ($users as $file) {
                $started_at = Carbon::now();
                $csv = Reader::createFromPath(config('import.import_path') . DIRECTORY_SEPARATOR . $file['file']);
                $csv->setHeaderOffset(0);
                foreach ($csv->getRecords($users_field_map) as $record) {
                    $organization = Organization::where('quest_organization_id',
                        $record['quest_organization_id'])->first();

                    $billing_organization = Organization::where('quest_organization_id',
                        $record['quest_billing_organization_id'])->first();

                    $culture = Culture::where('quest_culture_id', $record['quest_culture_id'])->first();

                    // if organization, billing_organization, or culture are null we can't import anything
                    // add a log that we failed
                    if (!$organization) {
                        return $this->abort($file['file'], $started_at,
                            sprintf('Organization ID %s from Quest could not be mapped.',
                                $record['quest_organization_id']));
                    }

                    if (!$billing_organization) {
                        return $this->abort($file['file'], $started_at,
                            sprintf('Billing Organization ID %s from Quest could not be mapped.',
                                $record['quest_billing_organization_id']));
                    }

                    if (!$culture) {
                        return $this->abort($file['file'], $started_at,
                            sprintf('Culture ID %s from Quest could not be mapped.', $record['quest_culture_id']));
                    }

                    // create the user (or find them)
                    $user = User::updateOrCreate([
                        'quest_user_id' => $record['quest_user_id'],
                    ], [
                        'quest_user_id'           => $record['quest_user_id'],
                        'first_name'              => $record['first_name'],
                        'last_name'               => $record['last_name'],
                        'email'                   => $record['email'],
                        'culture_id'              => $culture->id,
                        'organization_id'         => $organization->id,
                        'billing_organization_id' => $billing_organization->id,
                    ]);

                    $this->users[] = $user;

                    // we need to check if the report is new, or if it already exists
                    $existing_report = Report::where('quest_pqp_id', $record['quest_pqp_id'])->first();

                    if ($existing_report) {
                        return $this->abort($file['file'], $started_at,
                            sprintf('User (%s) has already been imported with the pqp id: %s.',
                                $record['email'], $record['quest_pqp_id']));
                    }

                    // set any old reports to inactive
                    $user->reports()->each(function ($report) {
                        $report->status = ReportStatuses::INACTIVE;
                        $report->save();
                    });

                    // make a new report object
                    $this->reports[] = Report::create([
                        'user_id'         => $user->id,
                        'file'            => $record['quest_report_file'],
                        'quest_report_id' => $record['quest_report_id'],
                        'quest_pqp_id'    => $record['quest_pqp_id'],
                        'status'          => ReportStatuses::ACTIVE,
                    ]);
                }

                $this->logComplete($file['file'], $started_at);
            }

        }
        //------------------------------------------------------------
        //                          OBSERVERS
        //------------------------------------------------------------

        /*
         * FirstName
         * LastName
         * ParticipantID_PK
         * Email
         * ReportHistoryID
         * MRGObserverTypeID (map to enum)
         * TargetParticipantID_FK (User find by quest_user_id)
         * CultureID
         */

        $observer_field_map = [
            'first_name',
            'last_name',
            'quest_observer_id',
            'email',
            'quest_report_id',
            'quest_observer_type',
            'quest_target_user_id',
            'quest_culture_id',
        ];

        $observers = $this->files_collection->get(config('import.observers_csv_prefix'));
        if ($observers) {
            foreach ($observers as $file) {
                $started_at = Carbon::now();
                $csv = Reader::createFromPath(config('import.import_path') . DIRECTORY_SEPARATOR . $file['file']);
                $csv->setHeaderOffset(0);
                foreach ($csv->getRecords($observer_field_map) as $record) {
                    $target_user = User::where('quest_user_id', $record['quest_target_user_id'])->first();

                    $observer_type = ObserverTypes::mapFromQuest($record['quest_observer_type']);

                    $culture = Culture::where('quest_culture_id', $record['quest_culture_id'])->first();

                    $report = Report::where('quest_report_id', $record['quest_report_id'])->first();

                    if (!$target_user) {
                        return $this->abort($file['file'], $started_at,
                            sprintf('Target User ID %s from Quest could not be mapped.',
                                $record['quest_target_user_id']));
                    }

                    if (!$observer_type) {
                        return $this->abort($file['file'], $started_at,
                            sprintf('Observer Type %s from Quest could not be mapped.',
                                $record['quest_observer_type']));
                    }

                    if (!$culture) {
                        return $this->abort($file['file'], $started_at,
                            sprintf('Culture ID %s from Quest could not be mapped.', $record['quest_culture_id']));
                    }

                    if (!$report) {
                        return $this->abort($file['file'], $started_at,
                            sprintf('Report ID %s from Quest could not be mapped.', $record['quest_report_id']));
                    }

                    $this->observers[] = Observer::create([
                        'first_name'          => $record['first_name'],
                        'last_name'           => $record['last_name'],
                        'quest_observer_id'   => $record['quest_observer_id'],
                        'email'               => $record['email'],
                        'observer_type' =>      $observer_type,
                        'user_id'             => $target_user->id,
                        'report_id'           => $report->id,
                        'culture_id'          => $culture->id,
                    ]);
                }

                $this->logComplete($file['file'], $started_at);
            }
        }

        //------------------------------------------------------------
        //                        REPORT SCORES
        //------------------------------------------------------------

        /*
         * ReportHistoryID_FK (Report find by quest_report_id)
         * DimensionID_FK (Culture find by quest_culture_id)
         * Dimension (ignored)
         * BossNorm
         * SelfNorm
         * PeerNorm
         * DRNorm
         * SortOrder (ignored)
         * BossAgreement (map to enum)
         * PeerAgreement (map to enum)
         * DirectReportAgreement (map to enum)
         */

        $report_scores_field_map = [
            'quest_report_id',
            'quest_behavior_id',
            'behavior',
            'boss_norm',
            'self_norm',
            'peer_norm',
            'direct_report_norm',
            'boss_agreement',
            'peer_agreement',
            'direct_report_agreement',
        ];

        $report_scores = $this->files_collection->get(config('import.report_scores_csv_prefix'));
        if ($report_scores) {
            foreach ($report_scores as $file) {
                $started_at = Carbon::now();
                $csv = Reader::createFromPath(config('import.import_path') . DIRECTORY_SEPARATOR . $file['file']);
                $csv->setHeaderOffset(0);
                foreach ($csv->getRecords($report_scores_field_map) as $record) {
                    $behavior = Behavior::where('quest_behavior_id', $record['quest_behavior_id'])->first();

                    $report = Report::where('quest_report_id', $record['quest_report_id'])->first();

                    if (!$behavior) {
                        return $this->abort($file['file'], $started_at,
                            sprintf('Behavior ID %s from Quest could not be mapped.', $record['quest_behavior_id']));
                    }

                    if (!$report) {
                        return $this->abort($file['file'], $started_at,
                            sprintf('Report ID %s from Quest could not be mapped.', $record['quest_report_id']));
                    }

                    $this->report_scores[] = ReportScore::create([
                        'report_id'               => $report->id,
                        'behavior_id'             => $behavior->id,
                        'boss_norm'               => $record['boss_norm'],
                        'self_norm'               => $record['self_norm'],
                        'peer_norm'               => $record['peer_norm'],
                        'direct_report_norm'      => $record['direct_report_norm'],
                        'boss_agreement'          => Agreements::mapFromQuest($record['boss_agreement']),
                        'peer_agreement'          => Agreements::mapFromQuest($record['peer_agreement']),
                        'direct_report_agreement' => Agreements::mapFromQuest($record['direct_report_agreement']),
                    ]);
                }

                $this->logComplete($file['file'], $started_at);
            }
        }

        // if we made it this far, we want to commit to the database
        DB::commit();

        // move all the CSVs to the archive location
        $this->files_collection->each(function ($group) {
            $group->each(function ($file) {
                $this->archive($file['file']);
            });
        });

        $this->moveAssets();

        $this->onboardUsers();
        event(new QuestImportCompleted($this->users, $this->files_collection));

    }

    /**
     * Archives a file.
     * @since 0.2.4
     *
     * @param string $file Filename to archive.
     */
    private function archive($file)
    {
        $old_file = config('import.import_path') . DIRECTORY_SEPARATOR . $file;
        $new_file = config('import.import_archive_path') . DIRECTORY_SEPARATOR . $file;

        $this->moveFile($old_file, $new_file);
    }

    /**
     * Aborts command, rollbacks started database transaction and sets every 
     * file in collection as failure.
     * @since 0.2.4
     *
     * @param string $file       Filename who caused the abortion.
     * @param string $started_at Timestamp for when file started being processed.
     * @param string $error      Error found.
     */
    private function abort($file, $started_at, $error)
    {
        // first we want to rollback any changes that were going to be made
        DB::rollback();

        // create a log for the error
        $this->logError($file, $started_at, $error);

        // move all the CSVs to the failure location
        $this->files_collection->each(function ($group) {
            $group->each(function ($file) {
                $this->failure($file['file']);
            });
        });
    }

    /**
     * Archives a file into the failure path.
     * @since 0.2.4
     *
     * @param string $file Filename to archive as failure.
     */
    private function failure($file)
    {
        $old_file = config('import.import_path') . DIRECTORY_SEPARATOR . $file;
        $new_file = config('import.import_failure_path') . DIRECTORY_SEPARATOR . $file;

        $this->moveFile($old_file, $new_file);
    }

    /**
     * Adds a new import record into the database log.
     * @since 0.2.4
     *
     * @param string $file       Filename of the imported file.
     * @param string $started_at Timestamp for when file started being processed.
     */
    private function logComplete($file, $started_at)
    {
        ImportLog::create([
            'file_name'    => $file,
            'started_at'   => $started_at,
            'completed_at' => Carbon::now(),
        ]);
    }

    /**
     * Adds a new import record (failed) into the database log.
     * @since 0.2.4
     *
     * @param string $file       Filename of the failed file.
     * @param string $started_at Timestamp for when file started being processed.
     */
    private function logError($file, $started_at, $error)
    {
        ImportLog::create([
            'file_name'  => $file,
            'started_at' => $started_at,
            'error_at'   => Carbon::now(),
            'error'      => $error,
        ]);
    }

    /**
     * Moves the Report PDFs and Organization Logos into their proper locations
     * @since 0.2.4
     */
    private function moveAssets()
    {
        foreach ($this->reports as $report) {
            $old_file = config('import.import_assets_reports_path') . DIRECTORY_SEPARATOR . $report->file;
            $new_file = config('momentum.reports_path') . DIRECTORY_SEPARATOR . $report->file;

            $this->moveFile($old_file, $new_file);
        }

        foreach ($this->organizations as $organization) {
            // not every organization has a logo
            if (!$organization->logo) {
                continue;
            }

            $old_file = config('import.import_assets_logos_path') . DIRECTORY_SEPARATOR . $organization->logo;
            $new_file = config('momentum.logos_path') . DIRECTORY_SEPARATOR . $organization->logo;

            $this->moveFile($old_file, $new_file);
        }
    }

    /**
     * Moves a file from location.
     * @since 0.2.4
     *
     * @param string $old_file Old file (path and filename).
     * @param string $new_file New file (path and filename).
     */
    private function moveFile($old_file, $new_file)
    {
        if (!file_exists($old_file)) {
            return;
        }

        if (!is_dir(dirname($new_file))) {
            mkdir(dirname($new_file), 0755, true);
        }

        // move the file to the archive folder
        rename($old_file, $new_file);
    }

    /**
     * Sends "Onboard" notifiation to users related to quest.
     * @since 0.2.4
     */
    private function onboardUsers()
    {
        foreach ($this->users as $user) {
            $token = $user->generateMagicToken();
            $user->sendNotification(new OnboardEmail($user, $token));
        }
    }
}
