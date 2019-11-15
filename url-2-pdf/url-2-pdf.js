#!/usr/bin/env node

var program = require('commander');
const puppeteer = require('puppeteer');
const Duplex = require('stream').Duplex;

program
  .version('0.1.0')
  .option('-u, --url [url]', 'URL top open')
  .parse(process.argv);

if(!program.url){
  console.error('Usage: url-2-pdf --url [url]');
  process.exit(1);
}

puppeteer
  .launch({
    // executablePath:
    //   "/home/vagrant/node_modules/puppeteer/.local-chromium/linux-497674/chrome-linux/chrome"
  }).then(browser => {
    return browser.newPage().then(page => {
      //console.error(JSON.stringify(ctx.request.body, null, " "));
      //console.error("goto");
      return page
        .on('console', (...args) => {
          // console.error('PAGE LOG:', ...args)
        })
        .on('requestfailed', (request) => {
          console.error('REQUEST FAILED:', request.url);
        })
        .goto(program.url, {
          waitUntil: 'networkidle'
        })
      //.then(myDelay)
        .then(response => {
          return page.pdf({ printBackground: true, format: 'Letter' }).then(buffer => {
            const stream = new Duplex();
            stream.pipe(process.stdout);
            stream.push(buffer);
            stream.push(null);
            return page.close();
          });
        });
    }).catch(()=>{
      process.exit(1);
    });
  })
  .catch(()=>{
    process.exit(1);
  })
  .then(()=>{
    process.exit(0);
  });
