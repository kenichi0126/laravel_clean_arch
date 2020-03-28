# smart2

## usage

```bash
cd $HOME/workspace

git clone git@github.com:switch-m/smart2.git

cd smart2

sed \
  -e "s/\(SMART_WRITE_RDB_HOST=\)\(.*\)/\1db/g" \
  -e "s/\(SMART_WRITE_RDB_PASSWORD=\)\(.*\)/\1THr8Tz7sTU4p/g" \
  -e "s/\(SMART_READ_RDB_HOST=\)\(.*\)/\1db/g" \
  -e "s/\(SMART_READ_RDB_PASSWORD=\)\(.*\)/\1THr8Tz7sTU4p/g" \
  -e "s/\(SMART_DWH_HOST=\)\(.*\)/\1db/g" \
  -e "s/\(SMART_DWH_PASSWORD=\)\(.*\)/\1THr8Tz7sTU4p/g" \
  -e "s/\(REDIS_HOST=\)\(.*\)/\1cache/g" \
  .env.example > .env

# 下記の最新の情報をメンバーに聞いて.envを更新する
* SMART_WRITE_RDB_HOST
* SMART_READ_RDB_HOST
* SMART_DWH_HOST
* REDIS_HOST
* AWS_ACCESS_KEY_ID
* AWS_SECRET_ACCESS_KEY

docker-compose up -d

docker-compose exec app composer composer-install-all

docker-compose exec app php artisan key:generate

# access test
curl localhost/api/test
```

## tips

```bash
# 手動でファイルを追加してautoloaderに読み込ませたい時
docker-compose exec app composer dump-autoload

# appログ確認
docker-compose exec app tailf storage/logs/laravel.log

# sqlログ確認
docker-compose exec app tailf storage/logs/sql/$(date "+%Y-%m-%d")-log.sql

# repl
docker-compose exec app php artisan tinker

# db app user
docker-compose exec app psql

# db postgres user
docker-compose exec db psql

# redis
docker-compose exec cache redis-cli
```
