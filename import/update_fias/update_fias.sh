#!/bin/bash
if [ -f /tmp/3_4.csv ]; then
	rm /tmp/3_4.csv
fi
if [ -f /tmp/6.csv ]; then
	rm /tmp/6.csv
fi
if [ -f /tmp/7.csv ]; then
	rm /tmp/7.csv
fi
#Выгружаем новые данные из таблицы ФИАС
mysql -u root -p -D osago -e "select * from (select t1.*, t2.aoid aoid2 from d_fias_addrobj t1 left join d_fias_addrobj_3_4 t2 on t1.aoid = t2.aoid where t1.aolevel in(3,4)) as t3 where aoid2 is null INTO OUTFILE '/tmp/3_4.csv'"
mysql -u root -p -D osago -e "select * from (select t1.*, t2.aoid aoid2 from d_fias_addrobj t1 left join d_fias_addrobj_6 t2 on t1.aoid = t2.aoid where t1.aolevel in(6)) as t3 where aoid2 is null INTO OUTFILE '/tmp/6.csv'"
mysql -u root -p -D osago -e "select * from (select t1.*, t2.aoid aoid2 from d_fias_addrobj t1 left join d_fias_addrobj_7 t2 on t1.aoid = t2.aoid where t1.aolevel in(7)) as t3 where aoid2 is null INTO OUTFILE '/tmp/7.csv'"
#Загружаем новые данные в таблицы ОСАГО
mysql -u root -p -D osago -e "LOAD DATA INFILE '/tmp/3_4.csv' INTO TABLE d_fias_addrobj_3_4"
mysql -u root -p -D osago -e "LOAD DATA INFILE '/tmp/6.csv' INTO TABLE d_fias_addrobj_6"
mysql -u root -p -D osago -e "LOAD DATA INFILE '/tmp/7.csv' INTO TABLE d_fias_addrobj_7"