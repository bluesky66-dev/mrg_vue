<template>
<div>
  <!-- <transition
  enter-active-class="animated bounceInLeft"
  leave-active-class="animated bounceOutRight"
  appear
>
  <q-alert
    v-if="alertVisible"
    color="primary"
    icon="warning"
    appear
    class="q-mb-sm"
  >
    Momentum Will Be Down Saturday - April 21 - from 7am EST to 12pm EST
  </q-alert>
</transition> -->
<q-card class="self-center login-card">
        <q-alert
            v-if="browserOutdated"
            style="font-size: 16px"
        >
            {{ $t('login.browser_outdated') }}
        </q-alert>
        <q-card-main class="column justify-center items-center">
            <img height="57px"
                src="../../images/MRG-logo.png"
                alt="MRG" />
            <h1 v-if="stepIsEmail">{{ $t('login.title') }}</h1>
            <h2 v-if="stepIsEmail">{{ $t('login.sub_title') }}</h2>
            <div class="row self-stretch justify-center">
                <div v-if="stepIsEmail"
                    id="submit-email"
                    class="col-10 login-form column self-stretch justify-center submit-email">
                    <form :disabled="networkRequestInFlight"
                        v-on:submit.prevent="submitEmail">
                        <q-field icon="mail"
                            :error="$v.email.$error"
                            :error-label="emailErrorMsg">
                            <q-input type="email"
                                @input="delayEmailTouch"
                                :float-label="$t('login.email.label')"
                                v-model.trim="email"
                                autofocus
                                @blur="$v.email.$touch" />
                        </q-field>
                    </form>
                    <q-field class="col-auto">
                        <q-btn v-on:click.prevent="submitEmail"
                            v-model="networkRequestInFlight"
                            class="full-width"
                            color="primary"
                            big>
                            {{ $t('login.sign_in.cta') }}
                        </q-btn>
                    </q-field>
                    <q-field class="col-auto">
                        <q-btn v-on:click="forgotEmail"
                               class="full-width"
                               flat
                               color="primary"
                               big>
                            {{ $t('login.forgot_email.cta') }}
                        </q-btn>
                    </q-field>
                </div>
                <div v-if="stepIsPassword"
                    id="submit-password"
                    class="col-10 login-form column self-stretch justify-center submit-password">
                    <i18n path="login.password_submit_instructions"
                        tag="h3">
                        <strong place="email">{{ email }}</strong>
                    </i18n>
                    <form :disabled="networkRequestInFlight"
                        v-on:submit.prevent="submitPassword">
                        <q-field :error="$v.password.$error"
                            :error-label="passwordErrorMsg">
                            <q-input type="password"
                                @input="delayPasswordTouch"
                                :float-label="$t('login.password.label')"
                                v-model.trim="password"
                                @blur="delayPasswordTouch"
                                autofocus />
                        </q-field>
                    </form>
                    <div class="forgot-link row justify-end">
                        <q-btn :disabled="networkRequestInFlight"
                            v-on:click.prevent="requestMagicLink(true)"
                            flat
                            small
                            class="self-end">
                            {{ $t('login.password.forgot_link') }}
                        </q-btn>
                    </div>
                    <q-field>
                        <div class="col-auto row  justify-around">
                            <q-btn :disabled="networkRequestInFlight"
                                @click="backTooEmail"
                                class="col-5">
                                {{ $t('login.password_submit_cancel') }}
                            </q-btn>
                            <q-btn @click="submitPassword"
                                v-model="passwordLoading"
                                class="col-5"
                                color="primary"
                                :disabled="networkRequestInFlight">
                                {{ $t('login.submit_password_cta') }}
                            </q-btn>
                        </div>
                    </q-field>
                </div>
                <div v-if="stepIsLoading"
                    id="magic-link-loading"
                    class="col-10 login-form column self-stretch  items-center  justify-center magic-link-loading">
                    <q-spinner class="justify-center"
                        :size="100"
                        color="primary" />
                </div>
                <div v-if="stepIsLinkSent"
                    id="magic-link-sent"
                    class="col-10 login-form column self-stretch  items-center  justify-center magic-link-sent">
                    <i18n :path="magicLinkMsgFromServer.key"
                        tag="h3">
                        <strong place="email">
                          {{ magicLinkMsgFromServer.replacements.email }}
                        </strong>
                    </i18n>
                </div>
            </div>
            <div v-if="stepIsEmail"
                class="self-stretch row justify-around">
                <div class="col login-page-footer-link">
                    <a :href="$t('login.mrg_link.url')" target="_blank">{{ $t('login.mrg_link.label') }}</a>
                </div>
                <div class="col login-page-footer-link">
                    <a :href="$t('login.privacy_link.url')" target="_blank">
                      {{ $t('login.privacy_link.label') }}
                    </a>
                </div>
            </div>
            <div v-if="stepIsEmail"
                class="self-stretch row justify-around">
                <div class="col login-page-footer-link">
                    {{ $t('login.copyright_link.label') }}
                </div>
            </div>
        </q-card-main>
    </q-card>
