## usage

```bash
cd $HOME/workspace

git clone git@github.com:switch-m/laravel_clean_arch.git

cd laravel_clean_arch

sed \
  -e "s/\(WRITE_RDB_HOST=\)\(.*\)/\1db/g" \
  -e "s/\(WRITE_RDB_PASSWORD=\)\(.*\)/\1THr8Tz7sTU4p/g" \
  -e "s/\(READ_RDB_HOST=\)\(.*\)/\1db/g" \
  -e "s/\(READ_RDB_PASSWORD=\)\(.*\)/\1THr8Tz7sTU4p/g" \
  -e "s/\(DWH_HOST=\)\(.*\)/\1db/g" \
  -e "s/\(DWH_PASSWORD=\)\(.*\)/\1THr8Tz7sTU4p/g" \
  -e "s/\(REDIS_HOST=\)\(.*\)/\1cache/g" \
  .env.example > .env

# 下記の最新の情報をメンバーに聞いて.envを更新する
* WRITE_RDB_HOST
* READ_RDB_HOST
* DWH_HOST
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
