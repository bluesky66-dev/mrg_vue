import axios from 'axios';
import get from 'lodash/get';
import set from 'lodash/set';
import debounce from 'lodash/debounce';
import throttle from 'lodash/throttle';
import merge from 'lodash/merge';


export default (options) => {

  let lastRequest = null;
  let unsentRequestWaiting = false;

  const defaultOptions = {
    applicationKey: 'default',
    dataKeys: [],
    disabled: false
  };

  const getOptions = (vm) => {
    return {
      ...defaultOptions,
      ...options || {},
      ...vm.$options.persistentApplicationState || {},
    };
  };


  return {
    created() {
      //console.log('persistentApplicationStateMixin created',this);

      const saveApplicationState = function() {
        //console.log('debouncedSaveApplicationState',this);
        this.saveApplicationState();
      };

      const debouncedSaveApplicationState = debounce(
        saveApplicationState.bind(this),
        2000, {
          'leading': false,
          'trailing': true
        });
      this['debouncedSaveApplicationState'] = debouncedSaveApplicationState;

      const throttledSaveApplicationState = throttle(
        saveApplicationState.bind(this),
        2000, {
          'leading': false,
          'trailing': true
        });
      this['throttledSaveApplicationState'] = throttledSaveApplicationState;
    },
    data() {
      const options = getOptions(this);
      if (options.disabled === true) {
        return {};
      }
      return {
        savingApplicationState: false
      };
    },
    methods: {
      getApplicationStateFromPage() {
        const applicationStateString = get(window, 'data.application_state.data', '{}');
        const applicationState = JSON.parse(applicationStateString);
        return applicationState;
      },
      loadApplicationState() {
        const options = getOptions(this);
        if (options.disabled === true) {
          return;
        }
        const appStateFromPage = this.getApplicationStateFromPage();
        //console.log('appStateFromPage',appStateFromPage)
        merge(this, appStateFromPage);
      },
      saveApplicationState() {
        const options = getOptions(this);
        if (options.disabled === true) {
          return;
        }
        if (lastRequest && unsentRequestWaiting) {
          return lastRequest;
        }
        if (lastRequest) {
          unsentRequestWaiting = true;
          return lastRequest.then(() => {
            this.saveApplicationState();
          });
        }

        const state = options.dataKeys.reduce((result, dataKey) => {
          set(result, dataKey, get(this, dataKey, null));
          return result;
        }, {});

        const initialApplicationStateString = get(window, 'application_state.data', '{}');
        const initialApplicationState = JSON.parse(initialApplicationStateString);

        this.savingApplicationState = true;
        unsentRequestWaiting = false;
        lastRequest = axios
          .post('/api/application-states', {
            'application_key': options.applicationKey,
            'data': JSON.stringify({
              ...initialApplicationState,
              ...state
            })
          })
          .then(() => {
            this.$emit('applicationStateSavedSuccessful', new Date());
          }) 
          .catch(() => {
            this.$emit('applicationStateSavedFailed', new Date());
          })
          .then(() => {
            this.savingApplicationState = false;
            lastRequest = null;
          });

        return lastRequest;
      },
      clearApplicationState(applicationKey) {
        //console.log('clearApplicationState', this);
        const options = getOptions(this);

        return axios.post('/api/application-states', {
          'application_key': applicationKey || options.applicationKey,
          'data': JSON.stringify({})
        });
      }
    }
  };
};
