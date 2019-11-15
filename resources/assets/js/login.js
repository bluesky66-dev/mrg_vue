/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';

import Vue from 'vue';
import VueAnalytics from 'vue-analytics';

import queryString from 'query-string';
import { Toast } from 'quasar-framework';
import createVueI18n from './utils/createVueI18n';
import createQuasar from './utils/createQuasar';
import createVuelidate from './utils/createVuelidate';

import CulturePicker from './components/CulturePicker.vue';
import Login from './components/Login.vue';


Vue.use(VueAnalytics, {
  id: 'UA-107242197-1'
});

createVuelidate(Vue);
createQuasar(Vue);
const i18n = createVueI18n(Vue);

if (window) {
  window.Vue = Vue;
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

window.momentumLogin = new Vue({
  i18n: i18n,
  mounted() {
    const { message } = (queryString.parse(location.search) || {});
    message && Toast.create(message);
  },
  el: '#momentum-login',
  components: {
    'momentum-culture-picker': CulturePicker,
    'momentum-login': Login
  }
});

window.momentumLogin.$ga.page(window.location.pathname);
