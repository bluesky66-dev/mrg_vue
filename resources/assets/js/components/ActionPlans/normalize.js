import omit from 'lodash/omit';
import transform from 'lodash/transform';
import get from 'lodash/get';


import { normalize, schema } from 'normalizr';


export default (data) => {
  //console.log('data', data);
  class FormattedDate {
    constructor(value = { iso8601: null, localized: null, timestamp: null }) {
      this.iso8601 = value.iso8601;
      this.localized = value.localized;
      this.timestamp = value.timestamp;
    }
    asDate() {
      return new Date(this.timestamp * 1000);
    }
    toString() {
      return this.asDate().toString();
    }
    isNull() {
      return this.timestamp === null ? true : false;
    }
  }

  const flattenDate = (processStrategy = x => x) => (entity) => {
    const processedEntity = processStrategy(entity);
    return {
      ...omit(processedEntity, ['formatted_dates']),
      ...transform(processedEntity.formatted_dates, function(result, value, key) {
        //console.log(result, value, key);
        result[key] = new FormattedDate(value);
        return result;
      }, {}),

    };
  };

  const processedValues = (processStrategy = x => x) => (entity) => {
    const entityprocessed = processStrategy(entity);
    const processed = {};
    if (entityprocessed.description_processed) {
      processed['description'] = entityprocessed.description_processed;
    }
    if (entityprocessed.description_processed) {
      processed['name'] = entityprocessed.name_processed;
    }
    return {
      ...entityprocessed,
      ...processed
    };
  };

  const userSchema = new schema.Entity('user', {}, {
    processStrategy: flattenDate()
  });
  const pulse_survey_resultsSchema = new schema.Entity('pulse_survey_results', {}, {
    processStrategy: flattenDate()
  });
  const pulse_survey_results_completedSchema = new schema.Entity('pulse_survey_results_completed', {}, {
    processStrategy: (entity) => ({
      ...entity,
      completed: true
    })
  });
  const pulse_survey_results_openSchema = new schema.Entity('pulse_survey_results', {}, {
    processStrategy: (entity) => ({
      ...entity,
      open: true
    })
  });

  const pulse_surveysSchema = new schema.Entity('pulse_surveys', {
    pulse_survey_results: [pulse_survey_resultsSchema],
    pulse_survey_results_completed: [pulse_survey_results_completedSchema],
    pulse_survey_results_open: [pulse_survey_results_openSchema]
  });
  const action_plan_remindersSchema = new schema.Entity('action_plan_reminders', {}, {
    processStrategy: flattenDate()
  });

  const action_stepsSchema = new schema.Entity('action_steps', {}, {
    processStrategy: flattenDate(processedValues())
  });

  const action_plan_action_stepsSchema = new schema.Entity('action_plan_action_steps', {}, {
    processStrategy: flattenDate(processedValues((entity) => {
      return ({
        id: entity.id,
        action_step_id: entity.id,
        complete: get(entity, 'formatted_dates.completed_at.iso8601',null) == null ? false : true,
        completed_at: get(entity, 'formatted_dates.completed_at.iso8601',null),
        due_at: get(entity, 'formatted_dates.due_at.iso8601',null)
      });
    }))
  });

  const behaviorSchema = new schema.Entity('behavior', {}, {
    processStrategy: flattenDate()
  });

  const current_pulse_surveySchema = new schema.Entity('pulse_surveys', {
    pulse_survey_results: [pulse_survey_resultsSchema],
    pulse_survey_results_completed: [pulse_survey_results_completedSchema],
    pulse_survey_results_open: [pulse_survey_results_openSchema]
  }, {
    processStrategy: (entity) => ({
      ...entity,
      current: true
    })
  });

  const action_planSchema = new schema.Entity('action_plan', {
    user: userSchema,
    pulse_surveys: [pulse_surveysSchema],
    action_plan_reminders: [action_plan_remindersSchema],
    action_steps_with_formatted_dates: [action_plan_action_stepsSchema],
    behavior: behaviorSchema,
    current_pulse_survey: current_pulse_surveySchema
  }, {
    processStrategy: processedValues(flattenDate((value) => {
      return {
        ...omit(value, []),
      };
    }))
  });

  const report_scoresSchema = new schema.Entity('report_scores', {
    behavior: behaviorSchema
  }, {
    processStrategy: flattenDate()
  });

  const application_stateSchema = new schema.Entity('application_state', {
    behavior: behaviorSchema
  }, {
    processStrategy: flattenDate()
  });

  const normalizeData = normalize(data, {
    action_plan: action_planSchema,
    action_steps: [action_stepsSchema],
    report_scores: [report_scoresSchema],
    application_state: application_stateSchema
  });

  //Make action_steps_with_formatted_dates into action_steps
  if (
    normalizeData &&
    normalizeData.entities &&
    normalizeData.entities.action_plan &&
    normalizeData.result &&
    normalizeData.result.action_plan) {

    const action_plan = normalizeData.entities.action_plan[normalizeData.result.action_plan];
    action_plan.action_steps = action_plan.action_steps_with_formatted_dates;
    delete action_plan.action_steps_with_formatted_dates;
    delete action_plan.action_steps_complete;

  }


  return normalizeData;
};
