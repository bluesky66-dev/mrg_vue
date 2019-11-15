<template>
    <div id="momentum-pulse-surveys"
        class="momentum-pulse-surveys-create">
        <h3>{{ $t('pulse_survey.create.title') }}</h3>
        <div class="with-hover">
            <div class="hover-over-card-top-right">
                <q-btn v-on:click.prevent="openHelp"
                    icon="help"
                    class=""
                    color="positive"
                    flat>
                    {{ $t('global.nav.get_help') }}
                </q-btn>
            </div>
        </div>
        <q-stepper flat
            @step="onStep"
            ref="stepper"
            v-model="step"
            color="primary">
            <!-- BUILD -->
            <q-step default
                name="build"
                :title="$t('pulse_survey.stepper.build')">
                <div v-if="action_planOptions.length === 0 && action_plans.length === 0">
                    <p>{{ $t('pulse_survey.get_started.empty_message') }}</p>
                    <q-btn big
                        color="primary"
                        @click="createActionPlan">
                        {{ $t('action_plan.create.page_title') }}
                    </q-btn>
                </div>
                <div v-if="action_planOptions.length === 0 && action_plans.length !== 0">
                    <p>{{ $t('pulse_survey.cannot_create_pulse_surveys') }}</p>
                </div>
                <div v-else>
                    <div class="row">
                        <q-field class="col-6"
                            style=""
                            :error="$v.action_plan.$error"
                            :error-label="errorMsg.action_plan">
                            <q-select v-model="action_plan"
                                :disabled="!allowActionPlanSelect"
                                :float-label="$t('pulse_survey.action_plan.picker.label')"
                                :options="action_planOptions">
                            </q-select>
                        </q-field>
                    </div>
                    <div v-if="action_plan"
                        class="action-plan-detail">
                        <div class="row">
                            <div class="col-8">
                                <p class="behavior-report-text">{{ action_plan.behavior.report_text_key_translated }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <q-field class="col-11"
                                     :error="$v.colleague_message.$error"
                                     :error-label="errorMsg.colleague_message"
                            >
                                <q-input v-model="colleague_message"
                                    type="textarea"
                                    :min-rows="2"
                                    :float-label="$t('pulse_survey.colleague_message.label')" />
                            </q-field>
                        </div>
                        <div class="row">
                            <q-field class="col-6 datetime-field"
                                icon="today"
                                :error="$v.due_date.$error"
                                :error-label="errorMsg.due_date"
                                color="primary">
                                <q-datetime v-model="due_date"
                                    format="MMM D, YYYY"
                                    no-clear
                                    :float-label="$t('pulse_survey.card.due_date.label')"
                                    :placeholder="$t('pulse_survey.link.due_date')"
                                    :min="minDate"
                                    type="date"
                                    color="primary" />
                            </q-field>
                        </div>
                    </div>
                </div>
                <q-stepper-navigation>
                    <q-btn flat
                           v-on:click="navigateTo('/pulse-surveys')">
                        {{ $t('global.cta.back') }}
                    </q-btn>
                    <div style="flex:1 1 auto;"></div>
                    <q-btn color="primary"
                        v-on:click="buildNextStep()">
                        {{ $t('global.cta.continue') }}
                    </q-btn>
                </q-stepper-navigation>
            </q-step>
            <!-- /BUILD -->
            <!-- PREVIEW -->
            <q-step default
                name="preview"
                :title="$t('pulse_survey.stepper.preview')">
                <!--
                <div class="viewport-toggles">
                    <q-btn flat
                        @click="switchViewport('desktop')"
                        icon="laptop"
                        :color="viewport == 'desktop' ? 'faded' : 'primary'"
                        :disabled="viewport == 'desktop'">
                        Desktop
                    </q-btn>
                    <q-btn flat
                        @click="switchViewport('mobile')"
                        icon="smartphone"
                        :color="viewport == 'mobile' ? 'faded' : 'primary'"
                        :disabled="viewport == 'mobile'">
                        Mobile
                    </q-btn>
                </div>
                -->
                <q-card v-if="action_plan"
                    class="pulse-survey-container"
                    v-bind:class="{mobile: (viewport == 'mobile')}">
                    <q-card-main>
                        <div class="disabled-div"></div>
                        <div class="text-region">
                            <div class="text-center">
                                <img src="/images/MRG-logo.png"
                                    alt="logo" />
                            </div>
                            <div class="bg-faded hr"></div>
                            <h5 class="text-center text-faded">{{ $t('pulse_survey.title') }}</h5>
                            <div class="body-text">
                                <div>{{ $t('pulse_survey.email.feedback.intro', { 'full_name': user.full_name, 'due_date': formattedDueDate }) }}
                                </div>
                            </div>
                            <div class="body-text"
                                v-if="colleague_message">
                                <div>{{ $t('pulse_survey.email.feedback.colleague_label', { 'first_name': user.first_name }) }}
                                </div>
                                <p class="colleague_message">{{ colleague_message }}</p>
                            </div>
                        </div>
                        <q-card>
                            <q-card-main>
                                <div>{{ $t(action_plan.behavior.rating_feedback_question_key, { 'first_name': user.first_name }) }}
                                </div>
                                <br/>
                                <div class="pulse-survey-radio-labels-numbers">
                                    <div data-label="one">1</div>
                                    <div data-label="two">2</div>
                                    <div data-label="three">3</div>
                                    <div data-label="four">4</div>
                                    <div data-label="five">5</div>
                                    <div data-label="six">6</div>
                                    <div data-label="sevent">7</div>
                                </div>
                                <div class="pulse-survey-radio-buttons">
                                    <div class="radio bar">
                                        <input type="radio"
                                            class="score"
                                            name="score"
                                            value="1">
                                    </div>
                                    <div class="radio bar">
                                        <input type="radio"
                                            class="score"
                                            name="score"
                                            value="2">
                                    </div>
                                    <div class="radio bar">
                                        <input type="radio"
                                            class="score"
                                            name="score"
                                            value="3">
                                    </div>
                                    <div class="radio bar">
                                        <input type="radio"
                                            class="score"
                                            name="score"
                                            value="4">
                                    </div>
                                    <div class="radio bar">
                                        <input type="radio"
                                            class="score"
                                            name="score"
                                            value="5">
                                    </div>
                                    <div class="radio bar">
                                        <input type="radio"
                                            class="score"
                                            name="score"
                                            value="6">
                                    </div>
                                    <div class="radio bar end">
                                        <input type="radio"
                                            class="score"
                                            name="score"
                                            value="7">
                                    </div>
                                    <div class="radio">
                                        <input type="radio"
                                            name="score"
                                            value="-1">
                                    </div>
                                </div>
                                <div class="pulse-survey-radio-labels">
                                    <div data-label="rarely">{{ $t('pulse_survey.preview.recent.rarely') }}</div>
                                    <div data-label="sometimes">{{ $t('pulse_survey.preview.recent.sometimes') }}</div>
                                    <div data-label="regularly">{{ $t('pulse_survey.preview.recent.regularly') }}</div>
                                    <div data-label="often">{{ $t('pulse_survey.preview.recent.often') }}</div>
                                    <div data-label="very-often">{{ $t('pulse_survey.preview.recent.very_often') }}</div>
                                    <div data-label="na">{{ $t('pulse_survey.preview.recent.na') }}</div>
                                </div>
                            </q-card-main>
                        </q-card>
                        <q-card>
                            <q-card-main>
                                {{ $t(action_plan.behavior.additional_feedback_question_key, { first_name: user.first_name }) }}
                                <q-field>
                                    <q-input type="text"
                                        value="" />
                                </q-field>
                            </q-card-main>
                        </q-card>
                        <div class="text-center text-region">
                            <q-btn color="primary">
                                {{ $t('pulse_survey.email.feedback.button') }}
                            </q-btn>
                        </div>
                        <div class="presented-with"
                            v-if="user.billing_organization.logo_path && user.organization.logo_path">
                            <div v-if="user.billing_organization.id == user.organization.id">
                                <span class="presented-text">
                  {{ $t('global.footer.client') }}</span>
                                <img class="organization-logo"
                                    v-bind:src="user.organization.logo_path">
                            </div>
                            <div v-else>
                                <span class="presented-text">{{ $t('global.footer.partner_and_client.prefix') }}</span>
                                <img class="organization-logo"
                                    v-bind:src="user.organization.logo_path">
                                <span class="presented-text">{{ $t('global.footer.partner_and_client.for') }}</span>
                                <img class="organization-logo"
                                    v-bind:src="user.billing_organization.logo_path">
                            </div>
                        </div>
                    </q-card-main>
                </q-card>
                <q-stepper-navigation>
                    <q-btn flat
                        v-on:click="$refs.stepper.previous()">
                        {{ $t('global.cta.back') }}
                    </q-btn>
                    <div style="flex:1 1 auto;"></div>
                    <q-btn color="primary"
                        v-on:click="$refs.stepper.next()">
                        {{ $t('global.cta.continue') }}
                    </q-btn>
                </q-stepper-navigation>
            </q-step>
            <!-- /PREVIEW -->
            <!-- SEND -->
            <q-step default
                name="send"
                :title="$t('pulse_survey.stepper.send')">
                <div class="row">
                  <q-field class="col-8"
                           :error="$v.selectedObservers.$error"
                           :error-label="errorMsg.selectedObservers">
                      <q-select multiple
                          chips
                          color="primary"
                          :float-label="$t('pulse_survey.send.picker.label')"
                          v-model="selectedObservers"
                          :options="observerOptions"></q-select>
                  </q-field>
                  <div class="col" ></div>
                  <div class="col-3 column justify-center"  >
                    <q-btn 
                      color="primary" 
                      icon="person_add" 
                      @click="addObserver">
                      {{ $t('global.cta.add_observer') }}
                    </q-btn>
                  </div>
                </div>
                <q-stepper-navigation>
                    <q-btn flat
                        v-on:click="$refs.stepper.previous()">
                        {{ $t('global.cta.back') }}
                    </q-btn>
                    <div style="flex:1 1 auto;"></div>
                    <q-btn color="primary"
                        @click="sendSurveys">
                        {{ $t('pulse_survey.cta.send') }}
                    </q-btn>
                </q-stepper-navigation>
                <q-inner-loading :visible="inFlight">
                    <q-spinner size="50px"
                               color="primary" />
                </q-inner-loading>
            </q-step>
            <!-- /SEND -->
        </q-stepper>
        <q-modal
          ref="editObserverModal" 
          position="top" 
          noBackdropDismiss
          noEscDismiss>
           <edit-observer
            ref="editObserver" 
            :observerTypes="observer_types"
            :cultures="cultures"
            @successful="editedObserver"
            @cancel="$refs.editObserverModal.close()" />
        </q-modal>

    </div>
</template>

<style lang="scss"
    scoped>
    .q-select{
        &[disabled] {
            pointer-events: none;
        }
    }
    .disabled-div{
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
        z-index: 1;
    }
    .momentum-pulse-surveys-create {
      max-width: 800px;
    }
    .action-plan-detail{
      & > .row:last-child{
        padding-top: 10px;
      }
    }
    .behavior-report-text{
      font-size: 1.2rem;
    }
    .colleague_message{
        line-height: 16px;
    }
    .datetime-field{
      i.q-field-icon{
        color: #0080B2;
      }
    }
    .pulse-survey-container{
      margin: 0 auto;
      position: relative;
      &.mobile{
        max-width: 400px;
      }
    }
    .presented-with{
      padding-top: 20px;
      margin-top: 20px;
      text-align: center;
      position: relative;
      &:before{
        display: block;
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        width: 200px;
        transform: translateX(-50%);
        border-top: 1px solid #000;
      }
    }
    .viewport-toggles{
      text-align: right;
      margin: -10px 0 0;
      padding: 0 0 10px;
    }
</style>
<style lang="scss">
  .viewport-toggles{
    .q-btn-inner{
      flex-direction: column;
      i.on-left{
        margin-right: 0;
        margin-bottom: 3px;
      }
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
  QSelect,
  QField,
  QIcon,
  QSpinner,
  QStepper,
  QStep,
  QStepperNavigation,
  QInput,
  QDatetime,
  QModal,
  QInnerLoading,
  date
} from 'quasar-framework';

import EditObserver from './ObserverCrud/EditObserver.vue';
import { Toast } from 'quasar-framework';
import axios from 'axios';
import { required as requiredValidator, minLength } from 'vuelidate/lib/validators';
import get from 'lodash/get';
import queryString from 'query-string';

import persistentApplicationStateMixin from '../utils/persistentApplicationStateMixin';
import serverValidationMixin from '../utils/serverValidationMixin';
const today = new Date();

export default {
  name: 'momentum-pulse-surveys-create',
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
    QStepper,
    QStep,
    QStepperNavigation,
    QInput,
    QDatetime,
    QModal,
    QInnerLoading,
    EditObserver
  },
  mixins: [
    persistentApplicationStateMixin(),
    serverValidationMixin
  ],
  persistentApplicationState: {
    applicationKey: 'pulse_survey',
    dataKeys: [
      'step',
      'action_plan',
      'colleague_message',
    ]
  },
  serverValidation: {
    parameters: [{
      name: 'action_plan',
      frontValidations: [{
        name: 'required',
        validator: requiredValidator,
        errorMsg: function() {
          return this.$t('pulse_survey.validation.action_plan');
        }
      }]
    },
    {
      name: 'colleague_message',
      frontValidations: [{
        name: 'required',
        validator: requiredValidator,
        errorMsg: function() {
          return this.$t('global.validation.required', {
            attribute: this.$t('pulse_survey.colleague_message.label')
          });
        }
      }]
    },
    {
      name: 'due_date',
      frontValidations: [{
        name: 'required',
        validator: function (value) {
          return value ? true : false;
        },
        errorMsg: function() {
          return this.$t('global.validation.required', {
            attribute: this.$t('pulse_survey.card.due_date.label')
          });
        }
      }]
    },
    {
      name: 'selectedObservers',
      frontValidations: [{
        name: 'minLength',
        validator: minLength(1),
        errorMsg: function() {
          return this.$t('pulse_survey.validation.send');
        }
      },{
        name: 'required',
        validator: requiredValidator,
        errorMsg: function() {
          return this.$t('pulse_survey.validation.send');
        }
      }
      ]
    }
    ]
  },
  methods: { openHelp() {
    window.trackEvent('get_help', 'view', `pulse_survey.create.${this.step}`);
    window.open(
      this.$t('pulse_survey.help_url'),
      'newwindow',
      'width=800,height=500'
    );
  },
  onStep() {

  },
  navigateTo: function(nav) {
    window.location = nav;
  },
  sendSurveys(e, done){
    const data = {
      action_plan_id: this.action_plan.id,
      observers: this.selectedObservers,
      due_at: this.due_date,
      message: this.colleague_message
    };
    if (this.$v.$invalid) {
      this.$v.$touch();
      return;
    }
    this.$v.$reset();
    this.inFlight = true;
    axios.post('/api/pulse-surveys', data)
      .then((resp) => {
        done();
        this.$emit('successful');
        if (resp.data.redirect) {
          if (resp.data.message) {
            window.sessionStorage.setItem('toast-message', resp.data.message);
          }
          window.location = resp.data.redirect;
        }
      }, (error) => {
        if (error.message) {
          Toast.create(error.message);
        }
        done();
      })
      .catch(({ response }) => {
        done();
        if (response && response.status === 422) {
          const errors = get(response, 'data.errors');

          this.errors = {
            ...this.errors,
            ...errors
          };
          this.$v.$touch();
        }
      })
      .then(() => {
        this.inFlight = false;
      });
  },
  switchViewport(to){
    this.viewport = to;
  },
  createActionPlan(){
    document.location.href='/action-plans/create';
  },
  addObserver(){
    //console.log('addObserver');
    //this.focusObserver = null;
    //this.defaultCultureId = get(window,'profile.culture.id',1);
    //console.log('addObserver',Object.keys(this.observers));
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
  buildNextStep() {
    this.$v.action_plan.$touch();
    this.$v.due_date.$touch();
    this.$v.colleague_message.$touch();
    if (this.$v.action_plan.$error || this.$v.due_date.$error || this.$v.colleague_message.$error ) return;
    this.$refs.stepper.next();
  },
  editedObserver({action,payload}){
    //console.log('editedObserver',{action,payload});
    this.$refs.editObserverModal.close();
    if(action === 'create'){
      this.$set(this.observers,this.observers.length,payload);
      this.$set(this.selectedObservers,this.selectedObservers.length,payload.id);

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
  data() {
    const { action_plan_id } = queryString.parse(location.search) || {};
    let allowActionPlanSelect = true;
    let actionPlan = null;

    if (action_plan_id) {
      window.data.action_plans.forEach((p) => {
        if (!actionPlan && p.id == action_plan_id) {
          allowActionPlanSelect = false;
          actionPlan = p;
        }
      });
    }

    return {
      step: '',
      allowActionPlanSelect: allowActionPlanSelect,
      action_plan: actionPlan,
      due_date: null,
      colleague_message: this.$t('pulse_survey.colleague_message.placeholder'),
      observers:window.data.observers,
      observer_types:window.data.observer_types,
      cultures:window.cultures,
      selectedObservers: [],
      minDate: today,
      user: window.user,
      action_plans: window.data.action_plans,
      viewport: 'desktop',
      inFlight: false,
    };
  },
  computed: {
    action_planOptions() {
      // filter out the action plans we can't select from
      var action_plans = window.data.action_plans.filter(function(action_plan){
        if (action_plan.is_complete) {
          return false;
        }
          
        return action_plan.can_create_pulse_survey;
      });

      return action_plans.map((p) => {
        return { value: p, label: p.label };
      });
    },
    observerOptions() {
      if (!this.observers) {
        return [];
      } else {
        return this.observers.map(observer => ({
          value: observer.id,
          label: observer.full_name + ' (' + observer.email + ')'
        }));
      }
    },
    formattedDueDate() {
      return date.formatDate(this.due_date, 'MMMM D, YYYY');
    }

  },
  mounted() {
    console.log('Component pulse-surveys-create mounted.');
  }
};

</script>
