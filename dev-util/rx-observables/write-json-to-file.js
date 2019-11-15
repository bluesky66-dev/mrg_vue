/* eslint-env node */
/*eslint no-console: 0*/

const Rx = require("rxjs");
const fs = require("fs-extra");

const writeJsonToFile = path => {
  return data => {
    return Rx.Observable.create(observer => {
      //console.dir(data);
      fs.outputJson(path + ".json", data, { spaces: "  " }, err => {
        if (err) {
          console.log(err); // => null
          observer.error(err);
        } else {
          observer.complete();
        }
      });
      return () => {
        console.log("Closing " + path + ".json");
      };
    });
  };
};

module.exports = writeJsonToFile;
