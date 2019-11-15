/* eslint-env node */

/*
Pig Latin interpreter
by Ben Buckman, July 20 2012
Rules:
- All words beginning with a consonant have their first letter moved to the end of word followed by 'ay'.
Example: Hello -> Ellohay
- All words beginning with a vowel have their first letter moved to the end moved to the word followed by 'hay'.
Example: Another -> Notherahay
- All punctuation must be kept in place for example:
Bacon is awesome! Sometimes, in the morning, I'll have bacon on toast for breakfast.
Aconbay sihay wesomeahay! Ometimessay, nihay hetay orningmay, 'llihay avehay aconbay nohay oasttay orfay reakfastbay.
*/

var pigLatin = (module.exports = function(english) {
  // make sure it's a string
  english = "" + english;

  // (only handle sentences. if paragraphs are expected, can extend this to split on non-space whitespace).
  var splitWords = english.split(" "),
    pigLatin = "",
    splitWordsPigLatin = [];

  // translate each word
  splitWords.forEach(function(word) {
    // starts w/ a consonant or vowel?
    // word can have an apostrophe in the middle. all other punctuation (comma etc) should be between words.
    // quotation marks or other punctuation around the word are captured in first block.
    // i flag indicates case-insensitive
    var consonantStartPattern = /^[^a-z]*(([bcdfghjklmnpqrstvwxyz]){1}([a-z']*))/i,
      consonantMatches = word.match(consonantStartPattern),
      vowelStartPattern = /^[^a-z]*(([aeiou]){1}([a-z']*))/i,
      vowelMatches = word.match(vowelStartPattern),
      punctuationStartPattern = /^[^a-z]*(([:]){1}([a-z']*))/i,
      punctuationMatches = word.match(punctuationStartPattern),
      origWord,
      rebuiltWord = null;

    // helper for both scenarios:
    // want to keep case consistent: if 1st letter is uppercase now, make new 1st letter uppercase.
    // (matches array is a reference)
    var consistentCases = function(matches) {
      if (/[A-Z]/.test(matches[2]) && matches[3].length > 0) {
        // will be moved to the middle. is the rest of the word uppercase? (maybe it's an acronym)
        if (matches[3] !== matches[3].toUpperCase())
          matches[2] = matches[2].toLowerCase();

        matches[3] = matches[3].replace(
          /^[a-z]{1}/,
          matches[3][0].toUpperCase()
        );
      }
    };

    if (punctuationMatches !== null) {
      rebuiltWord = word;
    } else if (consonantMatches !== null) {
      /*
      for '"morning"' [w/quotes], matches will hold:
        0: '"morning',
        1: 'morning',
        2: 'm',
        3: 'orning',
        4: index: 0,
        5: input: '"morning",'
      */
      // console.log('consonsant matches:', consonantMatches);

      consistentCases(consonantMatches);
      origWord = consonantMatches[1];
      rebuiltWord = "" + consonantMatches[3] + consonantMatches[2] + "ay"; // morning => orningmay
    } else if (vowelMatches !== null) {
      // console.log('vowel matches:', vowelMatches);
      // [same match array structure as before]

      consistentCases(vowelMatches);

      origWord = vowelMatches[1];
      rebuiltWord = "" + vowelMatches[3] + vowelMatches[2] + "hay"; // Another => Notherahay
    }

    if (rebuiltWord) {
      word = word.replace(origWord, rebuiltWord);
    }

    // (if original sentence had multiple spaces between words, this will return those spaces & preserve)
    splitWordsPigLatin.push(word);
  });

  pigLatin = splitWordsPigLatin.join(" ");

  return pigLatin;
});

// test helper
var assertTranslation = function(input, expectedOut) {
  var assert = require("assert"),
    actual = pigLatin(input);

  console.log("Input:\t\t" + input);
  console.log("Expected:\t" + expectedOut);
  console.log("Actual:\t\t" + actual);

  try {
    assert.equal(actual, expectedOut);
    console.log("Correct!\n");
    return true;
  } catch (error) {
    console.error("Incorrect!\n");
    return false;
  }
};

// assertTranslation(
//   "Bacon is awesome! Sometimes, in the morning, I'll have bacon on toast for breakfast.",
//   "Aconbay sihay wesomeahay! Ometimessay, nihay hetay orningmay, 'llihay avehay aconbay nohay oasttay orfay reakfastbay."
// );

// // test quotation marks around a word
// assertTranslation(
//   'Sometimes, in the "morning", I eat bacon.',
//   'Ometimessay, nihay hetay "orningmay", Ihay atehay aconbay.'
// );

// // test extra whitespace
// assertTranslation(
//   "Sometimes,    in the morning",
//   "Ometimessay,    nihay hetay orningmay"
// );
