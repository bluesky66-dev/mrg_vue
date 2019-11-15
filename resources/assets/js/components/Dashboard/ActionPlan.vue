<template>
  <div class="action-plan">
    <div class="row">
      <div class="col-6">
        <div class="card-center full-height">
          <ActionPlanHeader
            :title="$t('dashboard.card.overall.title')"
            @action="navigateTo('/action-plans/' + selectedActionPlan)">
            {{ $t('dashboard.link.review_action_plan') }}
          </ActionPlanHeader>
          
          <div class="card-body">
            <div class="card-inner">
              <q-knob v-model="currentActionPlan.progress_percent"
                      readonly
                      color="positive"
                      size="12rem">
                  <div class="column">
                      <div class="percent">{{ currentActionPlan.progress_percent }}%</div>
                      <div class="label">{{ $t('dashboard.card.overall.complete') }}</div>
                  </div>
              </q-knob>
              <p class="action-steps" v-if="currentActionPlan.action_steps.length > 0">{{ actionStepsCompleteLabel }}</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="card-center full-height">
          <ActionPlanHeader
            :title="$t('dashboard.card.timeline.title')"
            @action="navigateTo('/action-plans/' + selectedActionPlan + '?step=reminders')"            
          >
            {{ $t('dashboard.link.set_reminders') }}
          </ActionPlanHeader>
          <div class="card-body">
            <div class="card-inner">
              
              <div class="row center__vertically">
                  <div class="col-4 text-right">
                      <p class="caption">{{ $t('dashboard.card.timeline.start') }}</p>
                      {{ currentActionPlan.formatted_dates.starts_at.localized }}
                  </div>
                  <div class="col-4">
                    <q-knob v-model="currentActionPlan.timeline_percent"
                            readonly
                            color="positive"
                            size="12rem">
                        <div class="column">
                            <div class="percent">{{ currentActionPlan.days_remaining }}</div>
                            <div class="label">{{ $t('dashboard.card.timeline.remaining') }}</div>
                        </div>
                    </q-knob>
                  </div>
                  <div class="col-4 text-left">
                      <p class="caption">{{ $t('dashboard.card.timeline.end') }}</p>
                      {{ currentActionPlan.formatted_dates.ends_at.localized }}
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="card-center full-height">
              <template v-if="currentActionPlan.current_pulse_survey">
                <ActionPlanHeader
                  v-if="!currentActionPlan.current_pulse_survey.is_complete"
                  :title="$t('dashboard.card.pulse_survey.title')"
                  @action="resendSurveys"
                >
                  {{ $t('dashboard.link.resend_surveys') }}
                </ActionPlanHeader>
                <ActionPlanHeader
                  v-if="currentActionPlan.current_pulse_survey.is_complete"
                  :title="$t('dashboard.card.pulse_survey.title')"
                  :disable="true"
                >
                  <q-tooltip
                    anchor="bottom middle"
                    self="top middle">
                    {{ $t('pulse_survey.hover.disabled.resend_bulk') }}
                  </q-tooltip>
                  {{ $t('dashboard.link.resend_surveys') }}
                </ActionPlanHeader>
              </template>
              <ActionPlanHeader
                v-if="!currentActionPlan.current_pulse_survey && !currentActionPlan.is_complete"
                :title="$t('dashboard.card.pulse_survey.title')"
                @action="navigateTo('/pulse-surveys/create')"
              >
                {{ $t('dashboard.link.create_survey') }}
              </ActionPlanHeader>
              
              <div class="card-body">
                  <div class="card-inner">
                      <div v-if="currentActionPlan.current_pulse_survey">
                          <div class="label param">{{ $t('dashboard.card.pulse_survey.due_date') }} {{ currentActionPlan.current_pulse_survey.formatted_dates.due_at.localized }}</div>
                          <div class="row">
                              <div class="col-4">
                                  <div class="value-grey">{{ currentActionPlan.current_pulse_survey.total_surveys_sent }}</div>
                                  <p class="caption">{{ $t('dashboard.card.pulse_survey.sent') }}</p>
                              </div>
                              <div class="col-4">
                                  <div class="value-grey">{{ currentActionPlan.current_pulse_survey.total_surveys_complete }}</div>
                                  <p class="caption">{{ $t('dashboard.card.pulse_survey.complete') }}</p>
                              </div>
                              <div class="col-4">
                                  <div class="value-grey">{{ currentActionPlan.current_pulse_survey.total_surveys_open }}</div>
                                  <p class="caption">{{ $t('dashboard.card.pulse_survey.open') }}</p>
                              </div>
                          </div>
                      </div>
                      <div v-if="!currentActionPlan.current_pulse_survey">
                          <div class="label">{{ $t('dashboard.card.pulse_survey.sent') }}</div>
                          <div class="percent">0</div>
                      </div>
                  </div>
              </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card-center full-height">
              <ActionPlanHeader
                :title="$t('dashboard.card.pulse_results.title')"
                @action="navigateTo('/pulse-surveys/' + selectedActionPlan + '/results')"
              >
                {{ $t('dashboard.link.view_results') }}
              </ActionPlanHeader>
                <div class="card-body">
                    <div class="card-inner">
                        <div v-if="!currentActionPlan.current_pulse_survey">
                            <p class="no-results">{{ $t('dashboard.card.pulse_results.message.no_survey') }}</p>
                        </div>
                        <div v-if="currentActionPlan.current_pulse_survey && !currentActionPlan.complete_pulse_surveys.length">
                            <p class="no-results">{{ $t('dashboard.card.pulse_results.message.no_results') }}</p>
                        </div>
                        <div v-if="currentActionPlan.current_pulse_survey && currentActionPlan.complete_pulse_surveys.length">
                            <div class="pulse-results-chart-container">
                                <div class="y-axis-legend">
                                    <div>7</div>
                                    <div>6</div>
                                    <div>5</div>
                                    <div>4</div>
                                    <div>3</div>
                                    <div>2</div>
                                    <div>1</div>
                                </div>
                                <div class="pulse-results-chart-wrapper">
                                    <div id="pulse-results-chart"></div>
                                    <p>{{ $t('dashboard.card.pulse_results.x_axis') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <q-inner-loading :visible="inFlight">
        <q-spinner size="50px" color="primary" />
    </q-inner-loading>
  </div>
</template>
<script>
import d3Select from 'd3-selection/src/select';
import { timeParse as d3TimeParse } from 'd3-time-format';
import d3ScaleLinear from 'd3-scale/src/linear';
import {
  axisLeft as d3AxisLeft,axisBottom as d3AxisBottom
} from 'd3-axis/src/axis';
import d3Line from 'd3-shape/src/line';
import d3Extent from 'd3-array/src/extent';
import {
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
import ActionPlanHeader from './ActionPlanHeader.vue';
export default {
  components: {
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
    ActionPlanHeader
  },
  props: {
    inFlight: {
      required: true,
      type: Boolean
    },

    currentActionPlan: {
      required: true
    },

    selectedActionPlan: {
      required: true
    }
  },

  mounted() {
    this.renderChart();
  },

  updated() {
    this.renderChart();
  },
  computed: {
    actionStepsCompleteLabel() {
      return this.$t('dashboard.card.overall.action_steps', {
        complete: this.currentActionPlan.action_steps_complete.length,
        total: this.currentActionPlan.action_steps.length
      });
    }
  },

  methods: {
    navigateTo: function(nav) {
      window.location = nav;
    },

    renderChart() {
      if (
        this.currentActionPlan &&
        this.currentActionPlan.pulse_surveys.length > 0
      ) {
        if (document.getElementById('pulse-results-chart')) {
          document.getElementById('pulse-results-chart').innerHTML = '';
        }
        const chart = d3Select('#pulse-results-chart');
        if (!chart.node()) {
          return;
        }

        if (!chart.node()) {
          return;
        }

        const dims = chart.node().getBoundingClientRect();

        const margin = { top: 0, right: 1, bottom: 30, left: 0 },
          width = dims.width - margin.left - margin.right,
          height = Math.max(170, dims.height) - margin.top - margin.bottom;

        // Parse the date / time
        const parseDate = d3TimeParse('%Y-%m-%d %H:%M:%S');

        // Set the ranges
        const fullX = d3ScaleLinear().range([0, width]);

        const x = d3ScaleLinear().range([20, width - 20]);
        const y = d3ScaleLinear().range([height, 1]);

        let res = [];
        this.currentActionPlan.pulse_surveys.forEach(result => {
          if (result.is_complete && result.statistics.mean > 0) {
            res.push({
              completed_at: parseDate(result.completed_at),
              completed_at_formatted:
                result.formatted_dates.completed_at.localized,
              mean: result.statistics.mean,
              cycle: result.cycle
            });
          }
        });

        res.sort((a, b) => {
          const aCycle = new Date(a.cycle);
          const bCycle = new Date(b.cycle);
          if (aCycle < bCycle) {
            return -1;
          }
          if (aCycle > bCycle) {
            return 1;
          }
          return 0;
        });

        // Define the axes
        const xAxisLine = d3AxisBottom()
          .scale(fullX)
          .ticks(0)
          .tickFormat(() => '');
        const xAxis = d3AxisBottom()
          .scale(x)
          .ticks(res.length)
          .tickFormat(value => {
            let label = null;
            if (Math.round(value) == value) {
              label = value;
            }
            return label;
          });
        const yAxis = d3AxisLeft()
          .scale(y)
          .ticks(6)
          .tickFormat(() => '');

        // Define the line
        const valueline = d3Line()
          .x(function(d) {
            return x(d.cycle);
          })
          .y(function(d) {
            return y(d.mean);
          });

        const svg = chart
          .append('svg')
          .attr('width', width + margin.left + margin.right)
          .attr('height', height + margin.top + margin.bottom)
          .append('g')
          .attr(
            'transform',
            'translate(' + margin.left + ',' + margin.top + ')'
          );

        // Scale the range of the data
        x.domain(d3Extent(res, res => res.cycle));
        y.domain([1, 7]);

        if (res.length === 1) {
          const xPos = width / 2;
          // draw a dot
          svg
            .append('circle')
            .attr('class', 'dot')
            .attr('r', 3.5)
            .attr('cx', xPos) //x(res[0].cycle))
            .attr('cy', y(res[0].mean))
            .style('fill', '#46B488');

          svg
            .append('g')
            .attr('class', 'x axis cycles')
            .attr('transform', 'translate(0,' + (height + 18) + ')')
            .append('text')
            .attr('text-anchor', 'middle')
            .attr('x', xPos) //x(res[0].cycle))
            .text(res[0].cycle);
        } else {
          // Add the valueline path.
          svg
            .append('path')
            .attr('class', 'line')
            .attr('d', valueline(res));
        }

        // Add the X Axes
        svg
          .append('g')
          .attr('class', 'x axis cycles')
          .attr('transform', 'translate(0, ' + height + ')')
          .call(xAxis);

        svg
          .append('g')
          .attr('class', 'x axis')
          .attr('transform', 'translate(0, ' + height + ')')
          .call(xAxisLine);

        // Add the Y Axis
        svg
          .append('g')
          .attr('class', 'y axis')
          .call(yAxis);
      }
    }
  }
};
</script>

<style lang="scss" scoped>
@import "~@/_variables.scss";
.no-results {
  font-size: 16px;
  padding: 20px;
}
.pulse-results-chart-container {
  text-align: left;
  display: flex;
  flex-direction: row;
  padding: 0 30px;
  .y-axis-legend {
    display: flex;
    flex-direction: column;
    div {
      height: 100%;
      display: flex;
      flex-direction: column;
      padding-right: 8px;
      font-size: 0.9rem;
      &:last-child {
        justify-content: flex-end;
        padding-bottom: 45px;
      }
    }
  }
  .pulse-results-chart-wrapper {
    width: 100%;
    p {
      font-size: 1.3rem;
      margin: -10px 0 0;
      text-align: center;
    }
  }
}

.caption {
  font-size: 1.3rem;
  margin-top: 0 !important;
  font-weight: 500;
  letter-spacing: 0.5px;
  color: #222;
}

.percent {
  font-size: 3rem;
  color: #000;
}

.value-grey {
  font-size: 3rem;
  color: #333;  
  font-weight: 500;
}

.label {
  color: #000;
  font-size: 1.2rem;
  font-weight: 500;
  letter-spacing: .6px;
  padding: 0px 15px;
  margin-bottom: 13px;
}
.card-center {
  text-align: center;
  margin: 8px;
  vertical-align: top;
  display: flex;
  flex-direction: column;
}

.card-body {
  flex-grow: 1;
  display: flex;
  align-items: center;
  justify-content: center;
}

.card-inner {
  flex: 1 1 auto;
}

.action-steps {
  font-size: 1.3rem;
  line-height: 4.5rem;
  font-weight: 500;
  letter-spacing: 1px;
}
.action-plan {
  height: 100%;
  display: flex;
  flex-direction: column;
  > .row {
    flex: 1;

    &:first-of-type {
      border-bottom: 1px solid $color-gray;
    }

    > div:first-of-type {
      border-right: 1px solid $color-gray;
    }
  }
}
.center__vertically {
  align-content: center;
  align-items: center;
  margin-bottom: 55px;
}
.text-right {
  text-align: right;
}
.text-left {
  text-align: left;
}
.param {
  font-size: 1.3rem;
}
.text-positive {
  color: #13b487 !important ;
}
</style>
