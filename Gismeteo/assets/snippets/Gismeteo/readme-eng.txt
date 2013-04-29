==Installation

Make new snippet, called Gismeteo from file MODx-Gismeteo.php

Put "gismeteo" folder to
/assets/snippets/
on the server

==Using

Simple:
[[Gismeteo]]
This shows informer in theme Silk for Moscow.

To get info for another region - get it id on
http://informer.gismeteo.ru/getcode/xml.php
Select needed region.
Your region - part of adress of xml, placed on this page.
http://informer.gismeteo.ru/xml/!!!!!!this-->27612<--this!!!!!!!_1.xml

Full:
[[Gismeteo? &theme=`Tango` &region=`99970` &lang=`ru` &tpl=`my-weather-tpl`]]
Use theme Tango for Podolsk, use russian translate and try to use chunk 'my-weather-tpl' as template.

=Parameters

region — code of region in Gismeteo database.

theme - Case-sensitive name of theme. There is 4 themes in this destributive. Silk, Tango, FarmFresh and Text. If theme is not selected - default theme is Silk.

tpl — name of chunk-template. If no template — uses default template. Not all themes supports templates. For example, Silk is not. But you can change theme or manipulate with it with CSS.

Placeholders are individual for each theme. Check documentation in
/assets/snippets/gismeteo/THEME NAME/theme.php

lang - language of output. Default is 'ru' because gismeteo.ru is russian service.

==Info

Snippet uses cache of XML. Data on gismeteo refreshes 4 times a day in fixed times. Snippet got this refreshes and not go to data more often. Cache is stored in /assets/cache in files like gismeteo870xml.

All files shoud be saved in utf-8. If your site is not in utf-8 - don't worry - shippet recode data for you.

=========================================
Author: Smirnov Sergey aka Ifman http://ifman.ru
Snippets houmpage: http://dayte2.com/modx-rss-import