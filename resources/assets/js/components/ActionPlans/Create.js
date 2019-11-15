import merge from 'lodash/merge';

import {
  QCard,
  QCardMain,
  QCardTitle,
  QCardSeparator,
  QBtn,
  QCardActions,
  QStepper,
  QStep,
  QStepperNavigation,
  QField,
  QSelect,
  QOptionGroup,
  QList,
  QListHeader,
  QItem,
  QItemSeparator,
  QCheckbox,
  QDatetime,
  QDatetimeRange,
  QInput,
  QInnerLoading,
  QSpinner,
  QModal,
  Dialog as QDialog,
  date,
  QTabs,
  QTab,
  QTabPane
} from 'quasar-framework';


window.QSelect = QSelect;

import axios from 'axios';
import get from 'lodash/get';
import set from 'lodash/set';
import throttle from 'lodash/throttle';
import uuid from 'uuid/v4';

import isNil from 'lodash/isNil';
// import _ from 'lodash';
import persistentApplicationStateMixin from '../../utils/persistentApplicationStateMixin';
import serverValidationMixin from '../../utils/serverValidationMixin';

import ReminderIntervalPicker from './ReminderIntervalPicker.vue';
import EditActionStep from './EditActionStep.vue';

import queryString from 'query-string';


import DotPlot from './DotPlot';

import {
  required as requiredValidator
} from 'vuelidate/lib/validators';

import withTemplate from './Create.vue-template';
import style from './Create.css';

import normalizeData from './normalize';

const today = new Date();
const {
  addToDate
} = date;

