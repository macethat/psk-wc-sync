#!/bin/bash
echo "=== tables ==="
mysql -h localhost -u u1910_kGdIBHVGwu -p'R5fuHEG28o' pskcloud -e "SHOW TABLES;" 2>&1 | head -20
echo "=== inv columns ==="
mysql -h localhost -u u1910_kGdIBHVGwu -p'R5fuHEG28o' pskcloud -e "DESCRIBE inv;" 2>&1 | head -20
