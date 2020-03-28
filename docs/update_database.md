# update database

1. リモート開発DBに変更を入れる

2. 構造のみのダンプを取得

```bash
pg_dump -hdev-prsp-master-003.cnpfdiiu8d8e.ap-northeast-1.rds.amazonaws.com -Uswitch -dsmart \
--schema-only \
--file=.docker/db/sql/001_smart.sql
```

3. docker-compose up -d 以外でデータリストアするときは
```bash

# オーナー修正する場合
sed -i '' -e "s/Owner: smart/Owner: switch/g" -e "s/TO smart;/TO switch;/g" .docker/db/sql/001_smart.sql

psql -hxx -Uswitch -dsmart < .docker/db/sql/001_smart.sql
psql -xxx -Uswitch -dsmart

INSERT INTO system_informations VALUES ('smart2-api', 0, NOW());
INSERT INTO system_informations VALUES ('canopus-api', 0, NOW());
```
