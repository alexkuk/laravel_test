## Credentials
To access frontend list of transactions use the following credentials:
E-mail: cf@currencyfair.com
Password: cfap1user

## Test
I decided to concentrate on the first part - message consumption. That's why the frontend part is a simple grid with pagination.

## Choosing Tools
As an application should process a large number of messages per second, performance is the most critical part.
I decided to go with the following stack: Nginx + HHVM + Laravel + Elasticsearch. Hosted it on the AWS EC2 micro-instance for free :)
Laravel is chosen because it's comparatively fast, has 100% HHVM support, simple, and may be easily extended by some standalone packages (e.g. from Symfony).
Elasticsearch is a good tool for big data consumption and processing. And I have worked with it pretty much before.

## Approach
I used TDD as the main approach to this application development. The tests describe how API should respond on different messages.
API is a simple REST API with http authentication and one create method. Next step to secure it is using SSL, but I don't use it for the test.
Laravel starts a session everytime that is not needed for the API. Trying to disable this leads to errors, it seems Laravel 5 has an issue here, so I leaved it enabled, don't be surprised why session id appears in API answers.