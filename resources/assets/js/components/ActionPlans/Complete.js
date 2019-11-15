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
  QRadio,
  QOptionGroup,
  QInnerLoading,
  QSpinner,
  QSelect,
  Toast,
} from 'quasar-framework';
import axios from 'axios';
import get from 'lodash/get';

import {required as requiredValidator} from 'vuelidate/lib/validators';
import {createServerValidation} from '../../utils/serverValidationMixin';

import withTemplate from './Complete.vue-template';
import style from './Complete.css';

const serverValidation = createServerValidation({
  parameters: [
    {
      name: 'successes',
      frontValidations: [
        {
          name: 'required',
          validator: requiredValidator,
          errorMsg: function () {
            return this.$t('action_plan.validation.goals.required');
          }
        }
      ]
    },
    {
      name: 'failures',
      frontValidations: [
        {
          name: 'required',
          validator: requiredValidator,
          errorMsg: function () {
            return this.$t('action_plan.validation.goals.required');
          }
        }
      ]
    },
    {
      name: 'next_focus',
      frontValidations: [
        {
          name: 'required',
          validator: requiredValidator,
          errorMsg: function () {
            return this.$t('action_plan.validation.goals.required');
          }
        }
      ]
    },
    {
      name: 'helpful',
      frontValidations: [
        {
          name: 'required',
          validator: requiredValidator,
          errorMsg: function () {
            return this.$t('action_plan.validation.goals.required');
          }
        }
      ]
    },
  ],
  initData() {
    return window.data;
  }
});

const momentumActionPlansComplete = {
  name: 'momentum-action-plans-complete',
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
    QRadio,
    QOptionGroup,
    QInnerLoading,
    QSpinner,
    QSelect,
    Toast,
  },
  data() {
    const data = {
      inFlight: false,
      config: window.frontend_config,
      style: style,
    };
    return data;
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
        .post(`/api/action-plans/${this.action_plan.id}/complete`, {
          successes: this.successes,
          failures: this.failures,
          next_focus: this.next_focus,
          helpful: this.helpful
        })
        .then(({data}) => {
          if (data.message) {
            window.sessionStorage.setItem('toast-message', data.message);
          }
          window.location = data.redirect;
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
      window.trackEvent('get_help', 'view', 'action_plan.complete');
      window.open(
        this.$t('action_plan.help_url'),
        'newwindow',
        'width=800,height=500'
      );
    },
    back() {
      window.location = '/action-plans';
    }
  },
  mounted() {
  }
};
export default withTemplate(momentumActionPlansComplete);
