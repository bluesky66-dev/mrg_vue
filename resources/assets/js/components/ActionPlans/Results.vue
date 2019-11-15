<template>
  <div class="action-plans-results">
    <div class="results-heading">
      <h3>Pulse Surveys</h3>
      <q-card class="with-hover">
        <div class="hover-over-card-top-right">
          <q-btn icon="file_download"
              color="primary"
              @click="download"
              flat>
            {{ $t('global.link.download') }}
          </q-btn>
          <q-btn icon="email"
              color="primary"
              @click="startSharing"
              flat>
            {{ $t('global.link.share') }}
          </q-btn>
        </div>
      </q-card>
      <q-card class="relative-position"
          v-if="isSharing">
        <q-card-main>
          <div class="row">
            <q-field class="col-7"
                :error="$v.observers_selected.$error"
                :error-label="observers_selectedErrorMsg">
              <q-select multiple
                  chips
                  color="primary"
                  :float-label="$t('pulse_survey.send.picker.label')"
                  v-model="observers_selected"
                  :options="observerOptions"></q-select>
            </q-field>
            <div class="col"></div>
            <div class="col-4 column justify-center">
              <q-btn color="primary"
                  icon="person_add"
                  @click="addObserver">
                {{ $t('global.cta.add_recipient') }}
              </q-btn>
            </div>
          </div>
          <q-btn @click="stopSharing"
              flat
              class="pull-right"
              big
              color="primary">
            {{ $t('global.cta.back') }}
          </q-btn>
          <q-btn @click="sendResults"
              big
              class="pull-right"
              flat
              color="primary">
            {{ $t('pulse_survey.cta.send_results') }}
          </q-btn>
          <div style="clear: both"></div>
        </q-card-main>
        <q-inner-loading :visible="inFlight">
          <q-spinner size="50px"
              color="primary" />
        </q-inner-loading>
      </q-card>
    </div>
    <q-card>
      <q-card-main>
        <h6>{{ $t('pulse_survey.card.action_plan.label') }}</h6>
        <h5>{{ action_plan.behavior.name_key_translated }}</h5>
        <q-card>
          <q-card-main>
            <h5> {{ action_plan.rating_feedback_question_key_translated }}</h5>
          </q-card-main>
        </q-card>
        <div class="bubble-chart-container">
          <div class="header">
            <div class="y-axis-label">{{ $t('pulse_survey.results_table.cycle.label') }}</div>
            <div class="x-axis-labels">
              <div class="numbers">
                <div>1</div>
                <div>2</div>
                <div>3</div>
                <div>4</div>
                <div>5</div>
                <div>6</div>
                <div>7</div>
              </div>
              <div class="labels">
                <div>{{ $t('pulse_survey.preview.recent.rarely') }}</div>
                <div>{{ $t('pulse_survey.preview.recent.sometimes') }}</div>
                <div>{{ $t('pulse_survey.preview.recent.regularly') }}</div>
                <div>{{ $t('pulse_survey.preview.recent.often') }}</div>
                <div>{{ $t('pulse_survey.preview.recent.very_often') }}</div>
              </div>
            </div>
            <div class="value-heading">
              {{ $t('pulse_survey.results_table.mean.label') }}
            </div>
            <div class="value-heading">
              {{ $t('pulse_survey.results_table.sd.label') }}
            </div>
          </div>
          <div class="data">
            <div class="data-row"
                v-for="(r, index) in results"
                :key="index">
              <div class="cycle-num">{{ r.cycle }}</div>
              <div class="cycle-data">
                <div class="cycle-data-min-max"
                    :style="{ left: ((r.statistics.min / 7.25) * 100) + '%', width: (((r.statistics.max - r.statistics.min) / 7.25) * 100) + '%' }"></div>
                <div class="cycle-data-mean"
                    :style="{ left: ((r.statistics.mean / 7.25) * 100) + '%' }">{{ r.statistics.mean }}</div>
              </div>
              <div class="cycle-mean">
                {{ r.statistics.mean }}
              </div>
              <div class="cycle-standard-deviation">
                {{ r.statistics.standard_deviation }}
              </div>
            </div>
          </div>
        </div>
        <div class="line-chart-container">
          <div class="line-chart-y-legend">
            <div>{{ $t('pulse_survey.preview.recent.very_often') }}</div>
            <div>{{ $t('pulse_survey.preview.recent.often') }}</div>
            <div>{{ $t('pulse_survey.preview.recent.regularly') }}</div>
            <div>{{ $t('pulse_survey.preview.recent.sometimes') }}</div>
            <div>{{ $t('pulse_survey.preview.recent.rarely') }}</div>
          </div>
          <div class="line-chart-wrapper">
            <div id="line-chart"></div>
            <h5>{{ $t('pulse_survey.results_table.cycle.label') }}</h5>
          </div>
        </div>
      </q-card-main>
    </q-card>
    <div class="open-answers">
      <h4>{{ $t('global.validation.attributes.additional_comments') }}</h4>
      <div class="cycle"
          v-for="(r, index) in results"
          :key="index">
        <h5 class="cycle-header">{{ $t('pulse_survey.results_table.cycle.label') }} {{ r.cycle }}</h5>
        <p v-if="!r.has_additional_comments"> {{ $t('pulse_survey.no_additional_comments') }} </p>
        <div v-for="(answers, index) in r.pulse_survey_results"
            :key="index">
          <q-card class="answer"
              v-if="answers.additional_comments">
            <q-card-main>
              <p>{{ answers.additional_comments }}</p>
            </q-card-main>
          </q-card>
        </div>
      </div>
    </div>
    <q-modal ref="editObserverModal"
        position="top"
        noBackdropDismiss
        noEscDismiss>
      <edit-observer ref="editObserver"
          :observerTypes="observer_types"
          :cultures="cultures"
          @successful="editedObserver"
          @cancel="$refs.editObserverModal.close()" />
    </q-modal>
  </div>
