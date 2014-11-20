<?php
$blank_orig = "blank/statement.docx";
$name = md5(date("F j, Y, g:i:s "));
copy($blank_orig, "blank/tmp/$name.docx");
$blank = "blank/tmp/$name.docx";
$params = array(
    'NAME' => 'Хусаинов Артур Амурович',
    'DATE_BIRTH' => '06.04.1983'
);
$zip = new ZipArchive();
if (!$zip->open($blank)) {
    exit('Не удалось открыть бланк заявления');
}
$data_xml = $zip->getFromName("word/document.xml");
//Заменяем все найденные переменные в файле на значения
$data_xml = str_replace(array_keys($params), array_values($params), $data_xml);
$zip->deleteName('word/document.xml');
$zip->addFromString('word/document.xml', $data_xml);
$zip->close();
//ОТдаём файл браузеру
// заставляем браузер показать окно сохранения файла
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=Заявление.docx');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($blank));
// читаем файл и отправляем его пользователю
readfile($blank);
unlink($blank);
?>
