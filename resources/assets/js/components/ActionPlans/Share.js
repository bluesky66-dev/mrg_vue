import {
  QCard,
  QField,
  QBtn,
  QInnerLoading,
  QSpinner,
  QSelect,
  QStepper,
  QStep,
  QModal,
} from 'quasar-framework';
import axios from 'axios';
import get from 'lodash/get';

import {required as requiredValidator} from 'vuelidate/lib/validators';
import EditObserver from '../ObserverCrud/EditObserver.vue';

import {createServerValidation} from '../../utils/serverValidationMixin';

import withTemplate from './Share.vue-template';
import style from './Share.css';

import SharePreview from './SharePreview.vue';

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

const momentumActionPlansShare = {
  name: 'momentum-action-plans-share',
  mixins: [serverValidation],
  components: {
    QCard,
    QField,
    QBtn,
    QStepper,
    QStep,
    QInnerLoading,
    QSpinner,
    QSelect,
    QModal,
    'share-preview': SharePreview,
    EditObserver
  },
  data() {
    const data = {
      inFlight: false,
      config: window.frontend_config,
      observers: window.data.observers,
      style: style,
      observers_selected: [],
      observer_types:window.data.observer_types,
      cultures:window.cultures,
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
        .post(`/api/action-plans/${this.action_plan.id}/share`, {
          observers: this.observers_selected,
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
      window.open(
        this.$t('action_plan.help_url'),
        'newwindow',
        'width=800,height=500'
      );
    },
    back() {
      window.location = '/action-plans';
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
      //console.log('editedObserver',{action,payload});
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
  }
};

export default withTemplate(momentumActionPlansShare);
