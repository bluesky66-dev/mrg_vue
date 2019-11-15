import get from 'lodash/get';
import set from 'lodash/set';
import setWith from 'lodash/setWith';
import isArray from 'lodash/isArray';
import isString from 'lodash/isString';


//import { required, minLength } from 'vuelidate/lib/validators';
import { validationMixin, withParams } from 'vuelidate';


const configKeyName = 'serverValidation';

const defaultConfig = {
  parameters: [],
  initData: () => ({})
};


// const validationMixinBeforeCreate = validationMixin.beforeCreate;
// validationMixin.beforeCreate = function() {
//   const options = this.$options[configKeyName];
//   if(options){
//     //const parameters = options.parameters || [];
//     //console.log('beforeCreate intercept')

//     validationMixinBeforeCreate.call(this);

//     console.log('beforeCreate intercept',this);
//   }

// };

// const validationMixinData = validationMixin.data;
// validationMixin.data = function() {
//   const result = validationMixinData.call(this);
//   if(this._vuelidate){
//     console.log('_vuelidate',this._vuelidate)
//   }
//   return result;
// };


export function createServerValidation(config = {}) {

  const touchMap = new WeakMap();

  const { parameters, initData } = {
    ...defaultConfig,
    ...config
  };

  const serverValidationMixin = {
    data() {
      const data = {
        ...parameters.reduce((acc, { name }) => {
          acc[name] = null;
          return acc;
        }, {}),
        errors: {
          ...parameters.reduce((acc, { name }) => {
            acc[name] = [];
            return acc;
          }, {})
        },
        ...initData()
      };
      //console.log('mixin data', data);
      return data;
    },
    methods: {
      ...parameters.reduce((acc, { name }) => {
        acc[name + 'DelayedTouch'] = function() {
          this.$v[name].$reset();
          if (touchMap.has(this.$v[name])) {
            clearTimeout(touchMap.get(this.$v[name]));
          }
          touchMap.set(this.$v[name], setTimeout(this.$v[name].$touch, 1000));
        };
        return acc;
      }, {}),
      allDelayTouch() {
        this.$v.$reset();
        if (touchMap.has(this.$v)) {
          clearTimeout(touchMap.get(this.$v));
        }
        touchMap.set(this.$v, setTimeout(this.$v.$touch, 1000));
      },
      resetValidation() {
        //console.log('resetValidation')
        this.errors = {
          ...parameters.reduce((acc, { name }) => {
            acc[name] = [];
            return acc;
          }, {})
        };
        this.$v.$reset();
      }
    },
    validations: {
      ...parameters.reduce((acc, { name, frontValidations = [] }) => {
        acc[name] = {
          ...frontValidations.reduce((acc, { name, validator }) => {
            acc[name] = validator;
            return acc;
          }, {}),
          serverError: function() {
            const result = !(
              isArray(this.errors[name]) && isString(this.errors[name][0])
            );
            return result;
          }
        };
        return acc;
      }, {})
    },
    computed: {
      ...parameters.reduce((acc, { name, frontValidations = [] }) => {
        const msgName = name + 'ErrorMsg';
        acc[msgName] = function() {
          //console.log(msgName + ' running');
          const errorFromServer = get(this, ('errors.' + name).split('.'), []).find(
            isString
          );
          //console.log('errorFromServer',errorFromServer);
          // console.log(
          //   `this.$v[${name}].$flattenParams`,
          //   this.$v[name].$flattenParams()
          // );
          const frontValidationErrors = this.$v[name]
            .$flattenParams()
            .map(param => {
              return !this.$v[name][param.name] ?
                frontValidations.find(
                  validation =>
                    validation.errorMsg &&
              typeof validation.errorMsg === 'function' &&
              validation.name === param.name
                ) :
                null;
            })
            .filter(x => x)
            .map(
              validation =>
                typeof validation.errorMsg === 'function' &&
            validation.errorMsg.apply(this)
            )
            .find(x => x);
          //console.log('frontValidationErrors', frontValidationErrors);
          return [frontValidationErrors, errorFromServer].find(x => x);
        };

        // const hasName = name+'hasError';
        // acc[hasName] = function(){
        //   //console.log(hasName + ' running');
        //   const errors = get(this,'errors.' + name,[]);
        //   //console.log('error',errors);
        //   return errors.length > 0;
        // };

        return acc;
      }, {})
    }
  };
  return serverValidationMixin;
}

