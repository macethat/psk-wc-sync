#!/bin/bash
mysql -h localhost -u u1910_kGdIBHVGwu -p'R5fuHEG28o' pskcloud -e "
SELECT Articulo, CodigoBarra, Existencia, Sucursal 
FROM inv 
WHERE CodigoBarra LIKE '%811445021105%';
" 2>/dev/null
echo "---"
# Also search just by article name or partial barcode
mysql -h localhost -u u1910_kGdIBHVGwu -p'R5fuHEG28o' pskcloud -e "
SELECT Articulo, CodigoBarra, Existencia, Sucursal 
FROM inv 
WHERE Articulo LIKE '%CREATINA%MOHIDRATADA%' OR Articulo LIKE '%CREATINA%MICRONIZADA%'
LIMIT 20;
" 2>/dev/null
