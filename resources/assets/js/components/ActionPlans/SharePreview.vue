<template>
    <div>
        <h4 class="header">{{ $t('action_plan.stepper.behaviors') }}</h4>
        <dot-plot v-bind:reportScore="reportScore" />
        <h4 class="header">{{ $t('action_plan.stepper.action_steps') }}</h4>
        <q-card v-for="action_step in action_plan.action_steps_with_formatted_dates"
            :key="action_step.id">
            <q-card-title v-if="action_step.name_processed">
                {{ action_step.name_processed }}
            </q-card-title>
            <q-card-separator v-if="action_step.name_processed" />
            <q-card-main>
                {{ action_step.description_processed }}
            </q-card-main>
            <q-card-separator />
            <q-card-actions>
                <div class="col">
                    <q-field icon="today"
                        class="text-primary">
                        <q-datetime color="primary"
                            no-clear
                            type="date"
                            :disable="true"
                            format="MMM D, YYYY"
                            v-model="action_step.formatted_dates.due_at.iso8601" />
                    </q-field>
                </div>
                <div style="flex:1 1 auto;"></div>
                <div class="col">
                    <q-field class="mark-complete q-btn-standard">
                        <q-checkbox :value="action_step.pivot.completed_at !== null"
                            :label="$t('action_plan.card.status.complete')"
                            :disable="true"
                            class="pull-right" />
                    </q-field>
                </div>
            </q-card-actions>
        </q-card>
        <h4 class="header">{{ $t('action_plan.stepper.goals') }}</h4>
        <p class="goals-step-label">{{ $t('action_plan.goals.goal.label') }}</p>
        <p class="goals-body">{{ action_plan.goals }}</p>
        <p class="goals-step-label">{{ $t('action_plan.goals.constituents.label') }}</p>
        <p class="goals-body">{{ action_plan.key_constituents }}</p>
        <p class="goals-step-label">{{ $t('action_plan.goals.benefits.label') }}</p>
        <p class="goals-body">{{ action_plan.benefits }}</p>
        <p class="goals-step-label">{{ $t('action_plan.goals.risks.label') }}</p>
        <p class="goals-body">{{ action_plan.risks }}</p>
        <p class="goals-step-label">{{ $t('action_plan.goals.obstacles.label') }}</p>
        <p class="goals-body">{{ action_plan.obstacles }}</p>
        <p class="goals-step-label">{{ $t('action_plan.goals.resources.label') }}</p>
        <p class="goals-body">{{ action_plan.resources }}</p>
        <h4 class="header">{{ $t('action_plan.stepper.reminders') }}</h4>
        <h5>{{ $t('action_plan.reminders.start_end.label') }}</h5>
        <div class="date-label">
            <q-icon name="date_range"
                size="22px" /> {{ action_plan.formatted_dates.starts_at.localized }} - {{ action_plan.formatted_dates.ends_at.localized }}
        </div>
        <div class="row reminders">
            <div class="col">
                <q-field>
                    <q-checkbox :value="typeof(reviewReminder) === 'object'"
                        :disable="true"
                        :label="$t('action_plan.reminders.review')" />
                </q-field>
            </div>
            <div style="flex:1 1 auto;"></div>
            <div class="col">
                <q-field class="text-faded"
                    v-if="reviewReminder">
                    <div class="pull-right">
                        <q-icon name="access_time" size="22px" />
                        {{ $t(reviewReminder.frequency_translated) }}, {{ reviewReminder.formatted_dates.starts_at.localized }}
                    </div>
                </q-field>
            </div>
        </div>
        <div class="row reminders">
            <div class="col">
                <q-field>
                    <q-checkbox :value="typeof(pulseSurveysReminder) === 'object'"
                        :disable="true"
                        :label="$t('action_plan.reminders.feedback')" />
                </q-field>
            </div>
            <div style="flex:1 1 auto;"></div>
            <div class="col">
                <q-field class="text-faded"
                    v-if="pulseSurveysReminder">
                    <div class="pull-right">
                        <q-icon name="access_time" size="22px" />
                        {{ $t(pulseSurveysReminder.frequency_translated) }}, {{ pulseSurveysReminder.formatted_dates.starts_at.localized }}
                    </div>
                </q-field>
            </div>
        </div>
        <div class="row reminders">
            <div class="col">
                <q-field>
                    <q-checkbox :value="actionStepsWithReminder.filter(x => x.reminder).length > 0"
                        :disable="true"
                        :label="$t('action_plan.reminders.reminders_message')" />
                </q-field>
            </div>
        </div>
            <div v-if="actionStepsWithReminder.filter(x => x.reminder).length > 0">
                <q-card v-for="action_step in actionStepsWithReminder"
                        :key="action_step.id">
                    <q-card-title v-if="action_step.name_processed">
                        {{ action_step.name_processed }}
                    </q-card-title>
                    <q-card-separator v-if="action_step.name_processed" />
                    <q-card-main>
                        {{ action_step.description_processed }}
                    </q-card-main>
                    <q-card-separator />
                    <q-card-actions>
                        <div class="col">
                            <q-field icon="today"
                                     class="text-primary">
                                <q-datetime color="primary"
                                            no-clear
                                            type="date"
                                            :disable="true"
                                            format="MMM D, YYYY"
                                            v-model="action_step.formatted_dates.due_at.iso8601" />
                            </q-field>
                        </div>
                        <div style="flex:1 1 auto;"></div>
                        <div class="col">
                            <q-field class="text-faded"
                                     v-if="action_step.reminder">
                                <div class="pull-right">
                                    <q-icon name="access_time" size="22px" />
                                    {{ $t(action_step.reminder.frequency_translated) }}, {{ action_step.reminder.formatted_dates.starts_at.localized }}
                                </div>
                            </q-field>
                        </div>
                    </q-card-actions>
                </q-card>
            </div>
    </div>
