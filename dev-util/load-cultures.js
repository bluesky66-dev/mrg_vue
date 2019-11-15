/* eslint-env node */
/*eslint no-console: 0*/

const Rx = require('rxjs');

const set = require('lodash/set');
const identity = require('lodash/identity');
const pigLatin = require('./pig-latin');
const pirateSpeak = require('pirate-speak').translate;

const momentumCulturesAirtable = require('./rx-observables/momentum-cultures-airtable');
//const writeJsonToFile = require('./rx-observables/write-json-to-file');
const writePhpToFile = require('./rx-observables/write-php-to-file');

//console.log();

const pathBase = 'resources/lang/';

const applications = [
  { keySelector: 'global.', fileName: 'global' },
  { keySelector: 'global.auth.', fileName: 'auth' },
  { keySelector: 'global.validation.', fileName: 'validation' },
  { keySelector: 'global.pagination.', fileName: 'pagination' },
  { keySelector: 'global.passwords.', fileName: 'passwords' },
  { keySelector: 'login.', fileName: 'login' },
  { keySelector: 'profile.', fileName: 'profile' },
  { keySelector: 'dashboard.', fileName: 'dashboard' },
  { keySelector: 'action_plan.', fileName: 'action_plan' },
  { keySelector: 'pulse_survey.', fileName: 'pulse_survey' },
  { keySelector: 'journal.', fileName: 'journal' },
  { keySelector: 'behavior.', fileName: 'behavior' }
];

const formats = [
  // { label: "JSON", writer: writeJsonToFile },
  { label: 'PHP', writer: writePhpToFile }
];

const applicationFormats = applications.reduce((acc, application) => {
  return acc.concat(
    formats.map(format => {
      return {
        keySelector: application.keySelector,
        fileName: application.fileName,
        writer: format.writer
      };
    })
  );
}, []);

const cultureDirectories = [
  { keySelector: 'en_GB.', path: 'en_GB/' },
  {
    keySelector: 'en_US.',
    path: 'en_PL/',
    valueFilter: pigLatin
  },
  {
    keySelector: 'en_US.',
    path: 'en_PS/',
    valueFilter: pirateSpeak
  },
  // { keySelector: 'es_ES.', path: 'es_ES/' },
  // { keySelector: 'pt_BR.', path: 'pt_BR/' },
  // { keySelector: 'de_DE.', path: 'de_DE/' },
  // { keySelector: 'da_DK.', path: 'da_DK/' },
  // { keySelector: 'nl_NL.', path: 'nl_NL/' },
  // { keySelector: 'it_IT.', path: 'it_IT/' },
  // { keySelector: 'fr_FR.', path: 'fr_FR/' },
  // { keySelector: 'pl_PL.', path: 'pl_PL/' },
  // { keySelector: 'zh_CN.', path: 'zh_CN/' },
  { keySelector: 'en_US.', path: 'en_US/' }
];

const cultureFiles = cultureDirectories.reduce((acc, local) => {
  return acc.concat(
    applicationFormats.map(application => {
      return {
        keySelector: local.keySelector + application.keySelector,
        path: pathBase + local.path + application.fileName,
        writer: application.writer,
        valueFilter: local.valueFilter ? local.valueFilter : identity
      };
    })
  );
}, []);

//console.log(cultureFiles);

const airtableBaseId = 'app1pHWoqHljyR2wk';
const airtableKey = 'keyzkgXmxF1lyXhCG';

const tables = [
  { name: 'Global', keyPrefix: ['global'] },
  { name: 'Login', keyPrefix: ['login'] },
  { name: 'Profile', keyPrefix: ['profile'] },
  { name: 'Dashboard', keyPrefix: ['dashboard'] },
  { name: 'Action Plan', keyPrefix: ['action_plan'] },
  { name: 'Pulse Survey', keyPrefix: ['pulse_survey'] },
  { name: 'Journal', keyPrefix: ['journal'] },
  { name: 'Behavior', keyPrefix: ['behavior'] }
];

const cultures = [
  { columnName: 'sv_SE', keyPrefix: ['sv_SE'] },
  { columnName: 'en_GB', keyPrefix: ['en_GB'] },
  { columnName: 'en_US', keyPrefix: ['en_US'] },
  { columnName: 'es_ES', keyPrefix: ['es_ES'] },
  { columnName: 'pt_BR', keyPrefix: ['pt_BR'] },
  { columnName: 'de_DE', keyPrefix: ['de_DE'] },
  { columnName: 'da_DK', keyPrefix: ['da_DK'] },
  { columnName: 'nl_NL', keyPrefix: ['nl_NL'] },
  { columnName: 'it_IT', keyPrefix: ['it_IT'] },
  { columnName: 'fr_FR', keyPrefix: ['fr_FR'] },
  { columnName: 'pl_PL', keyPrefix: ['pl_PL'] },
  { columnName: 'zh_CN', keyPrefix: ['zh_CN'] }
];

const Airtable = require('airtable');
Airtable.configure({
  endpointUrl: 'https://api.airtable.com',
  apiKey: airtableKey
});

const airtableBase = Airtable.base(airtableBaseId);

const tableObservables = Rx.Observable
  .from(tables)
  .mergeMap(table =>
    momentumCulturesAirtable({
      airtableBase: airtableBase,
      table: table,
      cultures: cultures
    })
  )
  .publish();
//.refCount();

Rx.Observable
  .forkJoin(
    cultureFiles.map(cultureFile => {
      const fileWriter = cultureFile.writer(cultureFile.path);
      //console.log("fileWriter", fileWriter);
      const valueFilter = cultureFile.valueFilter;
      return tableObservables
        .filter(row => row.key.indexOf(cultureFile.keySelector) === 0)
        .reduce((acc, row) => {
          return (acc = set(
            acc,
            row.key.replace(cultureFile.keySelector, ''),
            valueFilter(row.string)
          ));
        }, {})
        .mergeMap(fileWriter);
    })
  )
  .subscribe(
    function(/*cultureFiles*/) {
      // cultureFiles.forEach(cultureFile => {
      //   //console.log(JSON.stringify(cultureFile, null, " "));
      // });
    },
    function(err) {
      console.log('Error: ', err);
    },
    function() {
      console.log('load-cultures Completed');
    }
  );

tableObservables.connect();
