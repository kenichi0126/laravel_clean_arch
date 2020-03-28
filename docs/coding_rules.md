# Coding rules

## Coding Style

### standard

laravel同様に、PSR-2 & PSR-4に準拠。  

- [PSR-1](https://qiita.com/katsukii/items/e68183f14407722de9cc#psr-1)
- [PSR-2](https://qiita.com/katsukii/items/e68183f14407722de9cc#psr-2)
- [PSR-4](https://qiita.com/inouet/items/0208237629496070bbd4)

### override

未確定。
変数名、１行の長さなどstarndard。

### auto formatter

コードの自動整形を導入しております。

レビュー前に下記を実行してからレビュー依頼をしてください。

```bash
# 全てのファイルを自動整形(レビュー前に必ず実行)
docker-compose exec app composer formatter
```

その他、コマンド実行例。

```bash
# パスを指定で自動整形
docker-compose exec app composer formatter packages/Smart2/src/Application/

# 整形されるファイルの一覧を確認したい時
docker-compose exec app composer formatter -- --dry-run

# 整形されるファイルの差分を確認したい時
docker-compose exec app composer formatter -- --dry-run --diff --diff-format udiff

# ルールの説明
docker-compose exec app ./vendor/bin/php-cs-fixer describe single_import_per_statement

# ブラウザでルールの確認
https://mlocati.github.io/php-cs-fixer-configurator/
```

### comment

現状、自動ドキュメント生成を行わず、メソッドの引数や返り値を定義するクラスやメソッドのヘッダコメントは不要。  
メソッド内のコメントは自由だが、内容に間違えがないようにすること。  
TODOの記入については名前を記入することを書く際にはgrepがしやすいように下記フォーマットで。  

```php
// TODO - kinoshita: コメント
```


## Validation

https://readouble.com/laravel/5.5/ja/validation.html  

### read

プログラムがバグらない程度の緩めなルールで。  

- alpha (アルファベッド)
- alpha_dash (アルファベッド記号)
- alpha_num (アルファベッド数字)
- array (配列)
- bool (論理)
- date (日付) または date_format (日付形式)
- email (メールアドレス)
- nullable (NULL可能)
- numeric (数値)
- present (存在)
- required (必須)
- string (文字列)

### write

思いつく限り入念に行う。  


## Test

### 注意点

- ./vendor/bin/phpunitなどで直接テストを実行しないでください。  
    phpunitの設定ファイルでDBの接続先を変えたりしているため。  
- テストクラスは必ずtests/TestCaseのクラスを継承してください。  
    Illuminate\Foundation\Testing\TestCaseやPHPUnit\Framework\TestCaseの直接継承は禁止です。  

2点を守らないと共通の開発DBのデータが消えたりする可能性があります。  

**Redshiftに関するテストはまだできません。**

### feature

laravelのFeatureテスト(HTTPテスト)で結合テストのことで、  
テストを実行すると内部でlaravelアプリケーションを起動します。  
そのアプリケーションにリクエストを送ってそのレスポンスをアサートできます。  

**実装は義務です**。  
コードレビューでテスト内容の確認を行います。  

テストの実装のポイントは下記。  
レスポンスコードをアサートするテストになります。  

- GETリクエストの正常系として200のテストが実装されていること
- GETリクエストの異常系としてデータが見つからなかった場合の404のテストが実装されていること
- POSTリクエストの正常系としてread apiであれば200、write apiであれば204のテストが実装されていること
- GET,POST共にURLにIDなどが入る際にデータが見つからなかった時の、異常系として404のテストが実装されていること
- GET,POST共にバリデーションがある時の、異常系として422のテストが実装されていること(リクエストクラスに関連)
- 認証付きのリソースに401のテストが実装されていること
- コントローラの各アクションの条件分岐が網羅していること(coverageで確認する)

まず、下記テストを参考にしてください。  
tests/Feature/ExampleQuestionTest.php  

その他、権限周りのテストは後日追記する。

テストの実行などは下記。  

```bash
# テストコードの場所
ls tests/Feature

# テストの一覧
docker-compose exec app composer test:feature -- --list-tests

# 特定のテストメソッドを実行
docker-compose exec app composer test:feature -- --filter=index200

# 特定のテストファイルを実行
docker-compose exec app composer test:feature tests/Feature/ExampleQuestionTest.php

# 全てのテストを実行
docker-compose exec app composer test:feature

# 最後に実行したテストのcoverageを確認
http://localhost/coverage/
```

### unit

**実装は任意です**。  
テストを作りたい方は実装してください。  

```bash
# テストコードの場所
ls tests/Feature

# テストの一覧
docker-compose exec app composer test:unit -- --list-tests
 
# 特定のテストメソッドを実行
docker-compose exec app composer test:unit -- --filter=testBasicTest

# 特定のテストファイルを実行
docker-compose exec app composer test:unit tests/Unit/ExampleTest.php

# 全てのテストを実行
docker-compose exec app composer test:unit

# 最後に実行したテストのcoverageを確認
http://localhost/coverage/
```
