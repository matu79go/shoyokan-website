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