const createLocalDelayedTouch = ({$vm,$vPath,delay}) =>{
  //console.log('createLocalDelayedTouch')
  //console.log('$vm',$vm)
  //console.log('$vPath',$vPath)

  let delayedTouchTimeout;

  const $vLocal = get($vm,$vPath.split('.'));
  //console.log('createLocalDelayedTouch',$vPath,$vLocal);

  const $delayedTouch = function() {
    const $vLocal = get($vm,$vPath.split('.'));
    //console.log("$vLocal['$delayedTouch']",$vLocal['$delayedTouch'])

    $vLocal.$reset();
    if($vLocal['$delayedTouch']){
      $vLocal['$delayedTouch'].cancel();
    }
    delayedTouchTimeout = setTimeout(function () {
      const $vLocal = get($vm,$vPath.split('.'));
      //console.log('delayedTouch triggered for '+$vPath);
      $vLocal.$touch();
    }, delay);
    $vm._touchMap[$vPath] = delayedTouchTimeout;
  };

  const $delayedTouchCancel = function() {
    //console.log('$delayedTouchCancel',$vPath);
    clearTimeout(delayedTouchTimeout);
    delete $vm._touchMap[$vPath];
  };

  if($vLocal['$delayedTouch']){
    if($vLocal['$delayedTouch'].cancel){
      $vLocal['$delayedTouch'].cancel();
      delete $vLocal['$delayedTouch'].cancel;
    }
    delete  $vLocal['$delayedTouch'];
  }

  $vLocal['$delayedTouch'] = $delayedTouch.bind(this);
  $vLocal['$delayedTouch'].cancel = $delayedTouchCancel.bind(this);

  const $vParentPath = $vPath.split('.').slice(0,-1).join('.');
  if($vParentPath.length > 0){
    const $vParent = get($vm,$vParentPath.split('.'));
        
    if(!$vParent['$delayedTouch']){
      //console.log('found $vParentPath', $vParent);
      createLocalDelayedTouch({
        $vm:$vm,
        $vPath:$vParentPath,
        delay:delay
      });
    }
  }
};

const getParameters = function(options){
  if(typeof options === 'function'){
    const result = options.call(this);
    return result.parameters || [];
  }
  if(typeof options === 'object'){
    return options.parameters;
  }
  return [];
};


