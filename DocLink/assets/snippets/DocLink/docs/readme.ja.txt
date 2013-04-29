Title      : DocLink
Category   : Snippet
Author     : Phize
Author URI : http://phize.net
License    : GNU General Public License(http://www.gnu.org/licenses/gpl.html)
Version    : 1.0.2
Last Update: 2009-10-21



0.目次
------

1.概要
2.インストール
3.使い方
4.パラメーター
5.更新履歴



1.概要
------

DocLinkは、前後のドキュメントへのリンクを表示するスニペットです。
さらに、最初のドキュメント、最後のドキュメント、ホームページ、親ドキュメントへのリンクも表示できます。

出力するXHTMLはテンプレートによって完全にカスタマイズが可能です。
あらかじめ同梱されているテンプレートとスタイルを利用することで、
簡単に見栄えのするナビゲーションを作成することもできます。

また、link要素を出力するテンプレートを利用することによって、
検索エンジンなどに対しても優しいサイトになります。



2.インストール
--------------

1.doclink/フォルダを /assets/snippets/ にコピー。

2.「DocLink」という名前のスニペットを新規作成。

3.doclink.snippet.tpl.phpの内容を「スニペットコード」にコピー&ペースト。

4.スニペットを保存。



3.使い方
--------

標準では、前後のドキュメントへのシンプルなナビゲーションリンクを表示します。
表示したいドキュメントの中でスニペットをコールします。

  [[DocLink]]


テンプレート「full」のナビゲーションをスタイル「modx」で出力する場合:
  [[DocLink? &template=`full` &style=`modx`]]


ナビゲーション用のlink要素を出力する場合:
  <head>
    ...省略...
    [[DocLink? &template=`link` &style=`none`]]
  </head>



4.パラメーター
--------------

パラメーターを指定することで見栄えや機能を変えることができます。
詳しくは、同梱のDocLink.ja.pdfを参照してください。



5.更新履歴
--------------

2009-10-21	doclink.tpl.phpをdoclink.snippet.tpl.phpに変更。

2009-06-03	PHP4でエラーが発生する問題を修正。
			デフォルトテンプレート内のタグの誤記を修正。