</template>
<style lang="scss"
    scoped>
.action-plans-results {
  max-width: 800px;
  .send-controls {
    display: flex;
    white-space: nowrap;
    align-items: center;
    justify-content: center;
    margin: 0 8px;
    .select-container {
      width: 100%;
      &+div {
        padding-left: 15px;
      }
    }
  }
}

.bubble-chart-container {
  padding-top: 20px;
  .header {
    display: flex;
    flex-direction: row;
    align-items: center;
    .y-axis-label,
    .value-heading {
      width: 80px;
      font-weight: bold;
      text-align: center;
    }
    .x-axis-labels {
      width: 100%;
      display: flex;
      flex-direction: column;
      background: #E8F0F3;
      padding: 2px;
      margin: 0 5px;
      .numbers,
      .labels {
        display: flex;
        flex-direction: row;
        div {
          flex: 1;
        }
      }
      .numbers {
        padding-bottom: 1px;
        div {
          text-align: center;
        }
      }
      .labels {
        border-top: 2px solid #46B488;
        padding-top: 3px;
        div {
          text-align: center;
          &:first-child {
            text-align: left;
            text-indent: 21px;
          }
          &:last-child {
            text-align: right;
            position: relative;
            left: -9px;
          }
        }
      }
    }
  }
  .data-row {
    display: flex;
    flex-direction: row;
    padding: 10px 0;
    align-items: center;
    justify-content: center;
    .cycle-num {
      font-weight: bold;
      font-size: 1.5rem;
      width: 58px;
      text-indent: 25px;
    }
    .cycle-mean {
      width: 167px;
      text-indent: 60px;
    }
    .cycle-standard-deviation {
      width: 90px;
    }
    .cycle-mean,
    .cycle-standard-deviation {
      text-align: center;
    }
    .cycle-data {
      width: 100%;
      position: relative;
      height: 2px;
      .cycle-data-min-max,
      .cycle-data-mean {
        position: absolute;
      }
      .cycle-data-min-max {
        border: 1px solid #46B488;
        transform: translateX(15px);
      }
      .cycle-data-mean {
        color: #FFF;
        background: #46B488;
        width: 30px;
        height: 30px;
        border-radius: 15px;
        text-align: center;
        line-height: 30px;
        vertical-align: middle;
        transform: translate(0, -50%);
        margin-top: 1px;
      }
    }
    &+.data-row {
      border-top: 1px solid #D3D2D3;
    }
  }
}

.line-chart-container {
  padding: 15px 0;
  display: flex;
  flex-direction: row;
  .line-chart-y-legend {
    width: 100px;
    padding: 22px 0 47px;
    flex-direction: column;
    div {
      text-align: right;
      height: 20%;
      display: flex;
      justify-content: flex-end;
      &:nth-child(2) {
        padding-bottom: 33%;
      }
      &:nth-child(2),
      &:nth-child(3),
      &:nth-child(4) {
        flex-direction: row;
        justify-content: flex-end;
        align-items: center;
      }
      &:nth-child(4) {
        padding-top: 33%;
      }
      &:last-child {
        flex-direction: row;
        align-items: flex-end;
      }
    }
  }
  .line-chart-wrapper {
    width: 100%;
    h5 {
      text-align: center;
      margin: 0;
      padding-left: 30px;
    }
  }
}

