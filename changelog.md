# v0.2.0 (Pre-Release)
## Added
Nothing added in this release

## Updated
* Updated pulse surveys to display latest cycle first
* Updated custom action steps to be unique to their action plan

## Fixed
Nothing fixed in this release

# v0.1.12 (Pre-Release)
## Added
Nothing added in this release

## Updated
* Updated GA tracking code for page view analytics
* Updated cultures seeder to disable en_GB language

## Fixed
Nothing fixed in this release

# v0.1.11 (Pre-Release)
## Added
* Added unsupported browser warning alert for all versions of Safari below 11

## Updated
Nothing updated in this release

## Fixed
Nothing fixed in this release

# v0.1.10 (Pre-Release)
## Added
Nothing added in this release

## Updated
Nothing updated in this release

## Fixed
* Fixed issue where user could not select today as the due date for pulse survey

# v0.1.9 (Pre-Release)
## Added
* Added schedule for sending reminders

## Updated
* Updated review and reflect reminder date picker minimum date
* Updated all required validation to "use" requiredValidator to fix heap analytics issue
* Removed heap and hotjar analytics

## Fixed
* Fixed issue where deleting an action plan would not delete action plan reminders

# v0.1.8 (Pre-Release)
## Added
Nothing added in this release

## Updated
* Updated reminders date inputs to include the action step due date in the minimum date calculation

## Fixed
* Fixed login style for IE

# v0.1.7 (Pre-Release)
## Added
Nothing added in this release

## Updated
* Updated Action Step reminders to use todays date as a minimum if it's past the action plan start date
* Updated date selectors on Action Plans to include today's date

## Fixed
* Fixed issue where the loader would not stop when resending pulse surveys in IE
* Fixed issue where action steps would be marked completed when they were not complete

# v0.1.6 (Pre-Release)
## Added
Nothing added in this release

## Updated
Nothing updated in this release

## Fixed
* Fixed an issue where complete Pulse Surveys would be re-completed if the results were viewed
* Fixed issue with action steps seeder that would remove all but 1 "less" emphasis action step
* Fixed issue where login and pulse survey results page would display an exception

# v0.1.5 (Pre-Release)
## Added
Nothing added in this release

## Updated
* Updated asset build process to use cache busting 
* Removed unused JS dependencies
* Removed unused language strings
* Updated style on Pulse Survey Results PDF

## Fixed
* Fixed issue where a user could resend a completed pulse survey
* Fixed issue where the reminder start date could be set after the due date of the Action Plan

# v0.1.4 (Pre-Release)
## Added
Nothing added in this release

## Updated
* Removed create pulse survey button on dashboard when an action plan is complete
* Updated all redirect messages to use window.sessionStorage instead of query params

## Fixed
* Fixed issue where observer pulse surveys couldn't be deleted when the pulse survey was incomplete and the observer hasn't responded 
* Fixed IE style issues by defaulting content to stretch full height of screen, main content width is now bound
* Fixed issue during import where a user with the same pqp id would create a new report record instead of aborting
* Fixed double scrollbar issue on ie for ie multi select

# v0.1.3 (Pre-Release)
## Added
* Added scrollable containers on Journals greater than 520 characters

## Updated
* Updated delete observer modal with delete confirmation message
* Updated journal entry save to reset selected entries and observers when a user clicks the back button

## Fixed
* Fixed issue where the new pulse survey cycle action plan selector would not be disabled
* Fixed issue where viewing results on an open survey with all results completed would not complete the pulse survey
* Fixed issue where validation error would be displayed after discarding a previously empty journal entry
* Fixed issue where due dates on completed actions steps would not be set properly, preventing a user from continuing through Action Plan review
* Fixed IE style issues on observer survey view for language dropdown and set button

# v0.1.2 (Pre-Release)
## Added
* Added extra space below pdf title
* Added header to behavior step in share preview

## Updated
* Removed reminders instructions from share preview

## Fixed
* Removed logic that would automatically complete pulse survey is all the results were finished
* Removed restriction of hiding the "delete" button on recipients when only 3 exist on a survey
* Fixed ie bug where error message is wider than login block

# v0.1.1 (Pre-Release)
## Added
Nothing added in this release

## Updated
* Updated copy from airtable for Strategic behavior

## Fixed
Nothing fixed in this release

# v0.1.0 (Pre-Release)
## Added
Nothing added in this release

## Updated
* Updated Action Plan action steps selector to use action step description if there is no name
* Updated Action Plan action steps to not display a name title if there is no name
* Updated New Pulse Survey Cycle feature to disable the action plan selector
* Updated all copy from airtable
* Replaced placeholder pdf

## Fixed
* Fixed logic on Pulse Survey Create to allow a user to send Pulse Survey to one or more observers

# v0.0.6 (Pre-Release)
## Added
* Added the ability to send pulse surveys to additional observers

## Updated
* Updated build step of pulse surveys create to display proper validation messages
* Updated pulse survey share step button so that it is always enabled
* Removed hotjar analytics
* Updated statistics calculations to round before adding or subtracting the standard deviation from the mean
* Removed unneeded lodash functions requiring extra js to be loaded
* Updated validation message on Pulse Survey action plan selector