const serverValidationMixin = {
  mixins: [{
    ...validationMixin
  }],
  created: function() {
    
    this._touchMap = {};

    this._errorWatchers = this._errorWatchers ||  [];

    this._errorWatchers.forEach((errorWatcher) => {
      errorWatcher();
    });

    const options = this.$options[configKeyName];
    const parameters = getParameters.call(this,options);


    //console.log('serverValidation created',parameters,this);


    parameters.forEach((parameter) => {
      if (!parameter['disabled'] && parameter['name']) {

        const $vPath = '$v.'+parameter['name'];

        createLocalDelayedTouch({
          $vm:this,
          $vPath:$vPath,
          delay:1000
        });

        const $errorPath = $vPath+'.$error';

        //const $delayedTouchPath =$vPath+'.$delayedTouch';

        const errorMsgPath = 'errorMsg.'+parameter['name'];
        //console.log(`new $watch ${$errorPath}`)
        const $unwach = this.$watch($errorPath, function (newVal) {
          //console.log(`$watch ${$errorPath}`,newVal)

          if(newVal === true){

            const validation = get(this,$vPath.split('.'));
            const params = get(this,($vPath+'.$flattenParams').split('.'));
            
            
            const firstParam = params().find((param)=>{
              return !validation[param.name];
            });
            
            //console.log('firstParam',firstParam);
            const message = get(firstParam,('params.errorMsg').split('.'),null);
            
            set(this,errorMsgPath.split('.'),message);
            //console.log('message ', errorMsgPath ,message);
          } else {  
            set(this,errorMsgPath.split('.'),null);
          }
          
        },{});
        this._errorWatchers.push($unwach);
      }
    });
  },
  beforeDestroy () {
    this._errorWatchers.forEach((errorWatcher) => {
      errorWatcher();
    });
    for(const $vPath in this._touchMap){
      clearTimeout(this._touchMap[$vPath]);
      delete this._touchMap[$vPath];
    }
  },
  data() {
    let serverErrors = {};
    let errorMsg = {};

    const options = this.$options[configKeyName];
    
    const parameters = getParameters.call(this,options);

    parameters.forEach((parameter) => {
      const name = parameter['name'];
      if (name) {
        set(serverErrors, name.split('.'), null);
        set(errorMsg, name.split('.'), null);
      }
    });

    return {
      serverErrors: serverErrors,
      errorMsg: errorMsg
    };
  },
  methods: {
    resetServerErrors() {
      const options = this.$options[configKeyName];
      const parameters = getParameters.call(this,options);

      parameters.forEach((parameter) => {
        if (parameter['name']) {
          set(this.serverErrors, parameter['name'].split('.'), null);
        }
      });
      this.$v.$reset();
    }
  },
  validations() {
    let validations = {};
    const options = this.$options[configKeyName];
    const parameters = getParameters.call(this,options);

    //console.log('validations', parameters);

    parameters.forEach((parameter) => {
      if (!parameter['disabled'] && parameter['name']) {

        const frontValidations = typeof parameter.frontValidations === 'function' 
          ? parameter.frontValidations.call(this)
          : (parameter.frontValidations || []);

        frontValidations.forEach((frontValidation) => {
          //console.log('frontValidation', frontValidation);
          if (!parameter['disabled'] && 
            parameter['name'] && 
            frontValidation['name'] && 
            frontValidation['validator']) {

            const path = parameter['name'] + '.' + frontValidation['name'];
            if (frontValidation['errorMsg']) {
              const validator = withParams({
                errorMsg: frontValidation['errorMsg'].apply(this)
              }, frontValidation['validator']);

              setWith(validations, path.split('.'), validator,Object);
            } else {
              setWith(validations, path.split('.'), frontValidation['validator'],Object);
            }
          }
        });
        //Add server side validation
        const serverValidation = withParams({
          errorMsg: get(this, ('serverErrors.' + parameter['name']).split('.'))
        }, function() {
          const value = get(this, ('serverErrors.' + parameter['name']).split('.'), null);
          const isErrorString = !(typeof value === 'string');
          return isErrorString;
        });

        set(validations, (parameter['name'] + '.serverError').split('.'), serverValidation, );

        //console.log('parameter', parameter);
      }
    });
    //console.log('validations', validations);
    return validations;
  },
  filters:{
    errorsFromMyValidators: function(value) {
      const myValidators = value.$flattenParams()
        .filter(p=>p.path.length === 0)
        .map(p=>p.name);
      const result = myValidators.reduce((acc,v)=>acc && value[v],true);
      return (!result && value.$dirty);
    },
    firstError: function(value) {
      const errors =  (value.$flattenParams() || []).filter(p=> {
        return (
          p.path &&
          p.path.length === 0 && 
          p.params && 
          p.params.errorMsg && 
          typeof p.params.errorMsg === 'string' &&
          p.params.errorMsg.length > 0
        );
      } );
      const error = errors.find(error=>value[error.name] === false);
      return get(error,('params.errorMsg').split('.'),null);
    }
  }
};

export default serverValidationMixin;