.open-answers {
  .cycle-header {
    font-size: 16px;
    font-weight: bold;
    margin: 8px;
  }
  .cycle p {
    word-break: break-all;
  }
}

</style>
<style lang="scss">
#line-chart {
  path {
    stroke: #46B488;
    stroke-width: 2;
    fill: none;
  }
  .dot {
    stroke: #46B488;
  }
  .axis path,
  .axis line {
    fill: none;
    stroke: grey;
    stroke-width: 1;
    shape-rendering: crispEdges;
  }
  .axis.cycles line,
  .axis.cycles path,
  .axis.dates line,
  .axis.dates path {
    display: none;
  }
  .axis.dates {
    font-size: 1.1rem;
  }
  .axis.cycles {
    font-size: 1.5rem;
  }
  .axis.line line {
    display: none;
  }
}

</style>
<script>
import {
  QCard,
  QCardMain,
  QCardTitle,
  QCardSeparator,
  QBtn,
  QCardActions,
  QModal,
  QInnerLoading,
  QSpinner,
  QSelect,
  QField,
  Toast,
} from 'quasar-framework';

import EditObserver from '../ObserverCrud/EditObserver.vue';


import get from 'lodash/get';

import axios from 'axios';


// D3!!
import d3Select from 'd3-selection/src/select';
import { timeParse as d3TimeParse } from 'd3-time-format';
import d3ScaleLinear from 'd3-scale/src/linear';
import {axisLeft as d3AxisLeft,axisBottom as d3AxisBottom} from 'd3-axis/src/axis';
import d3Line from 'd3-shape/src/line';
import d3Extent from 'd3-array/src/extent';

import persistentApplicationStateMixin from '../../utils/persistentApplicationStateMixin';

