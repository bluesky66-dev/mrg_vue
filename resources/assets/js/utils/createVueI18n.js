import VueI18n from 'vue-i18n';
import cloneDeepWith from 'lodash/cloneDeepWith';

export default Vue => {
  Vue.use(VueI18n);

  const messages = {};
  let vueI18n = {};

  const doNotMangleKeysMatching = [/(^|_)url(_|$)/];


  if (window) {
    if (window.Lang) {
      for (var cultureFile in window.Lang.messages) {
        const cultureId = cultureFile.split('.')[0];
        const file = cultureFile.split('.')[1];

        messages[cultureId] = messages[cultureId] || {};
        messages[cultureId][file] = cloneDeepWith(
          window.Lang.messages[cultureFile],
          (val,key) => {
            
            if(
              typeof val === 'string' && 
              !doNotMangleKeysMatching.map(m=>m.test(key)).some(x=>x) 
            ){
              return val.replace(/(:([a-zA-Z0-9_-]*))/g, '{$2}');
            }
          });

      }
      // Create VueI18n instance with options
      vueI18n = new VueI18n({
        locale: window.Lang.getLocale(), // set locale
        messages // set locale messages
      });
      window.vueI18n = vueI18n;
    }
  }
  return vueI18n;
};