</div>
    
</template>
<style>
#momentum-login {
  /*background-color: red;*/
  min-height: 100vh;
}
</style>
<style lang="scss" scoped>
.login-card {
  max-width: 500px;
  .q-card-main {
    & > img {
      width: 167px;
    }
    h1 {
      font-size: 40px;
      margin: 10px;
      text-align: center;
    }
    h2 {
      font-size: 26px;
      margin: 10px;
      text-align: center;
    }
    .login-form {
      min-width: 400px;
      min-height: 300px;
      margin-bottom: 3em;
      &[disabled] {
        pointer-events: none;
      }
    }
    .login-page-footer-link {
      text-align: center;
      margin: 0 0 1em 0;
      a {
        color: black;
        text-decoration: underline;
      }
    }
  }
}

.submit-password {
  & > h3 {
    font-size: 20px;
  }
}

.magic-link-sent h3 {
  font-size: 25px;
  max-width: 100%;
}

.docs-input {
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}

.fade-enter,
.fade-leave-to {
  opacity: 0;
}
</style>
<script>
import {
  QCard,
  QCardMain,
  QCardSeparator,
  QBtn,
  QInput,
  QField,
  QSpinner,
  QAlert,
  Dialog as QDialog
} from 'quasar-framework';

import { required as requiredValidator, email } from 'vuelidate/lib/validators';

import axios from 'axios';
import get from 'lodash/get';

//import { required, sameAs } from 'vuelidate/lib/validators';

const touchMap = new WeakMap();

