<template>
  <div id="momentum-profile-index"
      class="momentum-profile-index">
    <q-modal ref="passwordModal"
        position="top"
        noBackdropDismiss
        noEscDismiss>
      <change-password v-on:cancel="closePasswordModal"
          v-on:successful="closePasswordModal" />
    </q-modal>
    <h3>{{ $t('profile.title') }}</h3>
    <q-card class="with-hover">
      <div class="hover-over-card-top-right">
        <q-btn v-on:click.prevent="openHelp"
            icon="help"
            class=""
            color="positive"
            flat>
          {{ $t('global.nav.get_help') }}
        </q-btn>
      </div>
      <q-card-main class="column">
        <q-card flat
            class="row"
            style="">
          <img class="self-start"
              src="../../../images/generic-avatar.svg"
              alt="Avatar">
          <div style="flex:1 1 auto"></div>
          <q-btn v-on:click.prevent="open360results"
              icon="link"
              style=""
              class="justify-end self-start"
              color="primary"
              flat>
            {{ $t('global.link.my_360_results') }}
          </q-btn>
        </q-card>
        <q-card class="column">
          <q-card-main>
            <q-field :error="$v.first_name.$error"
                :error-label="first_nameErrorMsg">
              <q-input v-model="first_name"
                  @input="first_nameDelayedTouch"
                  :float-label="$t('profile.account_first_name')" />
            </q-field>
            <q-field :error="$v.last_name.$error"
                :error-label="last_nameErrorMsg">
              <q-input v-model="last_name"
                  @input="last_nameDelayedTouch"
                  :float-label="$t('profile.account_last_name')" />
            </q-field>
            <q-field :error="$v.email.$error"
                :error-label="emailErrorMsg">
              <q-input v-model="email"
                  @input="emailDelayedTouch"
                  :float-label="$t('profile.account_email')" />
            </q-field>
            <q-field :error="$v.culture_id.$error"
                :error-label="culture_idErrorMsg">
              <q-select class=""
                  :float-label="$t('global.validation.attributes.culture_id')"
                  v-model="culture_id"
                  :options="cultureOptions" />
            </q-field>
            <q-card-actions class="">
              <q-btn v-on:click="save"
                  class="col-5">
                {{ $t('profile.cta.save_changes') }}
              </q-btn>
              <div class="col-2"></div>
              <q-btn @click="$refs.passwordModal.open()"
                  flat
                  class="col-5">
                {{ $t('profile.change_password') }}
              </q-btn>
            </q-card-actions>
          </q-card-main>
        </q-card>
      </q-card-main>
      <q-inner-loading :visible="inFlight">
        <q-spinner size="50px"
            color="primary" />
      </q-inner-loading>
    </q-card>
    <q-card>
      <q-card-main>
        <observer-crud/>
      </q-card-main>
    </q-card>
    <!-- <pre>{{rawProfile}}</pre> -->
  </div>

</template>
<style lang="scss" scoped>
.momentum-profile-index {
  // max-width: 700px;
}
</style>
<script>
/* global data */
import {
  QCard,
  QCardMain,
  QCardTitle,
  QCardSeparator,
  QField,
  QBtn,
  QInput,
  QCardActions,
  QModal,
  Toast,
  QInnerLoading,
  QSpinner,
  QSelect
} from 'quasar-framework';
import axios from 'axios';
import get from 'lodash/get';


import {required as requiredValidator, email} from 'vuelidate/lib/validators';
import ChangePassword from './ChangePassword';
import ObserverCrud from '../ObserverCrud/ObserverCrud';

import { createServerValidation } from '../../utils/serverValidationMixin';

const serverValidation = createServerValidation({
  parameters: [
    {
      name: 'first_name',
      frontValidations: [
        {
          name: 'required',
          validator: requiredValidator,
          errorMsg: function () {
            return this.$t('global.validation.required', {
              attribute: this.$t('profile.account_first_name')
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
          errorMsg: function () {
            return this.$t('global.validation.required', {
              attribute: this.$t('profile.account_last_name')
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
          errorMsg: function () {
            return this.$t('global.validation.required', {
              attribute: this.$t('profile.account_email')
            });
          }
        },
        {
          name: 'email',
          validator: email,
          errorMsg: function () {
            return this.$t('global.validation.email', {
              attribute: this.$t('profile.account_email')
            });
          }
        }
      ]
    },
    {
      name: 'culture_id',
      frontValidations: []
    }
  ],
  initData() {
    return window.data.user;
  }
});

export default {
  name: 'momentum-profile-index',
  mixins: [serverValidation],
  components: {
    QCard,
    QCardMain,
    QCardTitle,
    QCardSeparator,
    QField,
    QBtn,
    QInput,
    QCardActions,
    QModal,
    QInnerLoading,
    QSpinner,
    QSelect,
    ChangePassword,
    ObserverCrud
  },
  data() {
    const data = {
      inFlight: false,
      cultures: window.cultures,
    };
    return data;
  },
  serverValidation: {
    foo: 'bar'
  },
  methods: {
    save() {
      if (this.$v.$invalid) {
        this.$v.$touch();
        return;
      }
      this.$v.$reset();
      this.inFlight = true;
      axios
        .post('/api/profile', {
          first_name: this.first_name,
          last_name: this.last_name,
          email: this.email,
          culture_id: this.culture_id
        })
        .then(({data}) => {
          //console.dir(data);
          // console.log('save');

          const culture = this.cultures.find(culture => culture.id === this.culture_id);
          //console.log('culture',culture);
          if (culture && culture.code) {
            this.$i18n.locale = culture.code;
          }
          this.$nextTick(() => {
            Toast.create(data.message);
            try {
              Array.from(document.querySelectorAll('[data-culture-key]'))
                .map((el) => {
                  el.innerHTML = this.$t(el.dataset['cultureKey']);
                });
            } catch (e) {
              console.error(e);
              document.location.reload();
            }

          });


          this.$emit('successful');
        })
        .catch(({response}) => {
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
    openHelp() {
      window.trackEvent('get_help', 'view', 'profile.landing');
      window.open(
        this.$t('profile.help_url'),
        'newwindow',
        'width=800,height=500'
      );
    },
    open360results() {
      window.trackEvent('360_results', 'view', 'profile');
      window.location = window.user.lea_results_link;

    },
    closePasswordModal() {
      this.$refs.passwordModal.close();
    }
  },
  computed: {
    cultureOptions() {
      if (!this.cultures) {
        return [];
      } else {
        return this.cultures.map(culture => ({
          value: culture.id,
          label: culture.name_key_translated
        }));
      }
    },
    rawProfile() {
      return JSON.stringify(window.data, null, ' ');
    }
  },
  mounted() {
    /*eslint-disable */
    if(data.reset){
      this.$refs.passwordModal.open();
    }
    /*eslint-enable */
  }
};
</script>
