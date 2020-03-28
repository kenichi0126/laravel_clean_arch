# deploy production

```bash
ssh prod-common-bastion-001.prsp.internal
# 複数台あれば複数台分行う
ssh -A prod-smart-api-001

cd /home/app/workspace

git clone git@github.com:switch-m/smart2.git

cd smart2

sed \
  -e "s/\(APP_ENV=\)\(.*\)/\1production/g" \
  -e "s/\(APP_DEBUG=\)\(.*\)/\1false/g" \
  -e "s/\(APP_URL=\)\(.*\)/\1https:\/\/smart2-api.switch-m.com/g" \
  -e "s/\(LOG_CHANNEL=\)\(.*\)/\1daily/g" \
  -e "s/\(SMART_WRITE_RDB_HOST=\)\(.*\)/\1prod-smart-cluster.cluster-copiyaqd3imr.ap-northeast-1.rds.amazonaws.com/g" \
  -e "s/\(SMART_WRITE_RDB_PORT=\)\(.*\)/\15432/g" \
  -e "s/\(SMART_WRITE_RDB_DATABASE=\)\(.*\)/\1smart/g" \
  -e "s/\(SMART_WRITE_RDB_USERNAME=\)\(.*\)/\1switch/g" \
  -e "s/\(SMART_WRITE_RDB_PASSWORD=\)\(.*\)/\1THr8Tz7sTU4p/g" \
  -e "s/\(SMART_READ_RDB_HOST=\)\(.*\)/\1prod-smart-cluster.cluster-ro-copiyaqd3imr.ap-northeast-1.rds.amazonaws.com/g" \
  -e "s/\(SMART_READ_RDB_PORT=\)\(.*\)/\15432/g" \
  -e "s/\(SMART_READ_RDB_DATABASE=\)\(.*\)/\1smart/g" \
  -e "s/\(SMART_READ_RDB_USERNAME=\)\(.*\)/\1switch/g" \
  -e "s/\(SMART_READ_RDB_PASSWORD=\)\(.*\)/\1THr8Tz7sTU4p/g" \
  -e "s/\(SMART_DWH_HOST=\)\(.*\)/\1prod-smart.chbswrjeznvw.ap-northeast-1.redshift.amazonaws.com/g" \
  -e "s/\(SMART_DWH_PORT=\)\(.*\)/\15439/g" \
  -e "s/\(SMART_DWH_DATABASE=\)\(.*\)/\1smart/g" \
  -e "s/\(SMART_DWH_USERNAME=\)\(.*\)/\1switch/g" \
  -e "s/\(SMART_DWH_PASSWORD=\)\(.*\)/\1THr8Tz7sTU4p/g" \
  -e "s/\(CACHE_DRIVER=\)\(.*\)/\1redis/g" \
  -e "s/\(SESSION_DRIVER=\)\(.*\)/\1redis/g" \
  -e "s/\(REDIS_HOST=\)\(.*\)/\1prod-smart-api.35lv78.0001.apne1.cache.amazonaws.com/g" \
  -e "s/\(SESSION_SECURE_COOKIE=\)\(.*\)/\1true/g" \
  .env.example > .env

composer install --no-dev --no-scripts --optimize-autoloader
php artisan config:cache
php artisan route:cache

# ログインがクリアされないか確認すること
php artisan key:generate

setfacl -m "default:g:users:rwx" storage/logs
setfacl -m "g:users:rwx" storage/logs

# check
curl smart2-api.switch-m.biz/api/test
```