const momentumActionPlansCreate = {
  name: 'momentum-action-plans-edit',
  created() {
    // document.addEventListener('beforeunload', this.beforeUnloadHandler)
    var that = this;
    window.addEventListener('beforeunload', function(e) {
      return that.beforeUnloadHandler(e);
    });

    window.momentumActionPlansCreate = this;
    this.date = date;

    const throttledUpdateActionPlan = throttle(() => {
      this.updateActionPlan();
    },
    2000, {
      'leading': false,
      'trailing': true
    });
    this['throttledUpdateActionPlan'] = throttledUpdateActionPlan;

    // function createActionStepsWatcher(){
    //   let oldVal = [];
    //   return function (newVal, mutation) {
    //     console.log('action_stepsStep.action_steps',newVal);
    //     oldVal = deepClone(newVal);

    //   };
    // }

    // this.$watch('action_stepsStep.action_steps',createActionStepsWatcher(), {
    //   //deap: true
    // });

  },
  components: {
    QCard,
    QCardMain,
    QCardTitle,
    QCardSeparator,
    QBtn,
    QCardActions,
    QStepper,
    QStep,
    QStepperNavigation,
    QField,
    QSelect,
    QOptionGroup,
    QList,
    QListHeader,
    QItem,
    QItemSeparator,
    QCheckbox,
    QDatetime,
    QDatetimeRange,
    QInput,
    QInnerLoading,
    QSpinner,
    QModal,
    QDialog,
    ReminderIntervalPicker,
    EditActionStep,
    DotPlot,
    QTabs,
    QTab,
    QTabPane
  },
  mixins: [
    persistentApplicationStateMixin(),
    serverValidationMixin
  ],
  persistentApplicationState: {
    applicationKey: 'action_plan',
    dataKeys: [
      'step',
      'action_plan_action_steps',
      'behaviorsStep.behavior_ids',
      'action_stepsStep.data',
      'goalsStep.goals',
      'goalsStep.key_constituents',
      'goalsStep.benefits',
      'goalsStep.risks',
      'goalsStep.obstacles',
      'goalsStep.resources',
      'remindersStep.dateRange',
      'remindersStep.action_plan_reminders',
      'remindersStep.action_plan_reminders',
      'remindersStep.pulse_surveys',
      'remindersStep.action_step',
      'tempID',
    ]
  },
  serverValidation: function() {
    const options = {
      parameters: [{
        name: 'behaviorsStep.behavior_ids',
        disabled: false,
        frontValidations: [{
          name: 'required',
          validator: requiredValidator,
          errorMsg: function() {
            return this.$t('global.validation.required', {
              attribute: this.$t('action_plan.stepper.behaviors')
            });
          }
        }]
      },
      {
        name: 'goalsStep.goals',
        frontValidations: [{
          name: 'required',
          validator: requiredValidator,
          errorMsg: function() {
            return this.$t('global.validation.required', {
              attribute: this.$t('action_plan.goals.goal.label')
            });
          }
        }]
      },
      {
        name: 'goalsStep.key_constituents',
        frontValidations: [{
          name: 'required',
          validator: requiredValidator,
          errorMsg: function() {
            return this.$t('global.validation.required', {
              attribute: this.$t('action_plan.goals.constituents.label')
            });
          }
        }]
      },
      {
        name: 'goalsStep.benefits',
        frontValidations: [{
          name: 'required',
          validator: requiredValidator,
          errorMsg: function() {
            return this.$t('global.validation.required', {
              attribute: this.$t('action_plan.goals.benefits.label')
            });
          }
        }]
      },
      {
        name: 'goalsStep.risks',
        frontValidations: [{
          name: 'required',
          validator: requiredValidator,
          errorMsg: function() {
            return this.$t('global.validation.required', {
              attribute: this.$t('action_plan.goals.risks.label')
            });
          }
        }]
      },
      {
        name: 'goalsStep.obstacles',
        frontValidations: [{
          name: 'required',
          validator: requiredValidator,
          errorMsg: function() {
            return this.$t('global.validation.required', {
              attribute: this.$t('action_plan.goals.obstacles.label')
            });
          }
        }]
      },
      {
        name: 'goalsStep.resources',
        frontValidations: [{
          name: 'required',
          validator: requiredValidator,
          errorMsg: function() {
            return this.$t('global.validation.required', {
              attribute: this.$t('action_plan.goals.resources.label')
            });
          }
        }]
      },
      {
        name: 'remindersStep.dateRange.from',
        frontValidations: [{
          name: 'required',
          validator: function(value) {
            return value ? true : false;
          },
          errorMsg: function() {
            return this.$t('global.validation.required', {
              attribute: this.$t('action_plan.reminders.start_end.start.label')
            });
          }
        }]
      },
      {
        name: 'remindersStep.dateRange.to',
        frontValidations: [{
          name: 'required',
          validator: function(value) {
            return value ? true : false;
          },
          errorMsg: function() {
            return this.$t('global.validation.required', {
              attribute: this.$t('action_plan.reminders.start_end.end.label')
            });
          }
        }]
      },
      {
        name: 'remindersStep.action_plan_reminders.review',
        frontValidations: [{
          name: 'reminderSet',
          validator: ReminderIntervalPicker.methods.reminderSetValidator,
          errorMsg: function() {
            return this.$t('global.validation.required', {
              attribute: this.$t('action_plan.reminders.start_end.end.label')
            });
          }
        }]
      },
      {
        name: 'remindersStep.action_plan_reminders.pulse_surveys',
        frontValidations: [{
          name: 'reminderSet',
          validator: ReminderIntervalPicker.methods.reminderSetValidator,
          errorMsg: function() {
            return this.$t('global.validation.required', {
              attribute: this.$t('action_plan.reminders.start_end.end.label')
            });
          }
        }]
      },
      ]
    };

    /**
     * Action plan/steps validations.
     * @since 0.2.5
     * https://monterail.github.io/vuelidate/#sub-collections-validation
     */
    if (this.action_stepsStep && this.action_stepsStep.data) {
      /**
       * Plan validations.
       * @since 0.2.5
       */
      for (var planIndex in this.action_stepsStep.data) {
        options.parameters = options.parameters.concat({
          name: 'action_stepsStep.data.'+planIndex+'.action_steps',
          disabled: false,
          frontValidations: [
            {
              name: 'minLength',
              validator: function(value) {
                return value && value.length && value.length >= 1;
              },
              errorMsg: function() {
                return this.$t('global.validation.min.array', {
                  attribute: this.$t('action_plan.action_steps.picker.label'),
                  min: 1
                });
              }
            },
            {
              name: 'maxLength',
              validator: function(value) {
                return value && value.length && value.length <= 3;
              },
              errorMsg: function() {
                return this.$t('global.validation.max.array', {
                  attribute: this.$t('action_plan.action_steps.picker.label'),
                  max: 3
                });
              }
            },
          ]
        });
        /**
         * Step validations.
         * @since 0.2.5
         */
        for (var stepIndex in this.action_stepsStep.data[planIndex].action_steps) {
          options.parameters = options.parameters.concat({
            name: 'action_stepsStep.data.'+planIndex+'.action_steps.'+stepIndex+'.due_at',
            disabled: false,
            frontValidations: [
              {
                name: 'required',
                validator: requiredValidator,
                errorMsg: function() {
                  return this.$t('global.validation.required', {
                    attribute: this.$t('action_plan.set_due_at.label')
                  });
                }
              },
            ]
          });
          options.parameters = options.parameters.concat({
            name: 'action_stepsStep.data.'+planIndex+'.action_steps.'+stepIndex+'.emphasis',
            disabled: false,
            frontValidations: [{
              name: 'required',
              validator: requiredValidator,
              errorMsg: function() {
                return this.$t('global.validation.required', {
                  attribute: this.$t('action_plan.stepper.behaviors')
                });
              }
            }]
          });
        } // end for steps
      }// end for plans
    }

    return options;
  },
  data() {
    if (!window.data.behaviors && window.behaviors) {
      window.data.behaviors = window.behaviors;
    }

    const normalizeedData = normalizeData(window.data);
    //console.log('normalizeedData',normalizeedData)

    const entities = normalizeedData.entities;
    const data = normalizeedData.result;

    // console.log('entities', entities);
    //console.log('data', data);


    const defaultData = {
      mode: 'create',
      tempID: uuid(),
      behaviorEditable: true,
      inFlight: false,
      step: 'behaviors',
      preventNavigation: true,
      action_plan_id: null,
      behaviorsStep: {
        behavior_ids: [],
      },
      action_stepsStep: {
        data: [{
          behaviorId: null,
          emphasis: null,
          action_steps: []
        }],
        minDate: today,
        maxDate: addToDate(today, {
          days: 356
        })
      },
      goalsStep: {
        goals: null,
        key_constituents: null,
        benefits: null,
        risks: null,
        obstacles: null,
        resources: null,
      },
      remindersStep: {
        dateRange: {
          from: null,
          to: null
        },
        dateRangeMin: today,
        dateRangeMax: addToDate(today, {
          days: 365
        }),
        action_plan_reminders: {
          review: {
            enabled: false,
            frequency: 'never',
            starts_at: null,
          },
          pulse_surveys: {
            enabled: false,
            frequency: 'never',
            starts_at: null,
          },
          action_step: {
            enabled: false,
            action_steps: []
          }
        }
      },
      report_scores: get(window, 'data.report_scores', []),
      behaviors: get(window, 'behaviors', []),
      style: style,
      all_action_steps: data.action_steps.map(id => entities.action_steps[id]),
      action_plan_action_steps: {},
      config: window.frontend_config,
      behaviorsSelected: [],
      behaviorSelectHolder: [],
      selectedTab: '',
      reportScoreExists: false
    };
    const stepFromUrl = get((queryString.parse(location.search)), 'step', defaultData.step);
    //console.log('stepFromUrl',stepFromUrl);


    const existingActionPlanData = {};
    existingActionPlanData['step'] = stepFromUrl;

    if (data.action_plan) {
      //console.log('data.action_plan',data.action_plan);

      // set the temp ID to null if we're editing an existing action plan
      defaultData.tempID = null;

      const action_plan = entities.action_plan[data.action_plan];
      existingActionPlanData['behaviorEditable'] = action_plan.can_edit_behavior;
      // console.log('action_plan', action_plan);

      existingActionPlanData['mode'] = 'update';
      existingActionPlanData['id'] = action_plan.id;

      existingActionPlanData['action_plan_id'] = data.action_plan;

      existingActionPlanData['behaviorsStep'] = {
        behavior_id: action_plan.behavior_id
      };

      existingActionPlanData['action_stepsStep'] = [];
      for (var i = 0; i < action_plan.data.length; i++) {
        existingActionPlanData['action_stepsStep'].push({
          emphasis: action_plan.emphasis,
          action_steps: action_plan.action_steps
            .map(id => {
              return {
                ...entities.action_steps[id],
                ...entities.action_plan_action_steps[id]
              };
            })
        });
      }
      // existingActionPlanData['action_stepsStep'] = {
      //   data: action_plan.data,

      // };
      existingActionPlanData['goalsStep'] = {
        goals: action_plan.goals,
        key_constituents: action_plan.key_constituents,
        benefits: action_plan.benefits,
        risks: action_plan.risks,
        obstacles: action_plan.obstacles,
        resources: action_plan.resources,
      };

      existingActionPlanData['remindersStep'] = {
        dateRange: {
          from: action_plan.starts_at.localized,
          to: action_plan.ends_at.localized
        },
        action_plan_reminders: defaultData.remindersStep.action_plan_reminders
      };

      const action_plan_reminders = action_plan.action_plan_reminders
        .map(id => entities.action_plan_reminders[id]);

      // console.log("Action_Plan_reminders are: ", action_plan_reminders);
      const review_reminder = action_plan_reminders.find(a => a.type === 'review');
      if (review_reminder) {
        set(
          existingActionPlanData,
          'remindersStep.action_plan_reminders.review', {
            enabled: review_reminder.frequency !== 'never',
            frequency: review_reminder.frequency,
            starts_at: review_reminder.starts_at.iso8601,
          });
      }

      const pulse_surveys_reminder = action_plan_reminders.find(a => a.type === 'pulse_surveys');
      if (pulse_surveys_reminder) {
        set(
          existingActionPlanData,
          'remindersStep.action_plan_reminders.pulse_surveys', {
            enabled: pulse_surveys_reminder.frequency !== 'never',
            frequency: pulse_surveys_reminder.frequency,
            starts_at: pulse_surveys_reminder.starts_at.iso8601,
          });
      }


      const action_steps_reminders = action_plan_reminders
        .filter(a => a.type === 'action_step');
      var enableActionSteps = action_steps_reminders.length > 0 ? true : false;
      //console.log('action_steps_reminders',action_steps_reminders);
      //console.log('action_plan.action_steps',action_plan.action_steps);

      //console.log('entities',entities);
      //console.log('ACTION_PLAN_REMINDERS');
      // get(this,'remindersStep.action_plan_reminders.action_step.enabled',false)
      set(
        existingActionPlanData,
        'remindersStep.action_plan_reminders.action_step', {
          enabled: enableActionSteps,
          action_steps: action_plan.action_steps
            .map((action_step_id) => {
              // console.log('action_step_id',action_step_id);

              // const action_step = entities.action_steps[action_step_id];
              // console.log('action_step',action_step);

              const action_plan_reminders = action_plan.action_plan_reminders
                .map(id => entities.action_plan_reminders[id]);

              // console.log('action_plan_reminders',action_plan_reminders);

              const action_plan_reminder = action_plan_reminders
                .find(apr => apr.action_step_id === action_step_id);

              // console.log('action_plan_reminder',action_plan_reminder);

              if (action_plan_reminder) {
                // console.log('action_plan_reminder found!')
                return {
                  id: action_plan_reminder.action_step_id,
                  frequency: action_plan_reminder.frequency,
                  starts_at: action_plan_reminder.starts_at.iso8601,
                };
              } else {
                //console.log('action_plan_reminder NOT found!')
                return {
                  id: action_step_id,
                  frequency: 'never',
                  starts_at: null,
                };
              }
            })
        });


      // console.log('action_plan_reminders', { review_reminder, pulse_surveys_reminder, action_steps_reminders });

    }

    //console.log('existingActionPlanData', existingActionPlanData);
    const applicationStateFromPage = this.getApplicationStateFromPage();
    //console.log('applicationStateFromPage', applicationStateFromPage);

    //console.log(existingActionPlanData);

    const appData = merge({}, defaultData, existingActionPlanData, applicationStateFromPage);
    //Turn off persistent Application State if it is not in the page
    if (appData.mode !== 'create') {
      this.$options.persistentApplicationState.disabled = true;
    }
    return appData;
  },
  methods: {
    /**
     * Returns flag indicating if a behavior ID is selected or not.
     * @param {int} behaviorId
     * @return bool 
     */
    isBehaviorSelected(behaviorId) {
      for (var i in this.behaviorComputed) {
        if (this.behaviorComputed[i] == behaviorId)
          return true;
      }
      return false;
    },
    getReminderMin(action_step = null) {
      const dateRangeFrom = Date.parse(this.remindersStep.dateRange.from);
      const dateRangeMin = Date.parse(this.remindersStep.dateRangeMin);
      const min = dateRangeFrom > dateRangeMin ? dateRangeFrom : dateRangeMin;

      if (action_step == null || action_step == undefined) {
        return min;
      }

      const dueAt = Date.parse(action_step.due_at);
      if (dueAt > min) {
        return action_step.due_at;
      }
      return min;
    },
    onOpenEditActionStep() {
      this.$refs.editActionStep.focusNameField();
    },
    behaviorsPreviousStep() {
      this.navigateTo('/action-plans');
    },
    behaviorsNextStep() {
      this.$v.behaviorsStep.$touch();
      if (this.$v.behaviorsStep.$error) return;
      this.$refs.stepper.next();
    },
    action_stepsPreviousStep() {
      this.$refs.stepper.previous();
    },
    action_stepsNextStep() {
      /*
      TODO TODO TODO
      this.$v.action_stepsStep.$touch();
      if (this.$v.action_stepsStep.$error) return;
      */
      this.$refs.stepper.next();
    },
    goals_stepsPreviousStep() {
      this.$refs.stepper.previous();
    },
    goals_stepsNextStep() {
      this.$v.goalsStep.$touch();
      if (this.$v.goalsStep.$error) return;
      this.$refs.stepper.next();
    },
    updateActionPlan() {
      if (this.$v.$invalid) {
        return;
      }

      const action_plan_reminders = this.remindersStep.action_plan_reminders;

      const reviewReminder = action_plan_reminders.review.enabled && action_plan_reminders.review.frequency !== 'never' ? {
        review: {
          starts_at: action_plan_reminders.review.starts_at,
          frequency: action_plan_reminders.review.frequency
        }
      } : {};

      const pulse_surveysReminder = action_plan_reminders.pulse_surveys.enabled && action_plan_reminders.pulse_surveys.frequency !== 'never' ? {
        pulse_surveys: {
          starts_at: action_plan_reminders.pulse_surveys.starts_at,
          frequency: action_plan_reminders.pulse_surveys.frequency
        }
      } : {};


      //console.log('this.remindersStep.action_plan_reminders.action_step.action_steps', this.remindersStep.action_plan_reminders.action_step.action_steps);

      const action_stepReminder = {
        action_step: this.remindersStep.action_plan_reminders.action_step.action_steps
          .filter(a => a.frequency !== 'never' && this.remindersStep.action_plan_reminders.action_step.enabled)
      };

      return axios
        .post(`/api/action-plans/${this.action_plan_id}/update`, {
          behavior_id: this.behaviorsStep.behavior_id,
          behaviors: this.action_stepsStep.data.map(d => ({
            behaviorId: d.behaviorId,
            emphasis: d.emphasis,
            action_steps: d.action_steps.map(action_step => ({
              due_at: action_step.due_at,
              complete: action_step.complete,
              id: action_step.id,
            }))
          })),
          benefits: this.goalsStep.benefits,
          goals: this.goalsStep.goals,
          key_constituents: this.goalsStep.key_constituents,
          obstacles: this.goalsStep.obstacles,
          resources: this.goalsStep.resources,
          risks: this.goalsStep.risks,
          starts_at: this.remindersStep.dateRange.from,
          ends_at: this.remindersStep.dateRange.to,
          action_plan_reminders: {
            ...reviewReminder,
            ...pulse_surveysReminder,
            ...action_stepReminder
          }
        });
    },
    saveActionPlan() {
      if (this.$v.$invalid) {
        this.$v.$touch();
        return;
      }

      this.inFlight = true;

      const action_plan_reminders = this.remindersStep.action_plan_reminders;

      const reviewReminder = action_plan_reminders.review.enabled && action_plan_reminders.review.frequency !== 'never' ? {
        review: {
          starts_at: action_plan_reminders.review.starts_at,
          frequency: action_plan_reminders.review.frequency
        }
      } : {};

      const pulse_surveysReminder = action_plan_reminders.pulse_surveys.enabled && action_plan_reminders.pulse_surveys.frequency !== 'never' ? {
        pulse_surveys: {
          starts_at: action_plan_reminders.pulse_surveys.starts_at,
          frequency: action_plan_reminders.pulse_surveys.frequency
        }
      } : {};


      // console.log('this.remindersStep.action_plan_reminders.action_step.action_steps',
      //   this.remindersStep.action_plan_reminders.action_step.action_steps);

      const action_stepReminder = {
        action_step: this.remindersStep.action_plan_reminders.action_step.action_steps
          .filter(a => a.frequency !== 'never' && this.remindersStep.action_plan_reminders.action_step.enabled)
      };

      //console.log('action_stepReminder',action_stepReminder.action_step);

      const request = (() => {
        if (this.mode === 'create') {
          return axios
            .post('/api/action-plans', {
              behavior_id: this.behaviorsStep.behavior_id,
              behaviors: this.action_stepsStep.data.map(d => ({
                behavior_id: d.behaviorId,
                emphasis: d.emphasis,
                action_steps: d.action_steps.map(action_step => ({
                  due_at: action_step.due_at.iso8601 ? action_step.due_at.iso8601 : action_step.due_at,
                  complete: action_step.complete,
                  id: action_step.id,
                }))
              })),
              benefits: this.goalsStep.benefits,
              goals: this.goalsStep.goals,
              key_constituents: this.goalsStep.key_constituents,
              obstacles: this.goalsStep.obstacles,
              resources: this.goalsStep.resources,
              risks: this.goalsStep.risks,
              starts_at: this.remindersStep.dateRange.from,
              ends_at: this.remindersStep.dateRange.to,
              action_plan_reminders: {
                ...reviewReminder,
                ...pulse_surveysReminder,
                ...action_stepReminder
              }
            });
        }
        if (this.mode === 'update') {
          return this.updateActionPlan();
        }
        return Promise.reject({
          response: {
            status: null
          }
        });
      })();


      request
        .then(({
          data
        }) => {
          this.$emit('successful');
          this.preventNavigation = false;
          this.$refs.stepper.next();
          if (data && data.redirect) {
            if (data.message) {
              window.sessionStorage.setItem('toast-message', data.message);
            }
            window.location = data.redirect;
          }
        })
        .catch(({
          response
        }) => {
          if (response.status === 422) {
            const errors = get(response, 'data.errors');


            this.errors = {
              ...this.errors,
              ...errors
            };
            if (errors.starts_at) {
              set(this, 'serverErrors.remindersStep.dateRange.from', errors.starts_at.find(x => x));
            }
            if (errors.ends_at) {
              set(this, 'serverErrors.remindersStep.dateRange.to', errors.ends_at.find(x => x));
            }
            this.$v.$touch();
          }
          //console.dir(response);
        })
        .then(() => {
          this.inFlight = false;
        });
    },
    onStep() {
      this.saveApplicationState();
    },
    navigateTo: function(nav) {
      window.location = nav;
    },
    formatDate(dateArg) {
      if (date.isValid(dateArg)) {
        return date.formatDate(dateArg, 'MMM D, YYYY');
      }
      return dateArg;
    },
    getActionStepById(actionStepId) {
      return this.all_action_steps
        .filter(action_step => action_step.id === actionStepId)
        .map(action_step => {
          //console.log(action_step)
          if (typeof action_step.name !== 'string') {
            action_step.name = action_step.name;
          }
          if (typeof action_step.description_key_translated !== 'string') {
            action_step.description_key_translated = action_step.description;
          }
          return action_step;
        })
        .find(action_step => action_step.id === actionStepId);
    },
    getActionStepRemindersById(actionStepId) {
      const action_step_reminders = this.remindersStep.action_plan_reminders.action_step.action_steps;

      if (!action_step_reminders[actionStepId]) {
        this.$set(action_step_reminders, actionStepId, {
          id: actionStepId,
          frequency: null,
          starts_at: null
        });
      }
      return action_step_reminders[actionStepId];
    },
    openHelp() {
      var modeLabel = this.mode == 'create' ? 'create' : 'review';
      window.trackEvent('get_help', 'view', `action_plan.${modeLabel}.${this.step}`);
      window.open(
        this.$t('action_plan.help_url'),
        'newwindow',
        'width=800,height=500'
      );
    },
    open360results() {
      var modeLabel = this.mode == 'create' ? 'create' : 'review';
      window.trackEvent('360_results', 'view', `action_plan.${modeLabel}`);
      window.location = window.user.lea_results_link;
    },
    openResourceGuide() {
      var modeLabel = this.mode == 'create' ? 'create' : 'review';
      window.trackEvent('resource_guide', 'view', `action_plan.${modeLabel}.${this.step}`);
      window.open(
        this.$t('global.lea_resource_guide_url'),
        'newwindow',
        'width=800,height=500'
      );
    },
    action_stepsChange(newValArg) {
      const action_step = this.remindersStep.action_plan_reminders.action_step;

      action_step.action_steps = newValArg.map((action_step) => {
        return {
          id: action_step.id,
          frequency: 'never',
          starts_at: null
        };
      });

      this.$emit('change');
    },
    saveAndFinishLater(event, done) {
      this.saveApplicationState().then(() => {
        done();
        this.navigateTo('/action-plans?message=' + this.$t('action_plan.draft_saved_successfully_message'));
      }).catch(() => {

      });

      // display a toast message
    },
    resetActionSteps() {
      if (this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab || as.behaviorId == null) !== undefined)
        this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab || as.behaviorId == null).action_steps = [];
    },
    beforeUnloadHandler(event) {
      if (this.mode === 'update' && this.preventNavigation == true) {
        event.returnValue = this.$t('action_plan.review.unload.message');
        return this.$t('action_plan.review.unload.message');
      }
    },
    updateActionSteps(data) {
      const normalizeedData = normalizeData(data);
      const results = normalizeedData.result;


      const entities = normalizeedData.entities;
      this.all_action_steps = results.action_steps.map(id => entities.action_steps[id]);

      this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab).action_steps.push({
        ...data.action_step,
        complete: false,
        due_at: null,
        reminder: {
          id: data.action_step.id,
          frequency: 'never',
          starts_at: null
        }
      });
    },
    onActionStepSelectFocus() {
      // console.log(this.$refs);
      const select = this.$refs.actionStepSelect[0];
      const popover = select.$refs.popover;
      if (popover.opened) {
        // set the max width of the popover to the width of the select
        popover.$el.style.maxWidth = select.$el.offsetWidth + 'px';
      }
    },
    // planActionSteps() {
    //   if (this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab))
    //     if (this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab).action_steps)
    //       return this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab).action_steps;
  
    //   return [];
    // }
  },
  watch: {
    'remindersStep': {
      handler: function() {
        this.resetServerErrors();
      },
      deep: true
    },
    behaviorsSelected: {
      handler: function(val) {
        if (val.length > 3) {
          this.behaviorsSelected.splice([this.behaviorsSelected.length - 1], 1);
          return;
        }
        if (this.behaviorsSelected.length != this.behaviorSelectHolder.length) {
          if (this.behaviorsSelected.length > this.behaviorSelectHolder.length) {
            // If a new behavior has been added that isn't in behaviorSelectHolder
            // Just add the new behavior
            this.behaviorSelectHolder = val.slice();
            this.behaviorsStep.behavior_ids = val.slice();
            this.selectedTab = this.reportScore[this.behaviorSelectHolder.length - 1].behavior.id + '';
            this.reportScoreExists = true;
          } else {
            // Otherwise, behavior has been removed so we need to update everything.
            var obj = this.behaviorSelectHolder.filter((item) => {
              return this.behaviorsSelected.indexOf(item) === -1;
            });
            QDialog.create({
              title: this.$t('action_plan.action_steps.emphasis.change_title'),
              message: this.$t('action_plan.action_steps.behavior.change_warning'),
              buttons: [{
                label: this.$t('action_plan.action_steps.emphasis.change_cta'),
                color: 'primary',
                raised: true,
                handler: () => {
                  this.resetActionSteps();
                  if (this.action_stepsStep.data.find(as => as.behaviorId == val || as.behaviorId == null) !== undefined)
                    this.action_stepsStep.data.find(as => as.behaviorId == val || as.behaviorId == null).emphasis = null;
                  this.behaviorSelectHolder.splice(this.behaviorSelectHolder.indexOf(obj[0]), 1);
                  this.behaviorsStep.behavior_ids = this.behaviorSelectHolder.slice();
                  if (this.behaviorSelectHolder.length == 0)
                    this.reportScoreExists = false;
                }
              },
              this.$t('global.cta.cancel'),
              ],
              noBackdropDismiss: true,
              noEscDismiss: true,
              onDismiss: () => {
                this.behaviorsSelected = this.behaviorSelectHolder !== undefined
                  ? this.behaviorSelectHolder.slice()
                  : (this.action_stepsStep.data.length > 0
                    ? this.action_stepsStep.data[0].behaviorId
                    : undefined
                  );
                // Clear steps data
                for (var i in this.action_stepsStep.data) {
                  if (!this.isBehaviorSelected(this.action_stepsStep.data[i].behaviorId))
                    this.action_stepsStep.data.splice(i, 1);
                }
              },
            });

          }
        }

      },
      deep: true
    }
    // 'action_stepsStep.action_steps':{
    //   handler(newVal,oldVal) {
    //     console.log(
    //       'watch action_stepsStep.action_steps: ',newVal,oldVal)
    //   },
    //   deep:true
    // }
  },
  computed: {
    availableActionSteps() {
      const behavior_id = this.selectedTab;
      // console.log(this.selectedTab);
      var emphasis = null;
      if (this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab)) {
        emphasis = this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab).emphasis;
      } else {
        emphasis = null;
      }

      const tempID = this.tempID;
      // const action_plan_id = this.action_plan_id;
      // console.log('availableActionSteps',{action_plan_id,behavior_id,emphasis})

      // console.log(this.all_action_steps.filter((a) => {
      //   if (!(a.behavior_id == behavior_id && a.emphasis == emphasis)) {
      //     return false;
      //   }return true;}));

      if (isNil(behavior_id) || isNil(emphasis)) return [];

      return this.all_action_steps
        .filter((a) => {
          if (!(a.behavior_id == behavior_id && a.emphasis == emphasis)) {
            return false;
          }

          if (!isNil(a.temp_action_plan_id) && a.temp_action_plan_id != tempID) {
            return false;
          }
          return true;
        });
    },

    // selected_action_steps:{
    //   get(){
    //     console.log('get selected_action_steps')
    //     return this.action_stepsStep.action_steps
    //   },
    //   set(action_steps){
    //     console.log('set selected_action_steps',action_steps)
    //     return this.action_stepsStep.action_steps = action_steps
    //   }
    // },
    behaviorsSelectOptions() {
      const behaviorsSelectOptions = this.behaviors.map(behavior => ({
        label: this.$t(behavior.name_key),
        value: behavior.id
      }));
      return behaviorsSelectOptions;
    },
    reportScore() {
      const report_score = this.report_scores.filter(
        result => this.behaviorsStep.behavior_ids.indexOf(result.behavior_id) > -1
      );

      return report_score;
    },
    behavior() {
      return this.behaviors.find(
        behavior => behavior.id === this.behaviorsStep.behavior_id
      );
    },
    action_stepsSelectOptions() {
      const getAvailableActionSteps = this.$options.computed.availableActionSteps.bind(this);

      const result = getAvailableActionSteps()
        .map(actionStep => {

          // console.log('actionStep', actionStep.id);
          // console.log(this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab).action_steps.map(x => x.id))

          const currentActionStep = this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab).action_steps
            .find(x => x.id === actionStep.id);

          let label = actionStep.name;
          if (actionStep.name == null || actionStep.name == undefined) {
            label = actionStep.description;
          }

          if (currentActionStep) {
            console.log('found ', currentActionStep.id);
            return {
              value: currentActionStep,
              label: label
            };
          }

          const complete = actionStep.complete || false;
          const due_at = actionStep.due_at || null;


          return {
            value: {
              ...actionStep,
              complete: complete,
              due_at: due_at,
              reminder: {
                id: actionStep.id,
                frequency: 'never',
                starts_at: null
              }
            },
            label: label
          };
        });
      return result;
    },
    emphasissSelectOptions() {
      return [{
        label: this.$t('action_plan.action_steps.emphasis.more'),
        value: 'more'
      },
      {
        label: this.$t('action_plan.action_steps.emphasis.less'),
        value: 'less'
      }
      ];
    },
    emphasisComputed: {
      get: function() {
        if (this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab))
          return this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab).emphasis;

        return null;
      },
      set: function(value) {
        if (!this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab)) {
          this.action_stepsStep.data.push({
            behaviorId: this.selectedTab,
            emphasis: null,
            action_steps: []
          });
        }
        var emphasis = this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab).emphasis;
        if (emphasis !== null && emphasis != value) {
          QDialog.create({
            title: this.$t('action_plan.action_steps.emphasis.change_title'),
            message: this.$t('action_plan.action_steps.emphasis.change_warning'),
            buttons: [{
              label: this.$t('action_plan.action_steps.emphasis.change_cta'),
              color: 'primary',
              raised: true,
              handler: () => {
                this.resetActionSteps();
                this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab).emphasis = value;
              }
            },
            this.$t('global.cta.cancel'),
            ]
          });
        } else {
          this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab).emphasis = value;
        }
      }
    },
    behaviorComputed: {
      get: function() {
        return this.behaviorsStep.behavior_ids;
      },
      set: function(value) {
        var behavior_id = this.behaviorsStep.behavior_ids.includes(value);
        if (behavior_id) {
          QDialog.create({
            title: this.$t('action_plan.action_steps.emphasis.change_title'),
            message: this.$t('action_plan.action_steps.behavior.change_warning'),
            buttons: [{
              label: this.$t('action_plan.action_steps.emphasis.change_cta'),
              color: 'primary',
              raised: true,
              handler: () => {
                this.resetActionSteps();
                this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab).emphasis = null;
                this.behaviorsStep.behavior_id = value;
              }
            },
            this.$t('global.cta.cancel'),
            ]
          });
        } else {
          this.behaviorsStep.behavior_ids.push(value);
        }
      }
    },
    behaviorTabs() {
      var result = {};
      for (var i = 0; i < this.reportScore.length; i++) {
        result.name = 'tab-' + i;
        result.score = this.reportScore[i];
      }
      return result;
    },
    emphasisExists() {
      if (this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab))
        if (this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab).emphasis !== null)
          return true;

      return false;
    },
    planActionSteps() {
      if (this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab))
        if (this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab).action_steps)
          return this.action_stepsStep.data.find(as => as.behaviorId == this.selectedTab).action_steps;

      return [];
    }
  },
  mounted() {
    //this.loadApplicationState();
    //console.log('mounted');
    this.$on('change', () => {
      if (this.mode === 'create') {
        this.throttledSaveApplicationState();
      }
      if (this.mode === 'update') {
        this.throttledUpdateActionPlan();
      }

    });
    this.$on('saveAppState', this.saveApplicationState);
    //this.$refs.createActionStepModal.open();
    this.behaviorsSelected = this.behaviorsStep.behavior_ids;
  }
};
export default withTemplate(momentumActionPlansCreate);