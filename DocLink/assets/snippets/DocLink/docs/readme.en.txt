Title      : DocLink
Category   : Snippet
Author     : Phize
Author URI : http://phize.net
License    : GNU General Public License(http://www.gnu.org/licenses/gpl.html)
Version    : 1.0.2
Last Update: 2009-10-21



0.Index
-------

1.Introduction
2.Install
3.How to use
4.Parameters
5.History



1.Introduction
--------------

DocLink is the snippet which shows the links to previous/next document.
In addition, it shows first/last/home/parent document.

The templates are completely customizable.
You could also create pretty navigations easily by using built-in templates & styles.

And your Web site would be search engine friendly by using built-in 'link' template.



2.Install
---------

1.Copy doclink/ folder into /assets/snippets/

2.Create new snippet called 'DocLink'.

3.Copy & Paste the content of doclink.snippet.tpl.php into 'Snippet code'.

4.Save the snippet.



3.How to use
------------

DocLink shows the navigation which is the links to previous/next document by the default.
You need call the snippet in the document which you would like to show the navigation.

  [[DocLink]]


When you would like to use 'full' template & 'modx' style:
  [[DocLink? &template=`full` &style=`modx`]]


When you would like link elements for navigation:
  <head>
    ...
    [[DocLink? &template=`link` &style=`none`]]
  </head>



4.Parameters
------------

You could change the presentation/function of navigatoin by specifying parameters.
See DocLink.en.pdf for further details, please.



5.History
--------------

2009-10-21	Changed doclink.tpl.php to doclink.snippet.tpl.php.

2009-06-03	Fixed a fatal error on PHP4.
			Fixed typographical error in default templates.
