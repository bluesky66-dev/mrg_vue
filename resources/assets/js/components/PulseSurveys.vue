<template>
  <div id="momentum-pulse-surveys" class="momentum-pulse-surveys">
    <!-- <h3>{{ $t('pulse_survey.title') }}</h3>
    <q-card class="with-hover">
      <div class="hover-over-card-top-right">
        <q-btn 
          v-on:click.prevent="openHelp"
          icon="help"
          class="" 
          color="positive"
          flat>
          {{ $t('global.nav.get_help') }}
        </q-btn>
      </div>
      <q-card-main class="layout-padding">
          <span v-if="plans.length > 0">{{ $t('pulse_survey.get_started.message') }}</span>
          <span v-if="plans.length === 0">{{ $t('pulse_survey.get_started.empty_message') }}</span>
      </q-card-main>
      <q-card-actions
              :style="{paddingTop:0}"
              class="layout-padding">
        <q-btn
                big
                v-if="plans.length > 0 && user.can_create_pulse_surveys"
                v-on:click.prevent="createSurvey"
                color="primary">
          {{ $t('pulse_survey.cta.create_pulse_survey') }}
        </q-btn>
        <q-btn
                big
                key="disabled-create-btn"
                class="disabled"
                v-if="plans.length > 0 && !user.can_create_pulse_surveys"
                color="primary">
          {{ $t('pulse_survey.cta.create_pulse_survey') }}
          <q-tooltip anchor="bottom middle" self="top middle">
            {{ $t('pulse_survey.cannot_create_pulse_surveys') }}
          </q-tooltip>
        </q-btn>
        <q-btn
                big
                v-if="plans.length === 0"
                color="primary"
                @click="createActionPlan">
          {{ $t('action_plan.create.page_title') }}
        </q-btn>
      </q-card-actions>
    </q-card>
    <div class="current-pulse-surveys" v-if="Object.keys(actionPlans).length">
      <h3>{{ $t('pulse_survey.section.current') }}</h3>
      <q-card v-for="(actionPlan, index) in actionPlans" :key="actionPlan.id" class="action-plan-card">
        <q-card-main>
          <div class="survey-title">
            <div>
              <h6>{{ $t('pulse_survey.card.action_plan.label') }}</h6>
              <h5>{{ actionPlan.label }}</h5>
            </div>
            <div>
              <q-btn
                v-if="!actionPlan.can_create_pulse_survey && !actionPlan.is_complete"
                class="disabled"
                big
                flat
                color="primary"
                icon="add">
                {{ $t('pulse_survey.cta.new_cycle') }}
                <q-tooltip anchor="bottom middle" self="top middle">
                  {{ $t('pulse_survey.validation.open_pulse_survey') }}
                </q-tooltip>
              </q-btn>
              <q-btn
                      v-if="!actionPlan.can_create_pulse_survey && actionPlan.is_complete"
                      class="disabled"
                      big
                      flat
                      color="primary"
                      icon="add">
                {{ $t('pulse_survey.cta.new_cycle') }}
                <q-tooltip anchor="bottom middle" self="top middle">
                  {{ $t('pulse_survey.validation.completed_action_plan') }}
                </q-tooltip>
              </q-btn>
              <q-btn
                      v-if="actionPlan.can_create_pulse_survey"
                      big
                      flat
                      v-on:click.prevent="newSurveyCycle(actionPlan)"
                      color="primary"
                      icon="add">
                {{ $t('pulse_survey.cta.new_cycle') }}
              </q-btn>
            </div>
          </div>
          <div class="survey-detail">
            <q-field>
              <q-select
                v-model="actionPlan.cycle"
                :float-label="$t('pulse_survey.card.cycle.label')"
                :options="getCycles(actionPlan.surveys)"
                @change="(value) => { setCurrentSurvey(index, value); }"
                ></q-select>
            </q-field>
            <q-card class="survey-detail-card">
              <q-card-main>
                <div class="selected-survey-details">
                  <div class="heading">
                    <div class="detail">
                      <h6>{{ $t('pulse_survey.card.sent.label') }}</h6>
                      <h5>{{ actionPlan.currentSurvey.total_surveys_sent }}</h5>
                    </div>
                    <div class="detail">
                      <h6>{{ $t('pulse_survey.card.complete.label') }}</h6>
                      <h5>{{ actionPlan.currentSurvey.total_surveys_complete }}</h5>
                    </div>
                    <div class="detail">
                      <h6>{{ $t('pulse_survey.card.open.label') }}</h6>
                      <h5>{{ actionPlan.currentSurvey.total_surveys_open }}</h5>
                    </div>
                    <div class="detail">
                      <h6>{{ $t('pulse_survey.card.due_date.label') }}</h6>
                      <h5>{{ actionPlan.currentSurvey.formatted_dates.due_at.localized }}</h5>
                    </div>
                    <div class="toggle">
                      <q-btn
                        color="primary"
                        @click="toggleSurveyDeatils(index)"
                        flat
                        round
                        small
                        :icon="actionPlan.currentSurvey.expanded ? 'keyboard_arrow_up' : 'keyboard_arrow_down'"
                      >
                      </q-btn>
                    </div>
                  </div>
                  <div class="details" v-if="actionPlan.currentSurvey.expanded">
                    <div class="head">
                      <div>{{ $t('pulse_survey.card.sent_to.label') }}</div>
                      <div>{{ $t('pulse_survey.card.reminders.label') }}</div>
                      <div>{{ $t('pulse_survey.card.status.label') }}</div>
                      <div>&nbsp;</div>
                    </div>
                    <div class="data">
                      <div class="survey-recipient-row" v-for="(result, rIndex) in actionPlan.currentSurvey.pulse_survey_results" :key="rIndex">
                        <div>{{ result.observer.full_name }}</div>
                        <div>{{ result.reminders_sent }}</div>
                        <div>
                          <div>
                            {{ result.status }}
                          </div>
                          <q-btn
                            small
                            flat
                            loader
                            color="primary"
                            icon="mail"
                            v-if="!(actionPlan.currentSurvey.is_complete || result.is_complete)"
                            @click="(e, done) => { resendSurvey(actionPlan.currentSurvey, result, done) }">
                            {{ $t('pulse_survey.cta.resend') }}
                            <q-spinner slot="loading" class="on-left"></q-spinner>
                            <span slot="loading">{{ $t('pulse_survey.cta.resend') }}</span>
                          </q-btn>
                        </div>
                        <div>
                          <q-btn
                            flat
                            round
                            loader
                            color="faded"
                            icon="delete"
                            v-if="result.is_complete == false && actionPlan.currentSurvey.can_delete_results"
                            @click="(e, done) => { deleteSurvey(result, index, rIndex, done) }"
                          ></q-btn>
                        </div>
                      </div>
                    </div>
                    <div class="foot">
                      <q-btn
                        flat
                        v-if="!actionPlan.currentSurvey.is_complete"
                        icon="add"
                        color="primary"
                        @click="(e) => { showAddContactModal(actionPlan.currentSurvey) }">
                        {{ $t('global.cta.add_recipients') }}
                      </q-btn>
                    </div>
                  </div>
                </div>
              </q-card-main>
            </q-card>
          </div>
        </q-card-main>
        <q-card-separator />
        <div class="action-plan-actions">
          <q-btn
            flat
            v-if="!actionPlan.currentSurvey.is_complete"
            loader
            icon="mail"
            color="primary"
            @click="(e, done) => { resendOpenSurveys(actionPlan.currentSurvey, done) }">
            {{ $t('pulse_survey.cta.resend_bulk') }}
            <q-spinner slot="loading" class="on-left"></q-spinner>
            <span slot="loading">{{ $t('pulse_survey.cta.resend_bulk') }}</span>
          </q-btn>
          <q-btn
                  flat
                  v-if="actionPlan.currentSurvey.is_complete"
                  icon="mail"
                  key="disabled-resend-btn"
                  color="primary"
                  class="disabled">
            {{ $t('pulse_survey.cta.resend_bulk') }}
            <q-tooltip anchor="bottom middle" self="top middle">
              {{ $t('pulse_survey.hover.disabled.resend_bulk') }}
            </q-tooltip>
          </q-btn>
          <q-btn
                  v-if="!actionPlan.currentSurvey.can_view_results"
                  flat
                  icon="insert_chart"
                  class="disabled"
                  key="disabled-results-btn"
                  color="primary">
            {{ $t('pulse_survey.link.view_results') }}
            <q-tooltip anchor="bottom middle" self="top middle">
              {{ $t('pulse_survey.cannot_complete') }}
            </q-tooltip>
          </q-btn>
          <q-btn
            v-if="actionPlan.currentSurvey.can_view_results"
            flat
            icon="insert_chart"
            color="primary"
            @click="viewResults(actionPlan)">
            {{ $t('pulse_survey.link.view_results') }}
          </q-btn>
        </div>
      </q-card>
    </div>
    <q-modal ref="addContactModal">
      <div class="add-contact-modal">
        <h5>{{ $t('global.cta.add_recipients') }}</h5>
        <q-card class="add-contact-content">
          <q-card-main>
            <div class="row">
              <q-field class="col-7">
                <span v-if="!observerOptions.length">{{ $t('pulse_survey.select_recipients.no_recipients') }}</span>
               <q-select multiple
                  chips
                  v-if="observerOptions.length"
                  color="primary"
                  :float-label="$t('pulse_survey.select_recipients.label')"
                  v-model="observers_selected"
                  :options="observerOptions"></q-select>
              </q-field>
              <div class="col"></div>
              <div class="col-4 column justify-center">
                  <q-btn color="primary"
                      icon="  "
                      @click="addObserver">
                      {{ $t('global.cta.add_recipient') }}
                  </q-btn>
              </div>
            </div>
          </q-card-main>
        </q-card>
        <div class="buttons">
          <q-btn @click="hideAddContactModal"
                 flat
                 color="primary">
            {{ $t('global.cta.cancel') }}
          </q-btn>
          <q-btn loader
                 @click="(e, done) => { addContacts(done) }"
                 class="pull-right"
                 color="primary">
            <q-spinner slot="loading" class="on-left"></q-spinner>
            <span slot="loading">{{ $t('global.cta.add_recipients') }}</span>
            {{ $t('global.cta.add_recipients') }}
          </q-btn>
        </div>
      </div>
    </q-modal>
    <q-modal ref="editObserverModal"
        position="top"
        noBackdropDismiss
        noEscDismiss>
        <edit-observer ref="editObserver"
            :observerTypes="observer_types"
            :cultures="cultures"
            @successful="editedObserver"
            @cancel="$refs.editObserverModal.close()" />
    </q-modal> -->
    <div class="top-div">
      <div class="top-div-left">
        <p>SORT BY:</p>
        <q-select
          v-model="selectSort"
         :options="selectSortOptions"
        />
      </div>
      <div class="top-div-right">
        <q-btn 
          v-on:click.prevent=""
          icon="add"
          class="hover-over-card-top-right" 
          color=""
          flat>
          Create New Survey
        </q-btn>
      </div>
    </div>
    
    <div class="momentum-pulse-surveys-tbl">
      <v-client-table :columns="columns" :data="tableData" :options="options">
        <template slot="cycle" scope="props">
          <q-select
            v-model="select"
           :options="selectOptions"
          />
        </template>
        <template slot="results" scope="props">
          <q-btn
            flat
            icon=""
            color="primary"
            @click=""
          >
          VIEW RESULTS
          </q-btn>
        </template>
        <template slot="actions" scope="props">            
          <q-btn
            flat
            round
            icon="more_horiz"
            color="primary"
            @click=""
            small
          >
          </q-btn>               
        </template>
      </v-client-table>
    </div>
  </div>