</template>

<style lang="scss" scoped>
.caption{
    font-size: 16px;
}

.header{
    font-size: 20px;
    margin: 20px 0 !important;
    font-weight: bold;
}

.goals-step-label{
    font-size: 16px;
    margin-bottom: 0;
    color: rgba(0,0,0,0.54);
}

.reminder-label{
    font-size: 16px;
    margin-bottom: 0;
}

.date-label{
    font-size: 16px;
    margin-bottom: 0;
    color: rgba(0,0,0,0.54);
}

.goals-body{
    font-size: 14px;
    margin-bottom: 2em;
    line-height: 16px;
    font-family: Roboto;
}
</style>

<script>
import {
  QCard,
  QCardMain,
  QCardTitle,
  QCardSeparator,
  QField,
  QCardActions,
  QCheckbox,
  QDatetime,
  QIcon,
} from 'quasar-framework';
import find from 'lodash/find';
import map from 'lodash/map';

import {required as requiredValidator} from 'vuelidate/lib/validators';
import {createServerValidation} from '../../utils/serverValidationMixin';

import DotPlot from './DotPlot';

const serverValidation = createServerValidation({
  parameters: [
    {
      name: 'observers_selected',
      frontValidations: [
        {
          name: 'required',
          validator: requiredValidator,
          errorMsg: function () {
            return this.$t('action_plan.share.select_observer');
          }
        }
      ]
    },
  ],
  initData() {
    return window.data;
  }
});

export default {
  name: 'momentum-action-plans-share-preview',
  mixins: [serverValidation],
  components: {
    QCard,
    QCardMain,
    QCardTitle,
    QCardSeparator,
    QField,
    QCardActions,
    QCheckbox,
    QDatetime,
    QIcon,
    'dot-plot': DotPlot
  },
  data() {
    const data = {
      inFlight: false,
      config: window.frontend_config,
      observers: window.data.observers,
      reportScore: window.data.report_score,
      observers_selected: [],
      reviewReminder: find(window.data.action_plan.action_plan_reminders, x => x.type === 'review'),
      pulseSurveysReminder: find(window.data.action_plan.action_plan_reminders, x => x.type === 'pulse_surveys'),
      actionStepsWithReminder: map(window.data.action_plan.action_steps, actionStep => {
        let actionStepReminder = find(window.data.action_plan.action_plan_reminders, actionPlanReminder => actionPlanReminder.type === 'action_step' && actionPlanReminder.action_step_id === actionStep.id);
        actionStep.reminder = actionStepReminder;
        return actionStep;
      })
    };
    return data;
  },
  methods: {
  },
  computed: {
  },
  mounted() {
  }
};

</script>

