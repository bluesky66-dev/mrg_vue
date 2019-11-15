/* eslint-env node */
/*eslint no-console: 0*/

const Rx = require('rxjs');

const counter = {};

const tableObservable = ({ airtableBase, table, cultures }) => {
  counter[table.name] = counter[table.name] || 0;
  return Rx.Observable.create(function(observer) {
    //console.log({ airtableBase, table, cultures });
    console.log('Retrieving table ' + table.name);
    airtableBase(table.name)
      .select({
        //maxRecords: 100,
        view: 'All'
      })
      .eachPage(
        (records, fetchNextPage) => {
          console.log(
            'Got ' + records.length + ' records from ' + table.name + ' page #',
            counter[table.name]
          );
          counter[table.name] = counter[table.name] + 1;
          // This function (`page`) will get called for each page of records.

          records.forEach(function(record) {
            const rowKey = record.get('key');

            cultures.forEach(local => {
              const value = record.get(local.columnName);
              if (rowKey) {
                const key = []
                  .concat(local.keyPrefix)
                  .concat(table.keyPrefix)
                  .concat(record.get('key').split('.'))
                  .join('.');
                observer.next({ key: key, string: value || key });
              }
            });
          });

          // To fetch the next page of records, call `fetchNextPage`.
          // If there are more records, `page` will get called again.
          // If there are no more records, `done` will get called.
          fetchNextPage();
        },
        err => {
          if (err) {
            console.error(err);
            observer.error(err);
          } else {
            observer.complete();
          }
        }
      );

    // Note that this is optional, you do not have to return this if you require no cleanup
    return () => {
      console.log('Finished ' + table.name);
    };
  });
};

module.exports = tableObservable;
