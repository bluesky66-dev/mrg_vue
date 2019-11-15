<template>
  <div style="min-width:480px"  class="layout-padding" :disabled="inFlight" >
    <div class="q-card-title">{{ $t('profile.change_password') }}</div>
    <form 
      v-on:submit.prevent="$refs.password_confirmation.focus"
      :disabled="inFlight">
      <q-field
        :error="$v.password.$error" 
        :error-label="passwordErrorMsg">
        <q-input
          ref="password"
          type="password"
          v-model="password"
          @input="resetValidation"
          :float-label="$t('profile.account_password')"
          autofocus />
      </q-field>
    </form>
    <form 
      v-on:submit.prevent="save"
      :disabled="inFlight">
      <q-field
        :error="$v.password_confirmation.$error" 
        :error-label="password_confirmationErrorMsg">
          <q-input
            ref="password_confirmation"
            type="password"
            @input="resetValidation" 
            v-model="password_confirmation" 
            :float-label="$t('profile.account_confirm_password')" 
          />
      </q-field>
    </form>
    <div style="margin-top:20px" class="row" >
      <q-btn
        v-model="inFlight"
        v-on:click="save"
        color="primary"
        class="col-4">
        {{ $t('global.cta.save_changes') }}
      </q-btn>
      <div class="col-4"></div>
      <q-btn
        :disabled="inFlight"
        v-on:click="cancel"
        class="col-4">
        {{ $t('global.cta.cancel') }}
      </q-btn>
    </div>
  </div>
</template>
<style lang="scss" scoped>


</style>
<script>
import {
  QField,
  QBtn,
  QInput,
  Toast
} from 'quasar-framework';
import axios from 'axios';
import get from 'lodash/get';


import { required as requiredValidator, sameAs } from 'vuelidate/lib/validators';

import { createServerValidation } from '../../utils/serverValidationMixin';

const serverValidation = createServerValidation({
  parameters: [
    {
      name: 'password',
      frontValidations: [
        {
          name: 'required',
          validator: requiredValidator,
          errorMsg: function() {
            return this.$t('global.validation.required', {
              attribute: this.$t('profile.account_password')
            });
          }
        }
      ]
    },
    {
      name: 'password_confirmation',
      frontValidations: [
        {
          name: 'required',
          validator: requiredValidator,
          errorMsg: function() {
            return this.$t('global.validation.required', {
              attribute: this.$t('profile.account_confirm_password')
            });
          }
        },
        {
          name: 'sameAs',
          validator: sameAs('password'),
          errorMsg: function() {
            return this.$t('global.validation.same', {
              attribute: this.$t('profile.account_password'),
              other: this.$t('profile.account_confirm_password')
            });
          }
          
        }
      ]
    }
  ],
  initData() {
    return {};
  }
});


export default {
  components: {
    QField,
    QBtn,
    QInput
  },
  mixins:[serverValidation],
  data(){
    return {
      inFlight:false,
      password:null,
      password_confirmation:null
    };
  },
  methods: {
    cancel(){
      //console.log('cancel');
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
        .post('/api/profile',{
          password:this.password,
          password_confirmation:this.password_confirmation
        })
        .then(({data})=>{
          //console.dir(data);
          // console.log('save');
          Toast.create(data.message);
          this.$emit('successful');
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
        });
    }
  },
  computed:{

  },
  mounted() {
    //console.log('Component change password mounted.');
  }
};
</script>