## Fixed
* Fixed Action Plan Share due dates not rendering in IE 
* Fixed issue where a user could delete open pulse survey responses after the pulse survey was complete
* Fixed issue where a user would be prompted to confirm if the pulse survey was complete 
* Fixed language dropdown to work properly in IE
* Fixed login box style in IE
* Fixed line spacing on Pulse Survey preview and observer survey page 

# v0.0.5 (Pre-Release)
## Added
* Added autofocus to first Goals textfield in Action Plan Create/Review
* Added maxlength to additional comments on pulse survey responses, Fixed word breaking on additional comments
* Added all analytics events
* Added "Forgot Email" functionality

## Updated
* Updated footer links to open in new window/tab
* Removed get help button from dashboard
* Updated login email validation error to only display error after 10s rather than 1s
* Improved positioning of dots on pulse survey results top chart
* Updated Share Action Plan to only display toast after redirect
* Updated Action Plan Complete to only display toast after redirect
* Updated field labels on Action Plan Complete similar to Action Plan Goals step
* Updated max width on action steps popover list on action plan create/review page
* Updated Pulse Survey results endpoint to "pulse-surveys/{id}/results" to fix active navigation item
* Updated pulse survey create with default message; Updated pulse survey create to prevent user input on form
* Updated footer to display at the bottom of the screen
* Updated dashboard pulse results y axis labels
* Updated style on survey results
* Updated all action steps copy
* Updated copy for 419 unknown status page

## Fixed
* Fixed issue where user could click "New Survey Cycle" on a completed Action Plan
* Fixed server validation for start/end dates on Action Plan Create/Review
* Fixed issue where you could only select 3 action steps when creating an Action Plan
* Fixed extra space from above sidebar nav
* Fixed issue where you could not set a due date on a newly created action step
* Fixed issue where creating an action step would clear out the "Pick Action Steps" selector
* Fixed issue where clicking cancel on action step create would not clear fields
* Fixed issue where user could resend individual open surveys if the cycle was closed
* Fixed issue where viewing results on an incomplete pulse survey would throw a 500 error
* Fixed IE style issues on Action Plan Share
* Fixed label on 'send to' select
* Fixed button alignment on 'share my action plan'
* Fixed management focus translation keys in seeder
* Fixed emphasis field label on Action Plan Create
* Fixed dates on Action Plan Share
* Fixed validation message of End Date field on Action Plan Create/Review
* Fixed issue where user could create a 4th Action Plan
* Fixed location of "Close Plan" button on Action Plan Complete
* Many copy updates

# v0.0.4 (Pre-Release)
## Added
* Added the ability for a user to edit and delete Journal Entries
* Added custom HTTP 419 page
* Added back button to first step of Pulse Survey create

## Updated
Nothing updated in this release.

## Fixed
* Fixed issue where a user could change the behavior or emphasis on an Action Plan if they had created a pulse survey for that Action Plan
* Fixed issue where organization logos would not display
* Fixed issue where validating start and end dates on Action Plan create/review would not return an error message to the front-end
* Fixed alignment of frequency date pickers on Action Plan create/review reminders step 

#v0.0.3 (Pre-Release)
## Added
* Added the ability to change behavior and emphasis when creating and reviewing/editing Action Plans
* Added the ability to change the goals when reviewing/editing an Action Plan
* Added "Save and Finish Later" button
* Added LEA Resource Guide PDFs and updated links
* Added the ability to delete an Action Plan that doesn't have any Pulse Surveys

## Updated
* Removed default dates on Action Plan create
* Changed color of all "Get Help" buttons to green
* Reduced the size of the Observer name in the Observer listing on the profile
* Updated Action Step Create to automatically select the Action Step the user just created
* Updated Action Step Create to automatically update the selectable Action Steps in Action Plan create/edit
* Changed color of "Get Help" button on dashboard to blue

## Fixed
* Fixed "Other" Observer type copy

# v0.0.2 (Pre-Release)
## Added
* Added the ability to create custom Action Steps during Action Plan creation
* Added confirmation dialog when viewing pulse survey results with open results 

## Updated
* Updated pulse survey results plot with cycle start date
* Updated width of app for consistency and to move sign out button inside of app container
* Updated magic tokens to have an expiration date
* Updated magic token database column to prevent logging in resetting the token
* Updated behavior dot plot to hide results with 0 (zero) values
* Updated dot plot labels to use translation keys
* Updated pulse survey creation to validate at least three observers are selected
* Updated action plan creation to validate the user has selected at least one action step
* Updated app footer to replace missing logo with organization name
* Updated PDF header with co-branding

## Fixed
* Fixed an issue where resending survey results would not increment the reminders sent counter
* Fixed an issue where the dashboard timeline progress graph was not displaying the correct progress 
* Fixed an issue where the dot plot would not display on the dashboard if a user had an open pulse survey for the currently selected action plan
* Fixed an issue where a pulse survey was automatically completed after only three pulse surveys submitted
* Fixed an issue where a user could create a pulse survey for a completed action plan
* Fixed an issue where a user could create a pulse survey for an action plan that already had an open pulse survey
* Implemented many consistency updates in dialogs, modals, action buttons, etc.

# v0.0.1 (Pre-Release)
Initial QA Release
