<template>
  <div id="momentum-dashboard" class="momentum-dashboard">
    <template v-if="!data.action_plans.length">
      <q-card-main class="layout-padding">
        {{ $t('dashboard.message.empty') }}
      </q-card-main>
      <q-card-actions class="layout-padding">
        <q-btn 
          big
          color="primary"
          @click="navigateTo('/action-plans/create')">
            {{ $t('dashboard.cta.empty_primary') }}
        </q-btn>
        <q-btn
          big
          flat
          color="primary"
          @click="navigateTo('/profile')">
          {{ $t('dashboard.cta.empty_secondary') }}
        </q-btn>
      </q-card-actions>
    </template>
    <q-tabs class="momentum-dashboard__tabs">
      <q-tab
        v-for="(item, index) in actionPlanOptions"
        :default="index === 0"
        slot="title"
        :name="item.value"
        :label="item.label"
        :key="index"
        @select="selectActionPlan(item.value)"
      />
      <q-tab
        slot="title"
        name="tab-4"
        label="+ Create New Action Plan"
        class="new-action"
      />
      <q-tab-pane
        v-for="(item, index) in actionPlanOptions"
        :name="item.value"
        :key="index"
      >
        <ActionPlan
          :selectedActionPlan="selectedActionPlan"
          :currentActionPlan="currentActionPlan"
          :inFlight="inFlight"
        ></ActionPlan>
      </q-tab-pane>
    </q-tabs>
  </div>
</template>
<script>
import {
  QTabs,
  QTab,
  QTabPane,
  QRouteTab,
  QCard,
  QCardMain,
  QCardTitle,
  QCardSeparator,
  QSelect,
  QBtn,
  QField,
  QKnob,
  QSpinner,
  QInnerLoading,
  Toast,
  QCardActions,
  QTooltip
} from 'quasar-framework';

import axios from 'axios';

import ActionPlan from './ActionPlan';

export default {
  name: 'momentum-dashboard',
  components: {
    QTabs,
    QTab,
    QTabPane,
    QRouteTab,
    QCard,
    QCardMain,
    QCardTitle,
    QCardSeparator,
    QSelect,
    QBtn,
    QField,
    QKnob,
    QSpinner,
    QInnerLoading,
    Toast,
    QCardActions,
    QTooltip,
    ActionPlan,
  },
  data() {
    const data = {
      inFlight: false,
      config: window.frontend_config,
      data: window.data,
      user: window.user,
      selectedActionPlan: window.data.action_plans.length ? window.data.action_plans.find(x => x).id : null
    };
    return data;
  },

  methods: {
    openHelp() {
      if (!this.data.action_plans.length) {
        window.trackEvent('get_help', 'view', 'dashboard.empty');
      } else if (!this.currentActionPlan.current_pulse_survey) {
        window.trackEvent('get_help', 'view', 'dashboard.no_surveys');
      }else if (this.currentActionPlan.current_pulse_survey && !this.currentActionPlan.complete_pulse_surveys.length){
        window.trackEvent('get_help', 'view', 'dashboard.no_results');
      } else {
        window.trackEvent('get_help', 'view', 'dashboard.happy');
      }

      window.open(
        this.$t('dashboard.help_url'),
        'newwindow',
        'width=800,height=500'
      );
    },
    openPrimer() {
      window.open(
        this.$t('dashboard.help_url'),
        'newwindow',
        'width=800,height=500'
      );
    },
    navigateTo: function(nav) {
      window.location = nav;
    },

    selectActionPlan: function(value) {
      this.selectedActionPlan = value;
    },
    resendSurveys() {
      var pulseSurvey = this.currentActionPlan.current_pulse_survey.id;
      this.inFlight = true;
      axios
        .post(`/api/pulse-surveys/${pulseSurvey}/resend`, {
          type: 'open'
        })
        .then(({data}) => {
          Toast.create(data.message);
          this.$emit('successful');
        })
        .catch(() => {
        })
        .then(() => {
          this.inFlight = false;
        });
    },
   
  },
  computed: {
    actionPlanOptions() {
      if (!this.data.action_plans) {
        return [];
      } else {
        return this.data.action_plans.map(action_plan => {
          return {
            value: '' + action_plan.id,
            label: this.$t(action_plan.label),
          };
        });
      }
    },
    currentActionPlan() {
      if (!this.data.action_plans.length) {
        return null;
      }
      if (this.selectedActionPlan == null) {
        return this.data.action_plans.find(x => x);
      }
      return this.data.action_plans.find((action_plan) => action_plan.id == this.selectedActionPlan);
    },
    actionStepsCompleteLabel() {
      return this.$t('dashboard.card.overall.action_steps', {
        'complete': this.currentActionPlan.action_steps_complete.length,
        'total': this.currentActionPlan.action_steps.length
      });
    }
  },
  mounted() {
    console.log('Component dashboard mounted.');
    // this.renderChart();
  },
};
</script>
<style lang="scss" scoped>
  .momentum-dashboard {
    flex: 1;
  }
</style>