</template>
<style lang="scss" scoped>
  .momentum-pulse-surveys {
    width: 100%;
    padding: 12px;

    .top-div {
      display: flex;
      justify-content: space-between;
      .top-div-left {
        display: flex;
        align-items: baseline;

        p {
          font-size: 12px;
          font-weight: bold;
          margin: 0 10px 0 20px;
        }

        .q-if {
          margin: 0;
          padding: 0;
        }

        .q-if::before, .q-if::after {
          background: none;
        }
      }
      .top-div-right {
        align-items: center;
        background-color: #f9e041;
        border-radius: 5px 5px 0 0;
        box-shadow: 0 0px 1px 0 rgba(0, 0, 0, 0.2), 0 0 6px 2px rgba(0, 0, 0, 0.19);
        width: 220px;
        text-align: center;

        .q-btn {
          text-transform: none !important;
        }
      }
    }

    .momentum-pulse-surveys-tbl { 
      padding: 12px;
      border: 1px solid rgba(0,0,0,0.1);
      background-color: #e4f4fb;
      box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);

      .q-if {
        margin: 0;
        padding: 0;
        font-size: 1.2rem;
      }

      .q-if::before, .q-if::after {
        background-color: #f9e041;
      }
    }
  }
  
  .lead{
    font-size: 1.5rem;
    line-height: 2.5rem;
  }
  .current-pulse-surveys{
    margin-top: 50px;
  }
  .action-plan-card + .action-plan-card{
    margin-top: 50px;
  }
  .add-contact-modal .buttons{
    padding-top: 15px;
  }
  .survey-title{
    display: flex;
    flex-direction: row;
    align-items: center;
    h5{
      margin-top: 0;
    }
    div{
      width: 50%;
      &:last-child{
        white-space: nowrap;
        text-align: right;
      }
    }
  }
  .survey-detail-card{
    margin-top: 20px;
  }
  .selected-survey-details{
    .heading{
      display: flex;
      flex-direction: row;
      text-align: center;
      div:not(:last-child){
        width: 25%;
      }
      h5, h6{
        margin-top: 0;
      }
      h5{
        margin-bottom: 0;
      }
    }
    .details{
      padding-top: 20px;
      margin: 0 -16px -16px;
      .head,
      .survey-recipient-row{
        display: flex;
        flex-direction: row;
        & > div{
          width: 10%;
          &:not(:last-child){
            width: 30%;
          }
          &:first-child{
            padding-left: 26px;
          }
          &:nth-child(2),
          &:nth-child(3){
            text-align: center;
          }
        }
      }
      .head{
        font-weight: bold;
        padding: 8px 10px;
      }
      .survey-recipient-row{
        border-top: 1px solid #D6D8D7;
        font-size: 1.5rem;
        padding: 12px 10px;
        align-items: center;
        &:nth-child(even){
          background-color: #E7F0F3;
        }
        & > div:last-child > button{
          margin-top: -12px;
          margin-bottom: -12px;
        }
      }
      .foot{
        border-top: 1px solid #D6D8D7;
      }
    }
  }
  .action-plan-actions{
    text-align: right;
  }
  .add-contact-modal{
    padding: 15px;
  }
  .add-contact-modal h5{
    margin-top: 0;
    margin-bottom: 15px;
  }
  .add-contact-content{
    width: 600px;
  }  
