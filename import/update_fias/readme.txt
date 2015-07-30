Обновление ФИАС-КЛАДР
1 Качаем фал с обновлениями http://basicdata.ru/download/fias/ таблицу ADDROBJ
2 Очищаем существующую таблицу d_fias_addrobj
3 Импортируем данные из скачанного файла в существующую таблицу 
mysql -u root -p osago < fias_addrobj_data.sql
4 Удаляем все неактуальные записи 
delete from d_fias_addrobj where actstatus = 0
5 Запускаем файл update_fias.sh
6 Готово