import {required as requiredValidator} from 'vuelidate/lib/validators';
import {createServerValidation} from '../../utils/serverValidationMixin';

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
  name: 'momentum-action-plans-results',
  mixins: [
    serverValidation,
    persistentApplicationStateMixin()
  ],
  data() {
    const surveys = JSON.parse(JSON.stringify(window.data.pulse_surveys)).sort((a, b) => {
      const aCycle = new Date(a.cycle);
      const bCycle = new Date(b.cycle);
      if (aCycle < bCycle) {
        return -1;
      }
      if (aCycle > bCycle) {
        return 1;
      }
      return 0;
    })
      .filter((s) => (s.is_complete && (s.statistics.mean > 0)));

    return {
      results: surveys,
      observers: window.data.observers,
      observers_selected: [],
      inFlight: false,
      action_plan: window.data.action_plan,
      isSharing: false,
      observer_types: window.data.observer_types,
      cultures: window.cultures,

    };
  },
  components: {
    QCard,
    QCardMain,
    QCardTitle,
    QCardSeparator,
    QBtn,
    QCardActions,
    QModal,
    QInnerLoading,
    QSpinner,
    QSelect,
    QField,
    Toast,
    EditObserver
  },
  methods: {
    sendResults() {
      if (this.$v.$invalid) {
        this.$v.$touch();
        return;
      }
      this.$v.$reset();
      this.inFlight = true;
      axios
        .post(`/api/action-plans/${this.action_plan.id}/results/share`, {
          observers: this.observers_selected,
        })
        .then(({data}) => {
          if (data.message) {
            window.sessionStorage.setItem('toast-message', data.message);
          }
          window.location = data.redirect;
          this.$emit('successful');
        })
        .catch(({ response }) => {
          if (response.status === 422) {
            const errors = get(response, 'data.errors');

            this.errors = {
              ...this.errors,
              ...errors
            };

            this.$v.$touch();
          }
          //console.dir(response);
        })
        .then(() => {
          this.inFlight = false;
        });
    },
    startSharing() {
      this.isSharing = true;
    },
    stopSharing() {
      this.isSharing = false;
    },
    download() {
      window.location = `/action-plans/${this.action_plan.id}/results/download`;
    },
    addObserver() {
      this.$refs.editObserver.setDefaultValues({
        id: null,
        mode: 'create',
        first_name: null,
        last_name: null,
        email: null,
        observer_type: Object.keys(this.observer_types)[0],
        culture_id: get(window, 'profile.culture.id', 1)
      });
      this.$refs.editObserverModal.open();
    },
    editedObserver({ action, payload }) {
      //console.log('editedObserver',{action,payload});
      this.$refs.editObserverModal.close();
      if (action === 'create') {
        this.$set(this.observers, this.observers.length, payload);
        this.$set(this.observers_selected, this.observers_selected.length, payload.id);
      }
      if (action === 'edit') {
        this.observers.forEach((observer, idx) => {
          if (observer.id === payload.id) {
            this.$set(this.observers, idx, payload);
          }
        });
      }
      if (action === 'delete') {
        this.observers.forEach((observer, idx) => {
          if (observer.id === payload.id) {
            this.$delete(this.observers, idx);
          }
        });
      }
    },
  },
  computed: {
    observerOptions() {
      if (!this.observers) {
        return [];
      } else {
        return this.observers.map(observer => ({
          value: observer.id,
          label: observer.full_name + ' (' + observer.email + ')'
        }));
      }
    }
  },
  mounted() {
    console.log('Component action-plan-results mounted.');

    if (this.results.length > 0) {
      const chart = d3Select('#line-chart'),
        dims = chart.node().getBoundingClientRect(),
        margin = { top: 30, right: 20, bottom: 30, left: 50 },
        width = dims.width - margin.left - margin.right,
        height = Math.max(400, dims.height) - margin.top - margin.bottom;

      // Parse the date / time
      const parseDate = d3TimeParse('%Y-%m-%d %H:%M:%S');
      //const parseDate = d3TimeParse('%B %d, %Y');

      // Set the ranges
      const fullX = d3ScaleLinear().range([0, width]);
      const x = d3ScaleLinear().range([50, (width - 50)]);
      const y = d3ScaleLinear().range([height, 1]);
      // Define the axes
      const cyclesXAxis = d3AxisBottom().scale(x).ticks(this.results.length)
        .tickFormat((value) => {
          let label = null;
          if (Math.round(value) == value) {
            label = value;
          }
          return label;
        });

      const lineXAxis = d3AxisBottom().scale(fullX).tickFormat(() => '');
      const dateXAxis = d3AxisBottom().scale(x).ticks(this.results.length)
        .tickFormat((value) => {
          let label = null;
          this.results.forEach((r) => {
            if (r.cycle === value) {
              label = r.formatted_dates.completed_at.localized;
            }
          });
          return label;
        });

      const yAxis = d3AxisLeft().scale(y).ticks(6);

      // Define the line
      const valueline = d3Line()
        .x(function(d) { return x(d.cycle); })
        .y(function(d) { return y(d.mean); });

      const svg = chart.append('svg')
        .attr('width', width + margin.left + margin.right)
        .attr('height', height + margin.top + margin.bottom)
        .append('g')
        .attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

      let res = [];
      this.results.forEach((result) => {
        res.push({
          completed_at: parseDate(result.completed_at),
          completed_at_formatted: result.formatted_dates.completed_at.localized,
          mean: result.statistics.mean,
          cycle: result.cycle
        });
      });

      // Scale the range of the data
      y.domain([1, 7]);
      x.domain(d3Extent(res, (res) => res.cycle));

      if (res.length === 1) {
        // draw a dot
        const xPos = (width / 2);
        svg.append('circle')
          .attr('class', 'dot')
          .attr('r', 3.5)
          .attr('cx', xPos) //x(res[0].cycle))
          .attr('cy', y(res[0].mean))
          .style('fill', '#46B488');

        svg.append('g')
          .attr('class', 'x axis cycles single-result')
          .attr('transform', 'translate(0,' + (height + 25) + ')')
          .append('text')
          .attr('text-anchor', 'middle')
          .attr('x', xPos) //x(res[0].cycle))
          .text(res[0].cycle);

        svg.append('g')
          .attr('class', 'x axis dates single-result')
          .attr('transform', 'translate(0,' + (height - 5) + ')')
          .append('text')
          .attr('x', xPos) //x(res[0].cycle))
          .attr('text-anchor', 'middle')
          .text(res[0].completed_at_formatted);
      } else {
        // Add the valueline path.
        svg.append('path')
          .attr('class', 'line')
          .attr('d', valueline(res));
      }

      // Add the X Axes
      svg.append('g')
        .attr('class', 'x axis dates')
        .attr('transform', 'translate(0, ' + (height - 25) + ')')
        .call(dateXAxis);

      svg.append('g')
        .attr('class', 'x axis cycles')
        .attr('transform', 'translate(0,' + height + ')')
        .call(cyclesXAxis);

      svg.append('g')
        .attr('class', 'x axis line')
        .attr('transform', 'translate(0,' + height + ')')
        .call(lineXAxis);

      // Add the Y Axis
      svg.append('g')
        .attr('class', 'y axis')
        .call(yAxis);
    }
  }
};

</script>