</style>
<script>
import {
  QCard,
  QCardMain,
  QCardTitle,
  QCardSeparator,
  QBtn,
  QSelect,
  QField,
  QIcon,
  QSpinner,
  QTooltip,
  QCardActions,
  Dialog as QDialog,
  QModal
} from 'quasar-framework';
import { Toast } from 'quasar-framework';
import axios from 'axios';
import get from 'lodash/get';
import has from 'lodash/has';
import EditObserver from './ObserverCrud/EditObserver.vue';

export default {
  name:'momentum-pulse-surveys',
  data() {
    return {
      columns: ['cycle', 'action_plan', 'sent_date', 'due_date', 'observers', 'open', 'completed', 'results', 'actions'],
      tableData: getData(),
      options: {
        headings: {
          cycle: 'CYCLE',
          action_plan: 'ACTION PLAN',
          sent_date: 'SENT DATE',
          due_date: 'DUE DATE',
          observers: 'OBSERVERS',
          open: 'OPEN',
          completed: 'COMPLETED',
          results: 'RESULTS',
          actions: 'ACTIONS'
        },
        sortable: ['cycle', 'action_plan'],
        filterable: false,
        texts:{
          count:''
        }
      },
      selectSort: 'm_recent',
      selectSortOptions: [
        {
          label: 'Most Recent',
          value: 'm_recent'
        },
        {
          label: 'Recent',
          value: 'recent'
        }
      ],
      select: 'part1',
      selectOptions: [
        {
          label: 'Part Cycle 1 Name',
          value: 'part1'
        },
        {
          label: 'Part Cycle 2 Name',
          value: 'part2'
        }
      ]
    };
    // let actionPlans = {};

    // if (window.data.pulse_surveys.length > 0) {
    //   window.data.pulse_surveys.forEach((survey) => {
    //     const { action_plan, ...props } = survey;
    //     props.expanded = false;
    //     if (!actionPlans.hasOwnProperty(action_plan.id)) {
    //       actionPlans[action_plan.id] = action_plan;
    //       actionPlans[action_plan.id].surveys = [];
    //       actionPlans[action_plan.id].currentSurvey = props;
    //       actionPlans[action_plan.id].cycle = props.cycle;
    //     }
    //     actionPlans[action_plan.id].surveys.push(props);
    //   });
    // }

    // return {
    //   user: window.user,
    //   actionPlans: actionPlans,
    //   plans: window.data.action_plans || [],
    //   observers: window.data.observers,
    //   observers_selected: [],
    //   observer_types:window.data.observer_types,
    //   cultures:window.cultures,
    //   currentSurvey: null
    // };
  },
  components: {
    QCard,
    QCardMain,
    QCardTitle,
    QCardSeparator,
    QBtn,
    QSelect,
    QField,
    QIcon,
    QSpinner,
    QTooltip,
    QDialog,
    QCardActions,
    QModal,
    EditObserver
  },
  methods: {
    navigateTo: function(nav) {
      window.location = nav;
    },
    openHelp() {
      window.trackEvent('get_help', 'view', 'pulse_survey.landing');
      window.open(
        this.$t('pulse_survey.help_url'),
        'newwindow',
        'width=800,height=500'
      );
    },
    createSurvey() {
      document.location.href = '/pulse-surveys/create';
    },
    newSurveyCycle(actionPlan) {
      document.location.href = '/pulse-surveys/create?action_plan_id=' + actionPlan.id;
    },
    createActionPlan(){
      document.location.href='/action-plans/create';
    },
    getCycles(surveys){
      let cycles = [];
      let _cycles = [];

      surveys.forEach((s) => {
        if (_cycles.indexOf(s.cycle) > -1) {
          return;
        }

        _cycles.push(s.cycle);

        cycles.push({
          value: s.cycle,
          label: s.cycle + ' - ' + s.status_name
        });
      });

      return cycles;
    },
    setCurrentSurvey(index, cycle){
      const curExp = this.actionPlans[index].currentSurvey.expanded;
      this.actionPlans[index].currentSurvey.expanded = false;
      this.actionPlans[index].surveys.forEach((s) => {
        if (s.cycle == cycle) {
          this.actionPlans[index].currentSurvey = s;
          this.actionPlans[index].currentSurvey.expanded = curExp;
        }
      });
    },
    toggleSurveyDeatils(index){
      this.actionPlans[index].currentSurvey.expanded = !this.actionPlans[index].currentSurvey.expanded;
    },
    resendOpenSurveys(survey, done){
      axios.post(`/api/pulse-surveys/${survey.id}/resend`, {
        type: 'open'
      }).then((response) => {
        if (response.data.message) {
          Toast.create(response.data.message);
        }
        if (has(response, 'data.data.pulse_survey_results')) {
          // get the key of the action plan object
          var action_plans = Object.entries(this.actionPlans);
          var action_plan_key = action_plans.find(function(action_plan) {
            return action_plan[1]['id'] == survey.action_plan_id;
          })[0];

          var action_plan = this.actionPlans[action_plan_key];
          var pulse_surveys = Object.entries(action_plan.surveys);

          var pulse_survey_key = pulse_surveys.find(function(pulse_survey) {
            return pulse_survey[1]['id'] == survey.id;
          })[0];


          this.actionPlans[action_plan_key].surveys[pulse_survey_key].pulse_survey_results = get(response, 'data.data.pulse_survey_results');
        }
        done();
      }, (error) => {
        if (error.message) {
          Toast.create(error.message);
        }
        done();
      });
    },
    viewResults(actionPlan){
      // if the survey has open results, we need to display a message to the user
      if(actionPlan.currentSurvey.pulse_survey_results_open.length && !actionPlan.currentSurvey.is_complete) {
        QDialog.create({
          title: this.$t('pulse_survey.modal.view_results.title'),
          message: this.$t('pulse_survey.modal.view_results.warning', {
            open_surveys: actionPlan.currentSurvey.pulse_survey_results_open.length
          }),
          buttons: [
            {
              label: this.$t('pulse_survey.modal.view_results.yes'),
              color: 'primary',
              raised: true,
              handler () {
                axios.post(`/api/pulse-surveys/${actionPlan.currentSurvey.id}/complete`)
                  .then(() => {
                    document.location.href = '/pulse-surveys/' + actionPlan.id + '/results';
                  })
                  .catch(({response}) => {
                    if (response.status === 422) {
                      Toast.create(response.data.message);
                    }
                  });
              }
            },
            this.$t('global.cta.cancel'),
          ]
        });
      } else if (!actionPlan.currentSurvey.is_complete) {
        axios.post(`/api/pulse-surveys/${actionPlan.currentSurvey.id}/complete`)
          .then(() => {
            document.location.href = '/pulse-surveys/' + actionPlan.id + '/results';
          })
          .catch(({response}) => {
            if (response.status === 422) {
              Toast.create(response.data.message);
            }
          });
      } else {
        document.location.href = '/pulse-surveys/' + actionPlan.id + '/results';
      }
    },
    resendSurvey(survey, results, done){
      axios.post(`/api/pulse-surveys/${survey.id}/resend`, {
        observer_id: results.observer.id
      }).then((response) => {
        if (response.data.message) {
          Toast.create(response.data.message);
        }
        if (has(response, 'data.data.pulse_survey_results')) {
          // get the key of the action plan object
          var action_plans = Object.entries(this.actionPlans);
          var action_plan_key = action_plans.find(function(action_plan) {
            return action_plan[1]['id'] == survey.action_plan_id;
          })[0];

          var action_plan = this.actionPlans[action_plan_key];
          var pulse_surveys = Object.entries(action_plan.surveys);

          var pulse_survey_key = pulse_surveys.find(function(pulse_survey) {
            return pulse_survey[1]['id'] == survey.id;
          })[0];


          this.actionPlans[action_plan_key].surveys[pulse_survey_key].pulse_survey_results = get(response, 'data.data.pulse_survey_results');
        }
        done();
      }, (error) => {
        if (error.message) {
          Toast.create(error.message);
        }
        done();
      });
    },
    deleteSurvey(survey, apIndex, sIndex, done){
      axios.post(`/api/pulse-surveys-results/${survey.id}/delete`)
        .then((response) => {
          if (response.data.message) {
            Toast.create(response.data.message);
          }
          if (response.data.success) {
            this.actionPlans[apIndex].currentSurvey.pulse_survey_results.splice(sIndex, 1);
          }
          done();
        }, (error) => {
          if (error.message) {
            Toast.create(error.message);
          }
          done();
        });
    },
    showAddContactModal(survey){
      this.currentSurvey = survey;
      this.observers_selected = [];
      this.$refs.addContactModal.open();
    },
    hideAddContactModal() {
      this.currentSurvey = null;
      this.$refs.addContactModal.close();
    },
    addContacts(done){
      // add the contact
      axios.post(`/api/pulse-surveys/${this.currentSurvey.id}/add`, {
        observers: this.observers_selected
      }).then((response) => {
        if (response.data.redirect) {
          if (response.data.message) {
            window.sessionStorage.setItem('toast-message', response.data.message);
          }
          document.location.href = response.data.redirect;
        }
      }, (error) => {
        if (error.message) {
          Toast.create(error.message);
        }
        done();
      });
    },
    addObserver(){
      this.$refs.editObserver.setDefaultValues(
        {id:null,
          mode:'create',
          first_name:null,
          last_name:null,
          email:null,
          observer_type:Object.keys(this.observer_types)[0],
          culture_id:get(window,'profile.culture.id',1)
        });
      this.$refs.editObserverModal.open();
    },
    editedObserver({action,payload}){
      this.$refs.editObserverModal.close();
      if(action === 'create'){
        this.$set(this.observers,this.observers.length,payload);
        this.$set(this.observers_selected,this.observers_selected.length,payload.id);
      }
      if(action === 'edit'){
        this.observers.forEach((observer,idx)=>{
          if(observer.id === payload.id ){
            this.$set(this.observers,idx,payload);
          }
        });
      }
      if(action === 'delete'){
        this.observers.forEach((observer,idx)=>{
          if(observer.id === payload.id ){
            this.$delete(this.observers,idx);
          }
        });
      }
    },
  },
  mounted() {
    console.log('Component pulse-surveys mounted.');
  },
  computed: {
    observerOptions() {
      if (!this.observers) {
        return [];
      } else {
        return this.observers.map(observer => ({
          value: observer.id,
          label: observer.full_name + ' (' + observer.email + ')'
        })).filter((observer) => {
          if (this.currentSurvey) {
            const observerIds = this.currentSurvey.pulse_survey_results.map(s => s.observer.id);
            if (observerIds.indexOf(observer.value) > -1) {
              return false;
            }
          }
          return observer;
        });
      }
    }

  }
};

function getData() {
  return [{
    cycle: 'cycle 1 nasdfsdfsdfsme',
    action_plan: 'Action Plan name 1',
    sent_date: '05/04/2018',
    due_date: '12/27/2018',
    observers: '10',
    open: '5',
    completed: '4',
    id: 1
  }, {
    cycle: 'cycle 2 name',
    action_plan: 'Action Plan name 2',
    sent_date: '05/05/2018',
    due_date: '12/25/2018',
    observers: '11',
    open: '6',
    completed: '5',
    id: 2
  }, {
    cycle: 'cycle 3 name',
    action_plan: 'Action Plan name 3',
    sent_date: '05/09/2018',
    due_date: '11/29/2018',
    observers: '12',
    open: '7',
    completed: '9',
    id: 3
  }];
}
</script>
