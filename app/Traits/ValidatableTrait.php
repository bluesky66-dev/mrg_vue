<?php

namespace Momentum\Traits;

use Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rule;

/**
 * Trait that self-validation functionality in a model.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
trait ValidatableTrait
{
    /**
     * Model property that defines the rules to be applied per attribute.
     * @since 0.2.4
     * 
     * @link https://laravel.com/docs/5.6/validation#available-validation-rules
     * 
     * @var array
     */
    protected $rules = [];

    /**
     * List of validaton rules remembered for future validations.
     * @since 0.2.4
     * 
     * @var array
     */
    protected $validation_current_rules = [];

    /**
     * List of model messages.
     * NOTE: No use on trait aside from definition.
     * @since 0.2.4
     * 
     * @var array
     */
    protected $messages = [];

    /**
     * List of errors found during validation.
     * @since 0.2.4
     * 
     * @var MessageBag
     */
    protected $validation_errors;

    /**
     * Flag that indicates if model has been validated already, meaning if
     * the validation method has been ran at least once.
     * @since 0.2.4
     * 
     * @var boolean
     */
    protected $validation_has_validated = false;

    /**
     * Default implementation of the selfValidate function. It will pass it's
     * current properties to the validate function with default options.
     * This method is intended to be overloaded in the child class, although
     * it is not abstract, as not all models need complicated validation logic
     * beyond the defaults.
     * @since 0.2.4
     *
     * @return boolean|\Illuminate\Support\MessageBag
     */
    public function selfValidate()
    {
        return $this->validate($this->toArray());
    }

    /**
     * Validates the current model against the remembered rules
     * @since 0.2.4
     *
     * @return boolean|\Illuminate\Support\MessageBag
     */
    public function validateRemembered()
    {
        return $this->validate($this->toArray(), $this->validation_current_rules);
    }

    /**
     * Validates the passed data against the passed rules.
     * @since 0.2.4
     *
     * @param array              $data     Data to validate.
     * @param mixed|array|string $rule_set Additional set of rule(s).
     * @param boolean            $merge    Flag that indicates if the `$rule_set` param should be
     *                                     merged with the trait's `$rules` for this validation.
     * 
     * @return boolean|\Illuminate\Support\MessageBag
     */
    public function validate(array $data, $rule_set = null, $merge = true)
    {
        // set a property indicating that validation has been run
        $this->validation_has_validated = true;

        // due to the way unique rules are handled, they cannot be configured as
        // class constants, and instead need to be replaced with a validation
        // rule at run-time. This cannot be done with a custom validator either
        // as the validator doesn't have access to the model to get the
        // table name
        $validation_rules = $this->getRules($rule_set, $merge);

        // check to see if any of the rules are "ignoredunique" and replace it
        // with a custom built unique ignore rule
        foreach ((array)$this->getRules($rule_set, $merge) as $key => $rule) {
            if (strpos($rule, "ignoredunique") !== false) {
                $rules = explode('|', $rule);
                $validation_rules[$key] = [];
                foreach ($rules as $r) {
                    if ($r == "ignoredunique") {
                        $validation_rules[$key][] = Rule::unique($this->getTable())->ignore($this->id)->where(function($query) {
                            $query->whereNull('deleted_at');
                        });
                    } else {
                        $validation_rules[$key][] = $r;
                    }
                }
            }
        }

        // create a validator from the BaseModel with the rules and messages
        $validator = Validator::make($data, $validation_rules, $this->getMessages());

        // if the validator fails, return the validator object
        if ($validator->fails()) {
            $this->setErrors($validator->errors());

            // if this is ajax, run the validator so that it produces the correct HTTP response (array of errors)
            if (request()->expectsJson()) {
                $validator->validate();
            }

            return $validator->errors();
        }

        return true;
    }

    /**
     * Returns flag indicating if model is valid.
     * @since 0.2.4
     *
     * @return boolean
     */
    public function isValid()
    {
        if ($this->validation_has_validated === false) {
            return null;
        }

        return empty($this->validation_errors);
    }

    /**
     * Returns the errors found during the validation of the model.
     * @since 0.2.4
     *
     * @return array|MessageBag
     */
    public function getErrors()
    {
        return $this->validation_errors ?: new MessageBag;
    }

    /**
     * Sets error messages.
     * @since 0.2.4
     *
     * @param \Illuminate\Support\MessageBag $validation_errors Errors to set.
     */
    public function setErrors(MessageBag $validation_errors)
    {
        $this->validation_errors = $validation_errors;
    }

    /**
     * Adds an error to the errors message bag.
     * @since 0.2.4
     *
     * @param mixed|string|int $key     Data key that identifies who the error is related to.
     * @param string           $message Error message to add.
     */
    public function addError($key, $message)
    {
        if (!$this->validation_errors instanceof MessageBag) {
            $this->validation_errors = new MessageBag;
        }
        $this->validation_errors->add($key, $message);
    }

    /**
     * Returns the validation rules for a model based on a passed key and
     * whether to merge the rules with the default rules or not/
     * @since 0.2.4
     *
     * @param mixed|string|array $key   Key or list of rule keys.
     * @param boolean            $merge Flag that indicates if the key should be merged with
     *                                  those defined at property `$rules`.
     * @return mixed|array|boolean
     */
    public function getRules($key = null, $merge = true)
    {
        $rules = [];
        // if an array of keys was passed, return all of the rules merged
        if (is_array($key)) {
            foreach ($key as $rule_set) {
                if (isset($this->rules[$rule_set])) {
                    $rules[] = $this->rules[$rule_set];
                }
            }

            // if merge is true, and we haven't already merged in the default rules
            // we want to add the default rules
            if ($merge === true && !isset($key['default'])) {
                if (isset($this->rules['default'])) {
                    $rules[] = $this->rules['default'];
                }
            }
            return call_user_func_array('array_merge', $rules);
        }

        if ($key) {
            if ($merge) {
                // check to make sure these rules exist
                if (!isset($this->rules[$key])) {
                    return null;
                }

                // since we are merging, lets make sure that the default rules exist
                // if the default rules don't exist, return just the keyed rules
                if (!isset($this->rules['default'])) {
                    return $this->rules[$key];
                }

                return array_merge($this->rules['default'], $this->rules[$key]);
            }

            return isset($this->rules[$key]) ? $this->rules[$key] : null;
        }
        return isset($this->rules['default']) ? $this->rules['default'] : null;
    }

    /**
     * Returns model messages.
     * @since 0.2.4
     * 
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Adds a rule set by key to the current rules to validate.
     * @since 0.2.4
     *
     * @param string $key Rule key to add.
     */
    public function rememberRule($key)
    {
        if (!isset($this->validation_current_rules[$key])) {
            $this->validation_current_rules[] = $key;
        }
    }

    /**
     * Removes a rule set from the current rules to validate.
     * @since 0.2.4
     *
     * @param string $key Rule key to remove.
     */
    public function forgetRule($key)
    {
        if (isset($this->validation_current_rules[$key])) {
            unset($this->validation_current_rules[$key]);
        }
    }

    /**
     * Removes all rules the current rules list to validate.
     * @since 0.2.4
     */
    public function forgetAllRules()
    {
        $this->validation_current_rules = [];
    }

    /**
     * Adds a new validation rule to the `$rules` property.
     * This is only available during runtime.
     * @since 0.2.4
     *
     * @param string $field      Attribute field name.
     * @param string $validation New validation rule to add.
     * @param string $set        Identifies to which set of rules this should be added.
     */
    public function addRule($field, $validation, $set = 'default')
    {
        $this->rules[$set][$field] = $validation;
    }
}
