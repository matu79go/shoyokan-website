# Project: 昭葉館 (Shoyokan) Karate Dojo Website Reconstruction

## 開発の絶対原則
- 既存サイト **https://www.shoyokan.jp/** を正本とし、そのレイアウト、コンテンツ、機能をHTML/Tailwind CSSで忠実に（あるいは現代的に最適化して）再現する。
- Wix特有の不要なコードを排除し、フルスクラッチで軽量なシングルページ（1ページ構成）として構築する。

## 技術スタック
- **Frontend**: HTML5, Tailwind CSS (CDN版推奨), Alpine.js (インタラクション用)
- **Backend**: PHP (さくらインターネット用メール送信処理のみ)
- **Typography**: 日本語は和風・武道らしさを出すため 'Noto Serif JP' (Google Fonts) を使用。
- **Icons**: Lucide Icons または Font Awesome

## サイト構成（既存サイト準拠）
1.  **Hero**: メインビジュアル、道場名、キャッチコピー
2.  **About**: 昭葉館について（日本空手道松濤館流の説明）
3.  **Instructor**: 指導員紹介
4.  **Schedule/Fee**: 各支部の稽古日程、対象、入会金・月謝等の詳細
5.  **Access**: 各道場の地図（Google Maps埋め込み）と住所
6.  **SNS Integration**: Instagramフィード、Facebook連携（公式プラグイン等）
7.  **Contact**: サーバーサイドPHP (`send.php`) に接続するお問い合わせフォーム

## 実装要件
- **シングルページ**: ナビゲーションクリックで該当セクションへスムーズスクロール。
- **レスポンス**: モバイルファーストで設計。スマホでも稽古予定表が見やすいこと。
- **画像・地図**: 既存サイトのURLや配置を参考に、適切な `img` タグや `iframe` を配置する。
- **フォーム**: バリデーションをJavaScriptで行い、送信先は `send.php` とする（PHP本体はローカル動作不要）。

## Claudeへの具体的な指示例
1. 「https://www.shoyokan.jp/ の構成を分析し、Tailwind CSSを用いた1ページ構成のHTMLの骨組みを作成して。」
2. 「既存サイトにある各道場の稽古スケジュール表を、HTMLのテーブルまたはカード形式でレスポンシブに再現して。」
3. 「Instagram連携部分を、後でスクリプトを差し込みやすいようにダミー表示で実装して。」

---

## デプロイ手順

### さくらレンタルサーバーへのFTPデプロイ

**接続情報:**
- FTPサーバー: `shoyokan.sakura.ne.jp`（またはIP: `219.94.129.183`）
- FTPアカウント: `shoyokan`
- FTP初期フォルダ: `www`
- 本番URL: http://shoyokan.sakura.ne.jp

**WSLからのFTPアップロード（DNSが解決できない場合はIPを使用）:**
```bash
# 単一ファイルのアップロード
curl -T ファイル名 ftp://219.94.129.183/www/ --user shoyokan:パスワード

# フォルダ付きでアップロード（フォルダ自動作成）
curl --ftp-create-dirs -T ファイル名 ftp://219.94.129.183/www/images/ --user shoyokan:パスワード

# 複数ファイルを一括アップロード（例: imagesフォルダ）
for file in *.avif; do
  curl --ftp-create-dirs -T "$file" ftp://219.94.129.183/www/images/ --user shoyokan:パスワード -s && echo "$file uploaded"
done
```

**アップロード対象ファイル:**
```
www/
├── index.html
├── send.php
├── thanks.html
├── favicon.svg
└── images/
    └── (全画像ファイル)
```

**アップロード不要:**
- `.git/`, `.claude/`, `CLAUDE.md`
- `mock.pdf`, `mock_flow.png`, `mock_top.png`

### 全ファイル一括デプロイ（コピペ用）
```bash
# HTMLファイル
curl -T /mnt/c/workspace/syoyokan/index.html ftp://219.94.129.183/www/ --user shoyokan:パスワード -s && echo "index.html"
curl -T /mnt/c/workspace/syoyokan/send.php ftp://219.94.129.183/www/ --user shoyokan:パスワード -s && echo "send.php"
curl -T /mnt/c/workspace/syoyokan/thanks.html ftp://219.94.129.183/www/ --user shoyokan:パスワード -s && echo "thanks.html"
curl -T /mnt/c/workspace/syoyokan/favicon.svg ftp://219.94.129.183/www/ --user shoyokan:パスワード -s && echo "favicon.svg"

# 画像ファイル（imagesフォルダ）
for file in /mnt/c/workspace/syoyokan/images/*.avif /mnt/c/workspace/syoyokan/images/*.png; do
  curl --ftp-create-dirs -T "$file" ftp://219.94.129.183/www/images/ --user shoyokan:パスワード -s && echo "$(basename $file)"
done
```

---

## メール設定（send.php）

**設定箇所（send.php 冒頭）:**
```php
$config = [
    'to_email' => 'info@shoyokan.jp',              // 道場の受信用メールアドレス
    'from_email' => 'noreply@shoyokan.jp',         // 送信元（さくらで作成したアドレス）
    'from_name' => '藤枝将陽館 お問い合わせフォーム',
    'subject_prefix' => '【藤枝将陽館】',
];
```

**注意:**
- 送信元メールアドレスは、さくらレンタルサーバーのドメインまたは独自ドメインで作成したものを使用
- 外部ドメイン（gmail.com, hotmail.co.jp等）を送信元にするとSPFチェックで弾かれる可能性あり
- 本番では `@shoyokan.jp` のメールアドレス推奨