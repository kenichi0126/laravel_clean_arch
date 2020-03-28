# deploy development

### 初回起動の時

```bash
# dev01-smart.switch-m.bizの部分、セットしたいドメイン名に変更し実行
cd /home/app/workspace/dev01-smart.switch-m.biz

git clone git@github.com:switch-m/smart2.git

cd smart2

# それぞれ数値の部分などを修正
tee .env << 'EOF'
COMPOSE_PROJECT_NAME=dev01-smart
CACHE_IMAGE_URL=406795679565.dkr.ecr.ap-northeast-1.amazonaws.com/smart2:master
DOTENV_PMS_NAME=/Manual/Dev01Smart/dev/.env
PORT=60015
EOF

$(aws ecr get-login --no-include-email --region ap-northeast-1 --registry-ids 406795679565)

docker-compose --file=docker-compose-devserver.yml up -d

docker-compose --file=docker-compose-devserver.yml exec app composer composer-install-all
```


### 再起動の時

```bash
# dev01-smart.switch-m.bizの部分、セットしたいドメイン名に変更し実行
cd /home/app/workspace/dev01-smart.switch-m.biz/smart2

git remote update

# masterの部分、セットしたいブランチ名に変更し実行
git checkout master

git pull

# masterの部分、セットしたいブランチ名に変更し実行（docker tagに / は使えないため - に変換して記入）
sed -i -e "s/\(CACHE_IMAGE_URL=.*:\)\(.*\)/\1master/g" .env

$(aws ecr get-login --no-include-email --region ap-northeast-1 --registry-ids 406795679565)

docker-compose --file=docker-compose-devserver.yml stop
docker-compose --file=docker-compose-devserver.yml rm -f
docker-compose --file=docker-compose-devserver.yml pull   
docker-compose --file=docker-compose-devserver.yml up -d

docker-compose --file=docker-compose-devserver.yml exec app composer composer-install-all
```


### テストの実行方法

```bash
# dev01-smart.switch-m.bizの部分、セットしたいドメイン名に変更し実行
cd /home/app/workspace/dev01-smart.switch-m.biz/smart2

docker-compose --file=docker-compose-devserver.yml exec app composer unit:app
docker-compose --file=docker-compose-devserver.yml exec app composer unit:components
docker-compose --file=docker-compose-devserver.yml exec app composer unit:queries
```


### 動作確認
https://dev01-smart.switch-m.biz/api/test


### その他
```bash
# ログの確認方法（-fでログ監視が可能）
docker-compose --file=docker-compose-devserver.yml logs app
```
