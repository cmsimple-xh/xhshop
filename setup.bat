@echo off

xcopy demo\content ..\..\content /e
mkdir ..\..\content\xhshop\tmp_orders
xcopy demo\userfiles ..\..\userfiles /e
