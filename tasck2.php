<?php
//создание файла
$file = fopen("file1.txt", "w") or die("Ошибка создания файла!");
//Вводим данные в файл
file_put_contents("file1.txt", "1. William Smith, 1990, 2344455666677\n", FILE_APPEND);
file_put_contents("file1.txt", "2. John Doe, 1988, 4445556666787\n", FILE_APPEND);
file_put_contents("file1.txt", "3. Michael Brown, 1991, 7748956996777\n", FILE_APPEND);
file_put_contents("file1.txt", "4. David Johnson, 1987, 5556667779999\n", FILE_APPEND);
file_put_contents("file1.txt", "5. Robert Jones, 1992, 99933456678888\n", FILE_APPEND);
//Закрываем файл
fclose($file);
//Открываем файл для добавления данных
$file = fopen("file1.txt", "a") or die("Ошибка открытия для добавления
данных!");
if (!$file) {
 echo("Не был найден файл для добавления данных!");
} else {
 // Добавьте в файл с помощью функции fwrite() еще 3 записи
 file_put_contents("file1.txt", "6. Christopher Lee, 1995, 2223334445556\n", FILE_APPEND);
 file_put_contents("file1.txt", "7. Emily Davis, 1998, 6667778889991\n", FILE_APPEND);
 file_put_contents("file1.txt", "8. Jessica Martinez, 1993, 7778889990002\n", FILE_APPEND );
}
fclose($file);
//Открываем файл для чтения из него
$file = fopen("file1.txt", "r") or die("Ошибка открытия файла для чтения!");
if (!$file) {
 echo("Не был найден файл для чтения данных!");
} else { ?>
 <div>Данные из файла: </div>
 <?php
 while (!feof($file)) {
 echo fgets($file); ?>
 <br/>
 <?php
 }
 fclose($file);
}
