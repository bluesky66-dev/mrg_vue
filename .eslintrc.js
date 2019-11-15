module.exports = {
  env: {
    es6: true, // We are writing ES6 code
    browser: true, // for the browser
    commonjs: true // and use require() for stylesheets
  },
  extends: [
  "eslint:recommended",
    "plugin:vue/base" // or 'plugin:vue/base'
    ],
    parserOptions: {
      parser: "babel-eslint",
      ecmaVersion: 2017,
      sourceType: "module",
      ecmaFeatures: {
        jsx: false
      }
    },
    rules: {
      "no-console": 0,
      indent: ["warn", 2],
      "no-empty": 0,
      "brace-style": [1, "1tbs", { allowSingleLine: true }],
      semi: 1,
      "no-multiple-empty-lines": [1, { max: 2 }],
      quotes: ["warn", "single"],
      "vue/max-attributes-per-line": ["warn",{
        "singleline": 3,
        "multiline": {
          max: 1,
          allowFirstLine: true
        }
      }],
      "vue/html-quotes": [1,"double"],
      'vue/mustache-interpolation-spacing': [2, 'always']
    // override/add rules' settings here
    //'vue/valid-v-if': 'error'
  }
};
