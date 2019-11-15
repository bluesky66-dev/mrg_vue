<template>

<q-card flat class="no-margin">
  <q-card-title>
    {{ $t('action_plan.action_steps.create.title') }}
  </q-card-title>
  <q-card-main>
    <QField
    :error="$v.name.$error"
    :error-label="errorMsg.name">
      <QInput
        ref="nameInput"
        :max-length="200"
        v-model="name" 
        :float-label="$t('action_plan.action_steps.create.name')"
        type="text" 
        autofocus />
    </QField>
    <QField 
    :error="$v.description.$error"
    :error-label="errorMsg.description"
    :count="config.max_char_length" >
      <QInput 
        ref="descriptionInput"
        type="textarea" 
        v-model="description" 
        :float-label="$t('action_plan.action_steps.create.description')"
        :max-height="100"
        :min-rows="7"
        :max-length="config.max_char_length" />
    </QField>
  </q-card-main>
  <q-card-separator/>
  <q-card-actions>
    <q-btn
      @click="save"
      flat 
      color="primary">
      {{ $t('action_plan.action_steps.create.save') }}
    </q-btn>
    <div class="col"></div>
    <q-btn 
      flat
      @click="cancel">
      {{ $t('action_plan.action_steps.create.discard') }}
    </q-btn>
  </q-card-actions>
  <q-inner-loading :visible="inFlight">
      <q-spinner size="20%" color="primary"></q-spinner>
  </q-inner-loading>
</q-card>
</template>
<script>
import {
  QCard,
  QCardMain,
  QCardTitle,
  QCardSeparator,
  QBtn,
  QCardActions,
  QStepperNavigation,
  QField,
  QInput,
  Toast,
  QInnerLoading,
  QSpinner
} from 'quasar-framework';

import serverValidationMixin from '../../utils/serverValidationMixin';
import { required as requiredValidator } from 'vuelidate/lib/validators';
import axios from 'axios';
import get from 'lodash/get';

export default {
  name: 'CreateActionStep',
  components: {
    QCard,
    QCardMain,
    QCardTitle,
    QCardSeparator,
    QBtn,
    QCardActions,
    QStepperNavigation,
    QField,
    QInput,
    QInnerLoading,
    QSpinner
  },
  props: ['emphasis','behaviorId','tempID', 'actionPlanID'],
  data() {
    return {
      name:null,
      description:null,
      config: window.frontend_config,
      inFlight:false,
    };
  },
  mixins: [
    serverValidationMixin
  ],
  serverValidation: {
    parameters: [{
      name: 'name',
      frontValidations: [{
        name: 'required',
        validator: requiredValidator,
        errorMsg: function() {
          return this.$t('global.validation.required', {
            attribute: this.$t('action_plan.action_steps.create.name')
          });
        }
      }]
    },
    {
      name: 'description',
      frontValidations: [{
        name: 'required',
        validator: requiredValidator,
        errorMsg: function() {
          return this.$t('global.validation.required', {
            attribute: this.$t('action_plan.action_steps.create.description')
          });
        }
      }]
    }
    ]
  },
  methods:{
    cancel(){
      //console.log('cancel');
      this.name = null;
      this.description = null;
      this.$emit('cancel');
    },
    save(){
      if (this.$v.$invalid) {
        this.$v.$touch();
        return;
      }
      this.$v.$reset();
      this.inFlight = true;
      axios
        .post('/api/action-steps',{
          emphasis:this.emphasis,
          temp_action_plan_id: this.tempID,
          action_plan_id: this.actionPlanID,
          behavior_id:this.behaviorId,
          name:this.name,
          description:this.description
        })
        .then(({data})=>{
          //console.dir(data);
          // console.log('save');
          Toast.create(data.message);
          this.$emit('action-step-save-successful', {action_steps: data.data.action_steps, action_step: data.data.action_step});
        })
        .catch(({response})=>{
          if(response.status === 422){
            const errors = get(response,'data.errors');

            this.errors = {
              ...this.errors,
              ...errors
            };

            this.$v.$touch();
          }
          //console.dir(response);
        })
        .then(()=>{
          this.inFlight = false;
          this.name = null;
          this.description = null;
          this.$emit('saved');
        });
    },
    focusNameField(){
      this.$refs.nameInput.focus();
    }
  }
};

</script>
<style lang="css"
    scoped>
  .q-card-title{
    font-weight: bold;
  }

</style>
