/* global  */
import Quasar from 'quasar-framework';

//require(`quasar-framework/dist/quasar.${__THEME}.css`);


// Uncomment the following lines if you need IE11/Edge support
require('quasar-framework/dist/quasar.ie');
// require(`quasar-framework/dist/quasar.ie.${__THEME}.css`);

// if (__THEME === 'mat') {
//   require('quasar-extras/roboto-font');
// }
// import 'quasar-extras/material-icons';

export default Vue => {
  Vue.use(Quasar);
};
