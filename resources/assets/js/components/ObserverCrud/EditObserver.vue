<template>
    <div style="min-width:480px"
        class="layout-padding"
        :disabled="inFlight">
        <div v-if="mode === 'create'"
            class="q-card-title">
            {{ $t('profile.contact.add_contact.title') }}
        </div>
        <div v-if="mode === 'edit'"
            class="q-card-title">
            {{ $t('profile.contact.edit_contact.title') }}
        </div>
        <div v-if="mode === 'delete'"
            class="q-card-title">
            {{ $t('profile.contact.delete_contact.title') }}
        </div>
        <p v-if="mode === 'delete'"
             class="">
            {{ $t('profile.contact.delete_contact.message') }}
        </p>
        <form v-on:submit.prevent="$refs.last_name.focus"
            :disabled="inFlight">
            <q-field :error="$v.first_name.$error"
                :error-label="first_nameErrorMsg">
                <q-input :disable="mode === 'delete'"
                    ref="first_name"
                    type="text"
                    v-model="first_name"
                    @input="resetValidation"
                    :float-label="$t('profile.contact.first_name')"
                    autofocus />
            </q-field>
        </form>
        <form v-on:submit.prevent="$refs.email.focus"
            :disabled="inFlight">
            <q-field :error="$v.last_name.$error"
                :error-label="last_nameErrorMsg">
                <q-input :disable="mode === 'delete'"
                    ref="last_name"
                    type="text"
                    v-model="last_name"
                    @input="resetValidation"
                    :float-label="$t('profile.contact.last_name')" />
            </q-field>
        </form>
        <form v-on:submit.prevent="()=>{}"
            :disabled="inFlight">
            <q-field :error="$v.email.$error"
                :error-label="emailErrorMsg">
                <q-input :disable="mode === 'delete'"
                    ref="email"
                    type="text"
                    v-model="email"
                    @input="resetValidation"
                    :float-label="$t('profile.contact.email_address')" />
            </q-field>
        </form>
        <form v-on:submit.prevent="()=>{}"
            :disabled="inFlight">
            <q-field :error="$v.culture_id.$error"
                :error-label="culture_idErrorMsg">
                <q-select :disable="mode === 'delete'"
                    v-model="culture_id"
                    :float-label="$t('profile.contact.culture.label')"
                    :options="observerCulturesSelect" />
            </q-field>
        </form>
        <form v-on:submit.prevent="save"
            :disabled="inFlight">
            <q-field :error="$v.observer_type.$error"
                :error-label="observer_typeErrorMsg">
                <q-select :disable="mode === 'delete'"
                    v-model="observer_type"
                    :float-label="$t('profile.contact.observer_type.label')"
                    :options="observerTypesSelect" />
            </q-field>
        </form>
        <div style="margin-top:20px"
            class="row">
            <q-btn v-if="mode !== 'delete'"
                v-model="inFlight"
                v-on:click="save"
                color="primary"
                class="col-4">
                {{ $t('global.cta.save_changes') }}
            </q-btn>
            <q-btn v-if="mode === 'delete'"
                v-model="inFlight"
                v-on:click="save"
                color="primary"
                class="col-4">
                {{ $t('profile.contact.delete_contact.title') }}
            </q-btn>
            <div class="col-4" />
            <q-btn :disabled="inFlight"
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
import { QField, QBtn, QInput, Toast, QSelect } from 'quasar-framework';
import axios from 'axios';
import get from 'lodash/get';

import { required as requiredValidator, email } from 'vuelidate/lib/validators';

import { createServerValidation } from '../../utils/serverValidationMixin';

