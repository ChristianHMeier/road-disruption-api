## Road Disruption API Console Program

This repo is of a simple exercise to consult the Road Disruption REST API provided by Transport for London through console commands. Results are cached for an hour and it is possible to filter them through optional parameters.

By default the tool prints a JSON representation of the results. In case of an empty response or an API error, it will print a relevant error message.

## How to Use

After cloning the repo in a device that has PHP 8 installed, you only need to run the following command to receive the unfiltered results:
````
$ php artisan app:disruption-call
````

It is also possible to narrow down the results using the '--category' or '--endsBefore' parameters. Both can also be used simultaneously:
````
$ php artisan app:disruption-call --category=Works --endsBefore=2023-10-17
````

**Important:** In case the category you search has more than one word, the --category value must be wrapped in quotation marks. Meanwhile --endsBefore must always be written in YYYY-MM-DD format, the program will automatically append the time so the limit is at 23:59:59 of the specified date.

Examples of a valid multi-word category:
````
$ php artisan app:disruption-call --category='Planned events'
````
````
$ php artisan app:disruption-call --category="Planned events"
````

## Code Base and Relevant Files

This exercise was written in PHP using the Laravel 10 framework. The relevant files for the exercise are:

- app/Console/Commands/DisruptionCall.php
- app/Models/Disruption.php
- app/Models/ApiCall.php

Separating the exercise code in these three files allows to reuse and expand them in case other functionalities are required, be them from the Road Disruption REST API or from an entirely different API.

## Licenses

Dual licensed under the [MIT license](https://opensource.org/licenses/MIT) and [JSON license](https://www.json.org/license.html).
