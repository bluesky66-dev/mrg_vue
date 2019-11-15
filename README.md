# Momentum

A project by [MRG](https://www.mrg.com/).


## Getting up and Running

1. Navigate to this project's root in your terminal
2. Run `vagrant up`
3. Add the following entry in your hosts file: `192.168.33.10 mrg.dev`
4. Access the app by accessing the site at `http://mrg.dev`, or `https://mrg.dev`


## Getting Data
You can refresh the database at any time by running:
```
    php artisan migrate:fresh --seed
```

You can generate fake data by running:
```
    php artisan db:seed --class=FakeDataSeeder
```

This will create four test users with the following credentials:

|         Email          | Password | Culture |
|:----------------------:|:--------:|:-------:|
|  johndoe@example.com   |  secret  |  en_US  |
|  pirate@example.com    |  secret  |  en_PS  |
| piglatin@example.com   |  secret  |  en_PL  |
| nopassword@example.com |          |  en_US  |


## Subdomains
Momentum works with multiple subdomains, for example, the *mobile* subdomain will display the mobile layout/version. Setup the following `.env` variables to configure the these options (look at file `config/app.php` for extended description):
```bash
APP_DOMAIN=http://www.momentum.com
APP_SUBDOMAIN=http://{subdomain}.momentum.com
MOBILE_SUBDOMAIN=m
```

On production server, make sure to assign a wildcard subdomain so the laravel can handle these with no issues.

### Subdomains on development environment
To make subdomains work on development environment, add them to your O.S. hosts files. The following is an example of Window's *hosts* file:
```bash
# Development

127.0.0.1   momentum.local          #momentum
127.0.0.1   www.momentum.local      #momentum
127.0.0.1   m.momentum.local        #momentum
```

In the same fashion, this would be the configured `.env` file:
```bash
APP_DOMAIN=www.momentum.local
APP_SUBDOMAIN={subdomain}.momentum.local
SUBDOMAIN_MOBILE=m
```

Make the proper changes on your webserver to finish.

## Front End

### Commands

Run with hot loader ```npm run hot```

Note that the hot loader mode puts some files in the public dir that lets the browser know to use the script from the hot loader instead of assets from the public directory. Make sure to do a ```npm run dev``` before committing so other people dont end up with your hot configuration. 

Run do development build ```npm run dev```

Run do production build  ```npm run prod```

Run linter ```npm run eslint```

Run linter with fixer ```npm run eslint-fix```

There is a precommit hook that runs the linter with fixer.

*NOTE:* Setup will require oauth keyst to be generated and present in the project. Run the following command to generate ouath keys ```php artisan passport:install```

### Frameworks

Vue2 -> https://vuejs.org/

Quasar UI -> http://quasar-framework.org/components/

Vuelidate -> https://monterail.github.io/vuelidate/

Laracasts made a in-depth set of video on Vue2 dev here  https://laracasts.com/series/learn-vue-2-step-by-step/

## Cultures
Pull cultures from airtable 

```
npm run cultures
```

### Packages

* [Laravel Permissions](https://github.com/spatie/laravel-permission)
* [Active for Laravel](https://github.com/dwightwatson/active)
* [Laravel JS Localization](https://github.com/rmariuzzo/Laravel-JS-Localization)

## Importing Users
Users are imported from the Quest system into Momentum using CSVs. Four separate CSV files are added to a 
folder defined in `config/import.php` as `import_path`. Every 5 minutes the `php artisan import` command is run
which checks for the existence of new CSV files and attempts to import them.

The importer first starts by reading all of the CSV files in the import path and determining which type each CSV file is of:
* Organizations
* Users (Participants)
* Observers
* Report Scores

The CSVs are sorted by date, then grouped by type. The CSVs are then read in and entities are created in the order displayed
above. 

> **NOTE:** If at any point during the import there is a failure to validate (malformed or missing data) the entire
 import will be failed. When an import is failed, a record is created in the `import_logs` table with the file the import
 failed on, and the message of the failure.
 
Once all of the entities are created, the report PDFs and logos are moved into the proper directories, which are all configured in
`config/import.php`. Users are then sent their onboard emails which give them a link they can use to login. This link expires 
5 days after it was generated.

## PDF Generation
Action Plans, Journal Entries, and Pulse Survey Results can all be shared/downloaded as PDFs. Momentum generates PDFs by passing
a URL to a headless chrome browser running on the server. The URL that is given to the chrome instance needs to be accessible 
to the browser, which is why a PdfToken is generated for every request that the headless chrome browser will make.

The token is generated, and the token is passed in the URL as the `?pdf-token=` GET param, which allows the chrome instance to
authenticate **ONLY ONCE** for that specific route to generate the PDF. 

The `app/Services/PDFGeneratorService.php` class handles creating these PDFs. Here is an example of the entire process:
```php
        $action_plan = ActionPlan::currentReport()->findOrFail($id);

        $token = PdfToken::create([
            'route' => 'pdfs.action-plans.share-results',
        ]);

        $url = route('pdfs.action-plans.share-results', [
            'id'        => $id,
            'user_id'   => \Auth::user()->id,
            'pdf-token' => $token->token,
        ]);

        $name = 'pulse-survey-results-' . Carbon::now()->format('YmdHis');

        $file = PDFGeneratorService::documentToPDF($url, $name);

        return response()->download($file);
``` 

A node script opens up the chrome instance and passes the URL, and forces it to use the PDF generation command. This script
lives at `url-2-pdf/url-2-pdf.js`. The NPM dependencies must be installed in this packages root folder to function.

> **NOTE:** The `url-2-pdf.js` script cannot be executed from the `/vagrant` directory in the Vagrant machine, and instead
  must be moved to the `/home/vagrant` directory to be executed. The command that is executed is defined in `config/momentum.php` as `pdf_generator_command`. 

## Email Log
All emails that are sent through the system are logged in the `email_logs` table. If there was an error in sending the email the
stack trace in JSON format for the error will be logged along with the timestamp of when the error occurred.

# Server
The production server is a standard LEMP configuration using the provision script at `provision.sh`. The MySQL database is exposed
for the export script on the Quest side to confirm when the import was successful or failed, and is locked down to only the MRG
network using a Firewall.

There are mysqldumps which are taken twice daily (at 8am and 6 pm EST) and stored in `root/backups`.

[Tyler Raymond](mailto:traymond@axisbusiness.com) is the lead over at AXIS who manages the server.


## API
The application relies on an API for making it's POST calls, and also to receive some data. This API is built almost entirely
RESTfully, with a few exceptions. Users who are authenticated using Laravel's auth are authenticated to the API using Laravel Passport,
where a new token is created on each request using the `CreateFreshApiToken` middleware provided by Passport. Session data cannot
be used in any responses from the API, as the routes are not using the session.


## Data
Most of the application data is boostrapped by including it in the blade template. If you add a `data` array to the view, it
will automatically be included in the blade template from the layout as `window.data`, which can then be accessed by the Vue components.

Also included on every page are:
 * `window.user`: The user object with their organizations
 * `window.cultures`: All of the available cultures/languages for the application
 * `window.behaviors`: All of the behaviors for the application
 * `window.frontend_config`: Configurations for the frontend which are returned from `Momentum\Utilities\Config::getConfigForFrontend()`

## Copyright
(c) 2018 [MRG](https://www.mrg.com/), all rights reserved.