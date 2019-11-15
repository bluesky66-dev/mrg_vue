<?php

namespace Momentum;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Momentum\Interfaces\CanReceiveNotifications;
use Momentum\Traits\ReceivesNotificationsTrait;
use Spatie\Permission\Traits\HasRoles;

/**
 * User model.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class User extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    CanReceiveNotifications
{
    use Authenticatable, Authorizable, CanResetPassword, Notifiable, HasApiTokens, ReceivesNotificationsTrait, HasRoles;

    /**
     * Fillable attributes during batch creation.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'culture_id',
        'organization_id',
        'billing_organization_id',
        'quest_user_id',
        'password',
        'remember_token',
        'token_expires_at',
    ];

    /**
     * Hidden attributes during model casting.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Validation rules per attribute.
     * @since 0.2.4
     *
     * @see \Momentum\Traits\ValidatableTrait
     *
     * @var array 
     */
    protected $rules = [
        'default' => [
            'first_name'              => 'required',
            'last_name'               => 'required',
            'email'                   => 'required|email|unique:users,email',
            'culture_id'              => 'required|exists:cultures,id',
            'organization_id'         => 'required|exists:organizations,id',
            'billing_organization_id' => 'required|exists:organizations,id',
            'quest_user_id'           => 'require|integer',
        ],
    ];

    /**
     * Dynamic attributes appended for casting.
     * @since 0.2.4
     *
     * @var array 
     */
    protected $appends = [
        'lea_results_link',
        'can_create_action_plan',
        'can_create_pulse_surveys',
        'full_name',
        'formatted_dates',
    ];

    /**
     * Returns and creates relationship with `ActionPlan` model.
     * This will return the plans associated with the user.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function action_plans()
    {
        return $this->hasMany(ActionPlan::class);
    }

    /**
     * Returns open plans.
     * @since 0.2.4
     * 
     * @see \Momentum\User::action_plans
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function open_action_plans()
    {
        return $this->action_plans()->whereNull('completed_at');
    }

    /**
     * Returns completed plans.
     * @since 0.2.4
     * 
     * @see \Momentum\User::action_plans
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function completed_action_plans()
    {
        return $this->action_plans()->whereNotNull('completed_at');
    }

    /**
     * Returns and creates relationship with `ActionPlanReminder` model through the `ActionPlan` model.
     * This will return the reminders associated with the user.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasManyThrough
     */
    public function action_plan_reminders()
    {
        return $this->hasManyThrough(ActionPlanReminder::class, ActionPlan::class);
    }

    /**
     * Returns and creates relationship with `AnalyticsEvent` model.
     * This will return the analytics events associated with the user.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function analytics_events()
    {
        return $this->hasMany(AnalyticsEvent::class);
    }

    /**
     * Returns and creates relationship with `Organization` model.
     * This will return the organization (for billing) associated with the user.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function billing_organization()
    {
        return $this->belongsTo(Organization::class, 'billing_organization_id');
    }

    /**
     * Returns and creates relationship with `Culture` model.
     * This will return the culture associated with the user.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function culture()
    {
        return $this->belongsTo(Culture::class);
    }

    /**
     * Returns and creates relationship with `JournalEntry` model.
     * This will return the journal entries associated with the user.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function journal_entries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    /**
     * Returns and creates relationship with `Observer` model.
     * This will return the observers associated with the user.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function observers()
    {
        return $this->hasMany(Observer::class);
    }

    /**
     * Returns and creates relationship with `Organization` model.
     * This will return the organization associated with the user.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Returns and creates relationship with `PulseSurvey` model.
     * This will return the pulse surveys associated with the user.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pulse_surveys()
    {
        return $this->hasMany(PulseSurvey::class);
    }

    /**
     * Returns and creates relationship with `Report` model.
     * This will return the reports associated with the user.
     * @since 0.2.4
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Returns the first and active report found and related to the user.
     * @since 0.2.4
     * 
     * @return \Momentum\User::reports
     * 
     * @return \Momentum\Report
     */
    public function getActiveReport()
    {
        return $this->reports()->where('status', 'active')->first();
    }

    /**
     * Returns dynamic attribute `leaResultsLink`.
     * Returns the download route/link for the active report related to the user.
     * @since 0.2.4
     * 
     * @return \Momentum\User::getActiveReport
     * 
     * @return string
     */
    public function getLeaResultsLinkAttribute()
    {
        $report = $this->getActiveReport();

        if (!$report) {
            return null;
        }

        return route('reports.download', ['id' => $report->id]);
    }

    /**
     * Returns dynamic attribute `fullName`.
     * Returns a concatenation between `first_name` and `last_name` attributes.
     * @since 0.2.4
     * 
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Returns dynamic attribute `canCreateActionPlan`.
     * Returns flag indicating if action plans can be created or not.
     * @since 0.2.4
     * 
     * @return boolean
     */
    public function getCanCreateActionPlanAttribute()
    {
        return $this->open_action_plans()->count() < 3;
    }

    /**
     * Returns dynamic attribute `canCreatePulseSurveys`.
     * Returns flag indicating if pulse surveys can be created or not.
     * @since 0.2.4
     * 
     * @return boolean
     */
    public function getCanCreatePulseSurveysAttribute()
    {
        $action_plans_with_open_pulse_surveys_count = $this->open_action_plans()->whereHas('pulse_surveys', function($q){
            $q->whereNull('completed_at');
        })->count();

        return $action_plans_with_open_pulse_surveys_count < $this->open_action_plans()->count();
    }

    /**
     * Generates a magic token and saves it to the users table.
     * @since 0.2.4
     *
     * @return string
     */
    public function generateMagicToken()
    {
        $token = Str::random(60);
        $this->magic_token = $token;
        $this->token_expires_at = Carbon::now()->addSeconds(config('momentum.token_expiration'));
        $this->save();

        return $token;
    }
}