const serverValidation = createServerValidation({
  parameters: [
    {
      name: 'first_name',
      frontValidations: [
        {
          name: 'required',
          validator: requiredValidator,
          errorMsg: function() {
            return this.$t('global.validation.required', {
              attribute: this.$t('profile.contact.first_name')
            });
          }
        }
      ]
    },
    {
      name: 'last_name',
      frontValidations: [
        {
          name: 'required',
          validator: requiredValidator,
          errorMsg: function() {
            return this.$t('global.validation.required', {
              attribute: this.$t('profile.contact.last_name')
            });
          }
        }
      ]
    },
    {
      name: 'email',
      frontValidations: [
        {
          name: 'required',
          validator: requiredValidator,
          errorMsg: function() {
            return this.$t('global.validation.required', {
              attribute: this.$t('profile.contact.email_address')
            });
          }
        },
        {
          name: 'email',
          validator: email,
          errorMsg: function() {
            return this.$t('global.validation.email', {
              attribute: this.$t('profile.contact.email_address')
            });
          }
        }
      ]
    },
    {
      name: 'observer_type',
      frontValidations: [
        {
          name: 'required',
          validator: requiredValidator,
          errorMsg: function() {
            return this.$t('global.validation.required', {
              attribute: this.$t('profile.contact.observer_type.label')
            });
          }
        }
      ]
    },
    {
      name: 'culture_id',
      frontValidations: [
        {
          name: 'required',
          validator: requiredValidator,
          errorMsg: function() {
            return this.$t('global.validation.required', {
              attribute: this.$t('profile.contact.culture.label')
            });
          }
        }
      ]
    }
  ],
  initData() {
    return {
      first_name: null,
      last_name: null,
      email: null,
      culture_id: 1,
      observer_type: 'peer'
    };
  }
});

export default {
  components: {
    QField,
    QBtn,
    QInput,
    QSelect
  },
  props: ['observerTypes', 'cultures', 'defaultCultureId'],
  mixins: [serverValidation],
  data() {
    return {
      inFlight: false,
      mode: null,
      id: null
    };
  },
  methods: {
    setDefaultValues(defaults) {
      console.log('def', defaults);
      Object.keys(defaults).forEach(key => {
        this[key] = defaults[key];
        console.log(key, this[key]);
      });
    },
    cancel() {
      var modeLabel = this.mode;
      if (this.mode == 'create') {
        modeLabel = 'add';
      }
      //console.log('cancel');
      window.trackEvent('observer', `${modeLabel}`, 'cancel');
      this.$emit('cancel');
    },
    save() {
      if (this.$v.$invalid) {
        this.$v.$touch();
        return;
      }
      this.$v.$reset();
      this.inFlight = true;
      //const endPoint = this.id ? '/api/observers/' + this.id : '/api/observers';
      const mode = this.mode;
      const id = this.id;
      const endpoint = mode !== 'delete' 
        ? '/api/observers'
        : `/api/observers/${this.id}/delete`;
      axios
        .post(endpoint, {
          ...this.id  ? {
            id:this.id 
          } : {},
          first_name: this.first_name,
          last_name: this.last_name,
          email: this.email,
          observer_type: this.observer_type,
          culture_id: this.culture_id
        })
        .then(result => {
          //console.log(result);

          const message = get(result,'data.message');
          const observer = get(result,'data.observer',{id:id});

          Toast.create({
            html:(message)
          });

          this.$emit('successful',{
            action: mode,
            payload: observer
          });

        })
        .catch(error => {
          const { response } = error;
          console.dir(error);
          if (response && response.status === 422) {
            const errors = get(response, 'data.errors');

            this.errors = {
              ...this.errors,
              ...errors
            };
            this.$v.$touch();
            return;
          }
          // Toast.create.negative({
          //   html: '<pre>' + JSON.stringify(response.data, null, ' ') + '</pre>'
          // });
          console.error(response);
        })
        .then(() => {
          this.inFlight = false;
        });
    }
  },
  computed: {
    observerTypesSelect() {
      return Object.keys(this.observerTypes).map(observerType => {
        return {
          label: this.observerTypes[observerType],
          value: observerType
        };
      });
    },
    observerCulturesSelect() {
      return this.cultures.map(culture => {
        return {
          label: culture.name_key_translated,
          value: culture.id
        };
      });
    }
  },
  mounted() {
    //console.log('Component change password mounted.');
  }
};
</script>
