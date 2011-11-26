<?php
/* ====================
Copyright (c) 2009-2010, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.

[BEGIN_COT_EXT]
Code=showcase
Name=Site Showcase
Description=Website gallery
Version=1.1
Date=2011-10-07
Author=Trustmaster
Copyright=(c) Vladimir Sibirov, 2009-2010
Notes=
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=RW
Lock_members=12345
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
access_key=01:string:::ShrinkTheWeb access key
secret_key=02:string:::ShrinkTheWeb secret key
cache_days=03:select:1,2,3,7,14,30:7:Days to keep thumbnails cache
per_row=04:select:1,2,3,4,5,6:3:Items displayed per row
per_page=05:select:1,2,3,4,5,6,8,9,10,12,15,16,18,20,24,25,30:9:Items displayed on page
length=06:string::40:Max title length
[END_COT_EXT_CONFIG]
==================== */
defined('COT_CODE') or die('Wrong URL.');

?>
