#!/bin/bash
mysql -h localhost -u u1910_kGdIBHVGwu -p'R5fuHEG28o' pskcloud -e "
SELECT Articulo, CodigoBarra, Existencia, Sucursal 
FROM inv 
WHERE CodigoBarra LIKE '%811445021105%' 
   OR Articulo IN (SELECT Articulo FROM inv WHERE CodigoBarra LIKE '%811445021105%')
LIMIT 20;
" 2>/dev/null
