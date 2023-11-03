https://developer.atlassian.com/cloud/jira/platform/rest/v3/api-group-users/#api-rest-api-3-user-get

sudo chown $USER:$GROUP

model factory controller requests

php artisan make:model NAME -m -c -f --requests
php artisan make:model NAME -mfcR
php artisan make:command SendEmails
php artisan make:migration create_flights_table

php artisan import-issues --date 2023-10-13
php artisan auto-import --date 2023-10-12

php artisan schedule:list


Dev Mode
docker-compose -f docker-compose-dev.yml up
docker-compose -f docker-compose-dev.yml down

docker build -t metabase_php -f Dockerfile .