export default {
  name: 'momentum-login',
  components: {
    QCard,
    QCardMain,
    QCardSeparator,
    QBtn,
    QInput,
    QField,
    QSpinner,
    QDialog,
    QAlert
  },
  data() {
    return {
      //step: 'link-sent',
      step: 'email',
      email: null,
      emailErrorMsgFromServer: null,
      emailErrorFromServer: false,
      emailLoading: false,
      password: null,
      passwordErrorMsgFromServer: null,
      passwordErrorFromServer: false,
      passwordLoading: false,
      magicLinkMsgFromServer: {
        message: null,
        key: null,
        replacements: null
      },
      requestMagicLinkLoading: false,
      alertVisible: true
    };
  },
  validations: {
    email: {
      requiredValidator,
      email,
      unknownEmail() {
        return !this.emailErrorFromServer;
      }
    },
    password: {
      requiredValidator,
      unknownPassword() {
        return !this.passwordErrorFromServer;
      }
    }
  },
  created() {},
  methods: {
    backTooEmail() {
      this.step = 'email';
      this.passwoed = null;
    },
    delayEmailTouch() {
      this.resetEmailValidation();
      if (touchMap.has(this.$v.email)) {
        clearTimeout(touchMap.get(this.$v.email));
      }
      touchMap.set(this.$v.email, setTimeout(this.$v.email.$touch, 10000));
    },
    delayPasswordTouch() {
      this.resetPasswordValidation();
      if (touchMap.has(this.$v.password)) {
        clearTimeout(touchMap.get(this.$v.password));
      }
      touchMap.set(
        this.$v.password,
        setTimeout(this.$v.password.$touch, 10000)
      );
    },
    submitEmail() {
      //console.log('submitEmail');
      if (this.$v.email.$invalid) {
        this.$v.email.$touch();
        this.emailLoading = false;
        return;
      }
      this.emailLoading = true;
      axios
        .post('login', {
          email: this.email
        })
        .then(({ data }) => {
          if (data.needs_password === true) {
            this.step = 'password';
          } else if (data.message) {
            //console.log(data.message,this)
            this.magicLinkMsgFromServer = {
              message: data.message,
              key: data.message_key,
              replacements: data.message_replacements
            };
            this.step = 'link-sent';
          }
        })
        .catch(({ response }) => {
          // console.error(response);
          // console.dir(response);
          const message = get(response, 'data.message');
          this.emailErrorFromServer = true;
          this.emailErrorMsgFromServer = message;
        })
        .then(() => {
          this.emailLoading = false;
        });
    },
    submitPassword() {
      //console.log('submitPassword');
      if (this.$v.password.$invalid) {
        this.$v.password.$touch();
        return;
      }
      this.passwordLoading = true;
      this.step = 'loading';
      axios
        .post('login', {
          email: this.email,
          password: this.password
        })
        .then(({ data }) => {
          //console.dir(data)
          if (data.redirect) {
            this.step = 'loading';
            if (data.message) {
              window.sessionStorage.setItem('toast-message', data.message);
            }
            window.document.location = data.redirect;
          } else if (data.message) {
            this.magicLinkMsgFromServer = {
              message: data.message,
              key: data.message_key,
              replacements: data.message_replacements
            };
            this.step = 'link-sent';
          }
        })
        .catch(({ response }) => {
          // console.error(response);
          // console.dir(response);

          const message = get(response, 'data.message');
          this.passwordErrorFromServer = true;
          this.passwordErrorMsgFromServer = message;
          this.step = 'password';
        })
        .then(() => {
          this.passwordLoading = false;
        });
    },
    requestMagicLink(forgot) {
      //console.log('requestMagicLink');
      this.requestMagicLinkLoading = true;
      this.step = 'loading';
      axios
        .post('magic-auth', {
          email: this.email,
          forgot: forgot
        })
        .then(({ data }) => {
          if (data.message) {
            this.magicLinkMsgFromServer = {
              message: data.message,
              key: data.message_key,
              replacements: data.message_replacements
            };
            this.step = 'link-sent';
          }
        })
        .catch(() => {
          this.requestMagicLinkLoading = false;
        })
        .then(() => {
          this.requestMagicLinkLoading = false;
        });
    },
    resetEmailValidation() {
      this.$v.email.$reset();
      this.emailErrorFromServer = false;
      this.emailErrorMsgFromServer = null;
    },
    resetPasswordValidation() {
      this.$v.password.$reset();
      this.passwordErrorFromServer = false;
      this.passwordErrorMsgFromServer = null;
    },
    forgotEmail() {
      QDialog.create({
        title: this.$t('login.forgot_email.title'),
        message: this.$t('login.forgot_email.message'),
        buttons: [this.$t('global.cta.back')]
      });
    }
  },
  computed: {
    emailErrorMsg() {
      return this.emailErrorMsgFromServer || this.$t('login.email.validate');
    },
    passwordErrorMsg() {
      return (
        this.passwordErrorMsgFromServer ||
        this.$t('login.password.validate.empty')
      );
    },
    stepIsEmail() {
      return this.step === 'email';
    },
    stepIsPassword() {
      return this.step === 'password';
    },
    stepIsLoading() {
      return this.step === 'loading';
    },
    stepIsLinkSent() {
      return this.step === 'link-sent';
    },
    networkRequestInFlight() {
      return (
        this.emailLoading ||
        this.passwordLoading ||
        this.requestMagicLinkLoading
      );
    },
    browserOutdated() {
      const version = window.get_browser();
      if (version.name == 'Safari' && parseInt(version.version) < 11) {
        return true;
      }

      return false;
    }
  },
  mounted() {
    //console.log('Component login mounted.');
    //document.getElementsByClassName('q-search-input')[0].focus()
  }
};
</script>
