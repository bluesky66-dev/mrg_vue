/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';

import Vue from 'vue';
import VueAnalytics from 'vue-analytics';

import { Toast } from 'quasar-framework';

//import VueRouter from 'vue-router';
import createVueI18n from './utils/createVueI18n';
import createQuasar from './utils/createQuasar';
import createVuelidate from './utils/createVuelidate';

// import { createStore } from './store';


import ActionPlansIndex from './components/ActionPlans/Index.vue';
import ActionPlansCreate from './components/ActionPlans/Create';
import ActionPlansComplete from './components/ActionPlans/Complete';
import ActionPlansShare from './components/ActionPlans/Share';
import ActionPlansSharePreview from './components/ActionPlans/SharePreview.vue';
import ActionPlansResults from './components/ActionPlans/Results.vue';
import Dashboard from './components/Dashboard/Dashboard.vue';
import Journal from './components/Journal.vue';
import ProfileIndex from './components/Profile/ProfileIndex.vue';
import PulseSurveys from './components/PulseSurveys.vue';
import PulseSurveysCreate from './components/PulseSurveysCreate.vue';
import VueFuse from 'vue-fuse';
import entries from 'object.entries';

import axios from 'axios';

import { ClientTable } from 'vue-tables-2';

Vue.use(ClientTable);
Vue.use(VueFuse);

Vue.use(VueAnalytics, {
  id: 'UA-107242197-1'
});

// Vue.use(VueRouter)
// const router = new VueRouter({
//   routes:[
//     { path: '/foo', component: Dashboard },
//   ]
// });

createVuelidate(Vue);
//createStore(Vue);
createQuasar(Vue);
const i18n = createVueI18n(Vue);

if (window) {
  window.Vue = Vue;
}

if (window) {
  window.Toast = Toast;
}

if (!Object.entries) {
  entries.shim();
}

window.get_browser = function() {
  var ua=navigator.userAgent,tem,M=ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
  if(/trident/i.test(M[1])){
    tem=/\brv[ :]+(\d+)/g.exec(ua) || [];
    return {name:'IE',version:(tem[1]||'')};
  }
  if(M[1]==='Chrome'){
    tem=ua.match(/\bOPR|Edge\/(\d+)/);
    if(tem!=null)   {return {name:'Opera', version:tem[1]};}
  }
  M=M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
  if((tem=ua.match(/version\/(\d+)/i))!=null) {M.splice(1,1,tem[1]);}
  return {
    name: M[0],
    version: M[1]
  };
};

window.trackEvent = function(category, action = null, label = null, value = null, data = null) {
  axios.post('/api/events', {
    category: category,
    action: action,
    label: label,
    value: value,
    data: data,
  });
};

window.momentumApp = new Vue({
  //  router:router,
  mounted() {
    // Call this only AFTER Vue.use(Quasar):
    Toast.setDefaults({
      // props from above
      timeout: 3000,
      bgColor: '#027be3',
    });
    const message = window.sessionStorage.getItem('toast-message') || null;
    window.sessionStorage.removeItem('toast-message');
    message && Toast.create(message);
  },
  i18n: i18n,
  components: {
    'momentum-action-plans-index': ActionPlansIndex,
    'momentum-action-plans-edit': ActionPlansCreate,
    'momentum-action-plans-complete': ActionPlansComplete,
    'momentum-action-plans-share': ActionPlansShare,
    'momentum-action-plans-share-preview': ActionPlansSharePreview,
    'momentum-action-plans-results': ActionPlansResults,
    'momentum-dashboard': Dashboard,
    'momentum-journal': Journal,
    'momentum-profile-index': ProfileIndex,
    'momentum-pulse-surveys': PulseSurveys,
    'momentum-pulse-surveys-create': PulseSurveysCreate
  }
});
window.momentumApp.$ga.page(window.location.pathname);
window.momentumApp.$mount('#momentum-app');
