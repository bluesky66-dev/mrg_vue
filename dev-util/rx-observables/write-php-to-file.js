/* eslint-env node */
/*eslint no-console: 0*/

const Rx = require("rxjs");
const fs = require("fs-extra");
const phpUnparse = require("php-unparser");

const walkData = (node, depth = 0, acc = {}) => {
  if (typeof node === "object") {
    return Object.keys(node).map(key => {
      if (typeof node[key] === "object") {
        //console.log("  ".repeat(depth) + "found object @" + key);
        return {
          kind: "entry",
          key: {
            kind: "string",
            value: key,
            isDoubleQuote: false
          },
          value: {
            kind: "array",
            items: walkData(node[key], depth + 1, acc),
            shortForm: true
          }
        };
      } else if (typeof node[key] === "string") {
        //console.log("  ".repeat(depth) + "found string @" + key);
        //return " ".repeat(depth) + key + " => " + node[key];
        return {
          kind: "entry",
          key: {
            kind: "string",
            value: key
          },
          value: {
            kind: "string",
            value: node[key]
          }
        };
      }
    });
  } else {
    return node;
  }
};

const writePhpToFile = path => {
  return data => {
    const phpAst = {
      kind: "program",
      children: [
        {
          kind: "return",
          expr: {
            kind: "array",
            items: walkData(data, 0, {}),
            shortForm: true
          }
        }
      ],
      errors: []
    };

    //console.log(JSON.stringify(phpAst, null, "  "));
    //console.log(phpUnparse(phpAst));

    return Rx.Observable.create(observer => {
      const fileContent = phpUnparse(phpAst);
      fs.outputFile(path + ".php", fileContent, {}, err => {
        if (err) {
          console.log(err); // => null
          observer.error(err);
        } else {
          observer.complete();
        }
      });
      return () => {
        console.log("Closing " + path + ".php");
      };
    });
  };
};

module.exports = writePhpToFile;
