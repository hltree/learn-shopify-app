# Apache

Using php with apache.

## Usage

1. data内にコンテンツを配置する

2. .envファイルをコピーする
```
コピー先で設定は自由に変えて頂いて構いません。
$ cp .env-sample .env
```

3. dockerを起動する
```
起動
$ docker-compose up -d
（初回はビルドが入るため、時間がかかります）

終了
$ docker-compose down
```

## Settings

### ・Portを変更する
1. [docker-compose.yml](./docker-compose.yml)を開く
2. 変更したいコンテナのportsを書き換える
3. （docker動作中なら）コンテナを再起動する

### ・PHP My Adminのインポートサイズを変更する
1. [upload.ini](./docker/phpmyadmin/upload.ini)を開く
2. 設定値を変更する
```
例

upload_max_filesize=128M
```

## Available by default

- PHP
- MySQL
- PHP My Admin
- MailHog