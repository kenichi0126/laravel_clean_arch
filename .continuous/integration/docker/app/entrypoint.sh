#!/bin/sh

set -e

echo "entrypoint!!!!!!!!!!!"

php -v
php artisan -V

if [ "${APP_ENV}" = "development" ]; then
  ROLE_NAME=$(curl -s --connect-timeout 3 http://169.254.169.254/latest/meta-data/iam/security-credentials/)
  CREDENTIALS=$(curl -s http://169.254.169.254/latest/meta-data/iam/security-credentials/${ROLE_NAME}/)

  AWS_ACCESS_KEY_ID=$(echo $CREDENTIALS | jq -r .AccessKeyId)
  AWS_SECRET_ACCESS_KEY=$(echo $CREDENTIALS | jq -r .SecretAccessKey)
  AWS_SESSION_TOKEN=$(echo $CREDENTIALS | jq -r .Token)
fi


if [ -n "${DOTENV_PMS_NAME}" ]; then
  aws ssm get-parameters --name "${DOTENV_PMS_NAME}" --query "Parameters[0].Value" --output text > .env
else
  {
    echo "APP_ENV=production"
    echo "APP_KEY=base64:AcwogRo7cXUpfHBVoyWQCIo9rik5OpB8OwxgNrqz6iw="
    echo "APP_DEBUG=true"
    echo "LOG_CHANNEL=stderr"
  } > .env
fi

chown www-data:www-data .env

su www-data -s /bin/bash -c "php artisan config:cache"
su www-data -s /bin/bash -c "php artisan route:cache"
#su www-data -s /bin/bash -c "php artisan view:cache"





# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- apache2-foreground "$@"
fi

exec "$@"
