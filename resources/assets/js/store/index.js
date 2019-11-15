/* global process */
import Vuex from 'vuex';
import actions from './actions';
import mutations from './mutations';
import getters from './getters';

export function createStore (Vue) {
  Vue.use(Vuex);
  return new Vuex.Store({
    strict: process.env.NODE_ENV !== 'production',
    state: {

    },
    actions,
    mutations,
    getters
  });
}
